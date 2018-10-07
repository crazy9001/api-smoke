<?php
/**
 * Created by PhpStorm.
 * User: Demon Warlock
 * Date: 5/29/2018
 * Time: 10:09 PM
 */

namespace Vtv\Base\Supports;

use File;

class Helper
{
    /**
     * Load module's helpers
     * @param $directory
     * @author Toinn
     */
    public static function autoload($directory)
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

}