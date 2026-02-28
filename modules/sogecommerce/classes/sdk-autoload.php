<?php
/**
 * Copyright © Lyra Network and contributors.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network and contributors
 * @license   See COPYING.md for license details.
 */

spl_autoload_register('sogecommerceSdkAutoload', true, true);

function sogecommerceSdkAutoload($className)
{
    if (empty($className) || strpos($className, 'Lyranetwork\\Sogecommerce\\Sdk') !== 0) {
        // Not Sogecommerce SDK classes.
        return;
    }

    $className = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $className);
    include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . $className . '.php';
}
