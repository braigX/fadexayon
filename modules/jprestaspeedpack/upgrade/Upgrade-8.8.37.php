<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * Fix overrides for blog modules
 */
function upgrade_module_8_8_37($module)
{
    $phpFiles = glob(_PS_OVERRIDE_DIR_ . 'modules/*/controllers/*/*.php');
    foreach ($phpFiles as $file) {
        $content = Tools::file_get_contents($file);
        if (strpos($content, 'public function getJprestaModelObjectClassName') > 0) {
            $newContent = str_replace(
                'public function getJprestaModelObjectClassName',
                'public static function getJprestaModelObjectClassName',
                $content
            );
            file_put_contents($file, $newContent);
        }
    }

    return true;
}
