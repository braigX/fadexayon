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
 * Update (again) jpresta_ps_token
 */
function upgrade_module_8_4_0($module)
{
    // Get the token using the name used since v8.1.24
    $key = substr(md5($_SERVER['HTTP_HOST'] . (isset($_SERVER['BASE']) ? $_SERVER['BASE'] : '')), 0, 14);
    $currentToken = Configuration::get('jpresta_ps_token_' . $key, null, 0, 0, false);
    if ($currentToken) {
        // Store it using the original name (we now use an other way to detect clones)
        Configuration::updateValue('jpresta_ps_token', $currentToken, false, 0, 0);
        Configuration::deleteByName('jpresta_ps_token_' . $key);
    }

    JprestaApi::setPrestashopIsClone(false);

    return true;
}
