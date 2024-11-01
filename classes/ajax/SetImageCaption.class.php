<?php
/**
 * SetImageCaption
 * Version:     0.1
 * Author:      Green Sheep
 * Created:     March 16, 2018
 * Modified:
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

class SetImageCaption extends AbstractAjax
{
    protected function ajaxAction()
    {
        $result = 'error';

        $post_id = $this->getProperty('post_id');
        $caption = $this->getProperty('caption');

        if(isset($post_id) && isset($caption)) {
          wp_update_post(array(
            'ID'           => $post_id,
            'post_excerpt' => $caption
          ));
          $result = 'success';
        }

        echo json_encode(['status' => $result]);
        die();
    }
}
