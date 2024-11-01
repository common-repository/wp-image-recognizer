<?php
/**
 * Plugin name: WP Image Recognizer
 * Plugin URI: https://www.greensheep.co.jp/service/wordpress-plugin/wp-image-recognizer-plugin/
 * Description: Image recognition for WordPress media library.
 * Version: 1.0.0
 * Author: Green Sheep
 * Author URI: https://www.greensheep.co.jp/
 * Created: May 31, 2018
 * Modified: Nov 27, 2018
 * Text Domain:
 * Domain Path:
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

define('WIR_PLUGIN_PATH', plugin_dir_path(__FILE__));

require dirname(__FILE__) . '/classes/ClassLoader.class.php';

$init_plugin = new InitPlugin();
$init_plugin->executeEnqueueScripts();

new AjaxInit();
