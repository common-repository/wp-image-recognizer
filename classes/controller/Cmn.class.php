<?php
/**
 * Cmn
 * description: Common processes
 * Version    : 0.1
 * Author     : Green Sheep
 * Created    : May 31, 2018
 * Modified   :
 * License    : GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace plugins\wir;

class Cmn
{
    /** prefix for the plugin */
    const PREFIX = 'wir_';

    const UNIQUE_KEY = 'Xv4RWF_';

    const TFJS_URL = 'https://www.greensheep.co.jp/tfjs/';

    /**
     * Return the url path of the plugin.
     *
     * @return string
     */
    public static function pluginUrlPath()
    {
        return plugins_url() . '/' . basename(WIR_PLUGIN_PATH) . '/';
    }
}
