<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProMailAttachment
{
    public static function get($templateName)
    {
        $attachments = [];

        if (isset($templateName)) {
            $attachment = NewsletterProAttachment::newInstanceByTemplateName($templateName);

            if ($attachment) {
                $files = $attachment->filesPathFilename();

                if (!empty($files)) {
                    foreach ($files as $file) {
                        $attach = Swift_Attachment::fromPath($file['path'])->setFilename($file['name']);
                        $attachments[] = $attach;
                    }
                }
            }
        }

        return $attachments;
    }
}
