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
 * Update jpresta_ps_token
 */
function upgrade_module_8_1_24($module)
{
    $currentToken = Configuration::get('jpresta_ps_token', null, 0, 0, false);
    if ($currentToken) {
        // Store the token using the new name
        $key = substr(md5($_SERVER['HTTP_HOST'] . (isset($_SERVER['BASE']) ? $_SERVER['BASE'] : '')), 0, 14);
        Configuration::updateValue('jpresta_ps_token_' . $key, $currentToken, false, 0, 0);
        Configuration::deleteByName('jpresta_ps_token');
    }

    return true;
}
