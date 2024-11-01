<?php
/**
 * AbstractAjax
 * Version    : 0.1
 * Author     : Green Sheep
 * Created    : May 31, 2018
 * Modified   :
 * License    : GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

abstract class AbstractAjax
{
    /** HTTP request variables */
    protected $properties;

    public function __construct()
    {
        // nonce check
        if (!(isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'wir-ajax-action'))) {
            return false;
        }

        $this->properties = $_POST;

        $this->ajaxAction();
    }

    public function setProperty($key, $val)
    {
        $this->properties[$key] = $val;
    }

    public function getProperty($key)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }

        return null;
    }

    abstract protected function ajaxAction();

    protected function render($resource = '')
    {
        if ($resource === '') {
            $classname = get_class($this);
            $classname_array = explode('\\', $classname);
            $resource = WSS_PLUGIN_PATH . 'classes/view/' . end($classname_array) . 'View.php';
        }

        ob_start();
        include($resource);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
