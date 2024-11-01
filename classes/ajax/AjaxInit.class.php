<?php
/**
 * AjaxInit
 * Version:     0.1
 * Author:      Green Sheep
 * Created:     May 31, 2018
 * Modified:
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

class AjaxInit
{
    /**
     * add action hooks
     */
    public function __construct()
    {
        add_action('wp_ajax_set_image_caption', array($this, 'addSetImageCaption'));
    }

    /**
     * action for wp ajax
     * set image captions
     */
    public function addSetImageCaption() {
    	new SetImageCaption();
    }
}
