<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

header('Service-Worker-Allowed: /');
header('Content-Type: application/javascript');
header('X-Robots-Tag: none');
echo "importScripts('https://cdn.onesignal.com/sdks/OneSignalSDK.js');";
