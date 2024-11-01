<?php
/**
 * InitPlugin
 * Version    : 0.1
 * Author     : Green Sheep
 * Created    : May 31, 2018
 * Modified   :
 * License    : GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

class InitPlugin
{
  private $suffix = '.min';

  function __construct() {
    $suffix = SCRIPT_DEBUG ? '' : '.min';
  }

  /**
   * Add scripts and styles.
   */
  public function executeEnqueueScripts()
  {
      add_action('admin_enqueue_scripts', array($this, 'adminScript'));
  }

  /**
   * Add scripts to administration screens.
   */
  public function adminScript()
  {
    wp_register_script(
      Cmn::PREFIX . 'admin',
      Cmn::pluginUrlPath() . "js/admin$this->suffix.js",
      array('jquery', Cmn::PREFIX . 'tfjs', Cmn::PREFIX . 'model'),
      '',
      true
    );
    wp_localize_script(
      Cmn::PREFIX . 'admin',
      'wirGl',
      array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wir-ajax-action')
      )
    );
    wp_enqueue_script(
      Cmn::PREFIX . 'tfjs',
      Cmn::TFJS_URL . "tfjs.js",
      array(),
      '',
      true
    );
    wp_enqueue_script(
      Cmn::PREFIX . 'model',
      Cmn::TFJS_URL . "model/bundle.js",
      array(Cmn::PREFIX . 'tfjs'),
      '',
      true
    );
    wp_enqueue_script(
      Cmn::PREFIX . 'exif',
      Cmn::pluginUrlPath() . "js/exif-js/exif.js",
      array(),
      '',
      true
    );
    $locale = get_locale();
    if($locale == 'ja') {
      wp_enqueue_script(
        Cmn::PREFIX . 'localize',
        Cmn::pluginUrlPath() . "js/localize-ja.js",
        array(),
        '',
        true
      );
    }
    wp_enqueue_script(
      Cmn::PREFIX . 'admin'
    );
  }
}
