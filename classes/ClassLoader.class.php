<?php
/**
 * ClassLoader
 * Version    : 1.1.0
 * Author     : Green Sheep
 * Created    : May 31, 2018
 * Modified   : May 31, 2018
 * License    : GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

class ClassLoader
{
    private $dir;

    /**
     * constructor
     */
    public function __construct()
    {
        // Get the directory name up to 'classes'.
        $this->dir = WIR_PLUGIN_PATH . 'classes/';

        spl_autoload_register(array($this, 'loader'), true, true);
    }

    /**
     * Do 'require' the class file.
     *
     * @param string filename
     */
    public function loader($filename)
    {
        if (strpos($filename, __NAMESPACE__) === false) {
            return false;
        }

        if (strstr($filename, '\\')) {
            $filenames = explode('\\', $filename);
            $filename = end($filenames);
        }

        // Set the name of the class file.
        $file = $this->dir . $filename . '.class.php';

        // Search for the file in the same hierarchical directory if it is not directly under the 'classes'.
        if (!file_exists($file)) {
            $files = scandir($this->dir);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                } elseif (is_file($this->dir . $file)) {
                    continue;
                } elseif (is_dir($this->dir . $file)) {
                    $tmp = $this->dir . $file . '/' . $filename . '.class.php';
                    if (file_exists($tmp)) {
                        $file = $tmp;
                        break;
                    }
                }
            }
        }

        if (is_readable($file)) {
            require $file;

            return true;
        }
    }
}
new ClassLoader();
