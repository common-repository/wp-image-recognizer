/**
 * admin.js
 * Version:     1.0b
 * Author:      Green Sheep
 * Created:     May 31, 2018
 * Modified:    Sep 19, 2018
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
(function ($) {
  var loadedModel;

  if (typeof wp.Uploader === 'function') {
    // Add processes for Media Library 'grid' mode.
    $.extend( wp.Uploader.prototype, {
      init : function() {
        this.uploader.bind('BeforeUpload', function(up, file) {
          // Start image recognition before upload starts.
          onBeforeUpload(file.attachment.cid, file);
        });
      },
      success : function(fileAttachment) {
        // Store the information of the attachment file.
        // console.log( fileAttachment );
        imageCaption.setAttachment(fileAttachment.cid, fileAttachment);
      }
    });
  } else {
    $(document).ready(function($){
      if(typeof uploader !== 'undefined') {
        // Add processes for Media Library 'list' mode.
        uploader.bind('BeforeUpload', function(up, file) {
          // Start image recognition before upload starts.
          onBeforeUpload(file.id, file);
        });

        uploader.bind('FileUploaded', function(up, file, response) {
          // Store the post id of the uploaded image file.
          // console.log(response);
          imageCaption.setPostId(file.id, response.response);
        });
      }
    });
  }

  // Start image recognition.
  function onBeforeUpload(fileId, file) {
    // console.log('BeforeUpload file=%o', file);
    imageCaption.init(fileId);

    var modelLoad = !loadedModel ? mobilenet.load() : loadedModel;
    var imageLoad = new Promise(function (resolve, reject) {
      var image = new Image();
      image.onload = function() {
        getImageCanvas(image, resolve);
      };
      image.src = window.URL.createObjectURL(file.getNative());
    });

    // Loading the model and the image.
    Promise.all([modelLoad, imageLoad])
    .then(function(results) {
      var model = results[0];
      var canvas = results[1];
      if(!loadedModel) {
        // For reusing.
        loadedModel = model;
      }
      // Classify the image.
      return model.classify(canvas);
    }).then(function(predictions) {
      // console.log(predictions);
      // Store the result of image recognition.
      imageCaption.setCaption(fileId, predictions[0].className);
    });
  }

  var imageCaption = {
    image : {},
    init : function(cid) {
      this.image[cid] = { caption : null, attachment : null, postId : null };
    },
    setCaption : function(cid, caption) {
      if(typeof wirGl_localize !== 'undefined'
       && typeof wirGl_localize[caption] !== 'undefined') {
        caption = wirGl_localize[caption];
      }
      this.image[cid].caption = caption;
      this.setImageCaption(cid);
    },
    setAttachment : function(cid, attachment) {
      this.image[cid].attachment = attachment;
      this.setImageCaption(cid);
    },
    setPostId : function(cid, postId) {
      this.image[cid].postId = postId;
      this.setImageCaption(cid);
    },
    // Save the recognition result as a caption if both the recognition result and the upload result exist.
    setImageCaption : function(cid) {
      var img = this.image[cid];
      if(img.caption === null) {
        return;
      }
      if(img.attachment !== null) {
        // Save the caption to the attachment.
        img.attachment.save('caption', img.caption);
        // console.log("image caption updated;  post_id(%s) : %s", img.attachment.id, img.caption);
        // Delete the image information after the caption has been saved.
        delete this.image[cid];
      } else if (img.postId !== null) {
        // Send request for saving the caption of the image corresponding to the post id.
        $.ajax({
          type: 'POST',
          url: wirGl.ajaxUrl,
          dataType: 'json',
          data: {
            action: 'set_image_caption',
            nonce: wirGl.nonce,
            post_id: img.postId,
            caption: img.caption
          }
        })
        .done(function (response) {
          //
        });
        // Delete the image information after the caption has been saved.
        delete this.image[cid];
      }
    }
  };

  function getImageCanvas(image, resolve) {
    const MAX_WIDTH = 224;
    const MAX_HEIGHT = 224;

    var width, height;
    if(image.width > image.height){
      width = MAX_WIDTH;
      height = MAX_WIDTH * image.height/image.width;
    } else {
      width = MAX_HEIGHT * image.width/image.height;
      height = MAX_HEIGHT;
    }

    //if(!$('#canvas_test')[0]) $('.wp-heading-inline').after('<canvas id="canvas_test"></canvas>');
    //let canvas = document.getElementById('canvas_test');

    let canvas = document.createElement("canvas");

    EXIF.getData(image, function () {
      var rotate = 0;
      if (EXIF.pretty(this)) {
        var orientation = EXIF.getAllTags(this).Orientation;
        if (orientation == 6) {
          rotate = 90;
        } else if (orientation == 3) {
          rotate = 180;
        } else if (orientation == 8) {
          rotate = 270;
        }
      }
      if (rotate == 90 || rotate == 270) {
        canvas.width = height;
        canvas.height = width;
      } else {
        canvas.width = width;
        canvas.height = height;
      }

      let ctx = canvas.getContext("2d");
      ctx.clearRect(0, 0, width, height);

      // rotate image
      if (rotate && rotate > 0) {
        ctx.rotate(rotate * Math.PI / 180);
        if (rotate == 90)
          ctx.translate(0, -height);
        else if (rotate == 180)
          ctx.translate(-width, -height);
        else if (rotate == 270)
          ctx.translate(-width, 0);
      }
      ctx.drawImage(image, 0, 0, image.width, image.height, 0, 0, width, height);

      window.URL.revokeObjectURL(image.src);

      resolve(canvas);
    });
  }
})(jQuery);
