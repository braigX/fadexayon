<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}


class EtsAbancartMail extends MailCore
{
    const METHOD_SMTP = 2;
    const METHOD_DISABLE = 3;
    const SEND_MAIL_DELIVERED = 1;
    const SEND_MAIL_FAILED = 0;
    const SEND_MAIL_TIMEOUT = 2;

    private static $retriableErrorCodes = array(
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    );

    public static $mailAPIs = array(
        'sendgrid',
        'sendinblue',
        'mailjet'
    );

    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null,
        &$errors = []
    )
    {
        if (version_compare(_PS_VERSION_, '1.6.1.1', '<=')) {
            return self::send1611($idLang
                , $template
                , $subject
                , $templateVars
                , $to
                , $toName
                , $from
                , $fromName
                , $fileAttachment
                , $templatePath
                , $die
                , $idShop
                , $bcc
                , $replyTo
                , $errors
            );
        }
        if (!$idShop) {
            $idShop = Context::getContext()->shop->id;
        }
        if ($is17 = version_compare(_PS_VERSION_, '1.7', '>')) {
            $hookBeforeEmailResult = Hook::exec(
                'actionEmailSendBefore',
                array(
                    'idLang' => &$idLang,
                    'template' => &$template,
                    'subject' => &$subject,
                    'templateVars' => &$templateVars,
                    'to' => &$to,
                    'toName' => &$toName,
                    'from' => &$from,
                    'fromName' => &$fromName,
                    'fileAttachment' => &$fileAttachment,
                    'mode_smtp' => &$mode_smtp,
                    'templatePath' => &$templatePath,
                    'die' => &$die,
                    'idShop' => &$idShop,
                    'bcc' => &$bcc,
                    'replyTo' => &$replyTo,
                ),
                null,
                true
            );

            if ($hookBeforeEmailResult === null) {
                $keepGoing = false;
            } else {
                $keepGoing = array_reduce(
                    $hookBeforeEmailResult,
                    function ($carry, $item) {
                        return ($item === false) ? false : $carry;
                    },
                    true
                );
            }
            if (!$keepGoing) {
                return true;
            }
        }
        if (is_numeric($idShop) && $idShop) {
            $shop = new Shop((int)$idShop);
        }
        $mailApiType = Configuration::get('ETS_ABANCART_MAIL_SERVICE') ?: 'default';
        $isMailApi = in_array($mailApiType, self::$mailAPIs);
        $configs = array(
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
        );
        $configs_extra = array(
            'PS_MAIL_METHOD',
            'PS_SHOP_EMAIL',
            'PS_SHOP_NAME',
            'PS_MAIL_TYPE'
        );
        $configuration = Configuration::getMultiple($configs_extra, null, null, $idShop);
        if ($mailApiType != 'default' && $configs && !$isMailApi) {
            $prefix = Tools::strtoupper($mailApiType);
            foreach ($configs as $config) {
                $configuration[$config] = Configuration::get(preg_replace('/^PS_(.+?)$/', 'ETS_ABANCART_$1_' . $prefix, $config), null, null, $idShop);
            }
            $configuration['PS_MAIL_METHOD'] = self::PS_SMTP_METHOD;
            $configuration['PS_SHOP_EMAIL'] = $configuration['PS_MAIL_USER'];
        } elseif (trim($mailApiType) == 'default') {
            foreach ($configs as $config) {
                $configuration[$config] = Configuration::get($config, null, null, $idShop);
            }
        }
        if (!$is17 && $configuration['PS_MAIL_METHOD'] == 3 || $is17 && $configuration['PS_MAIL_METHOD'] == self::METHOD_DISABLE) {
            return true;
        }

        Hook::exec('sendMailAlterTemplateVars',
            array(
                'template' => $template,
                'template_vars' => &$templateVars,
            )
        );

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']) || Tools::strtolower($configuration['PS_MAIL_SMTP_ENCRYPTION']) === 'off') {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = false;
        }

        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        /*
         * Sending an e-mail can be of vital importance for the merchant, when his password
         * is lost for example, so we must not die but do our best to send the e-mail.
         */
        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }

        if (!Validate::isEmail($from)) {
            $from = null;
        }

        if (!isset($fromName) || !Validate::isMailName($fromName)) {
            $fromName = $configuration['PS_SHOP_NAME'];
        }

        if (!Validate::isMailName($fromName)) {
            $fromName = null;
        }

        /*
         * It would be difficult to send an e-mail if the e-mail is not valid,
         * so this time we can die if there is a problem.
         */
        if (!is_array($to) && !Validate::isEmail($to)) {
            self::dieOrLog($die, 'Error: parameter "to" is corrupted');

            return false;
        }

        if (null !== $bcc && !is_array($bcc) && !Validate::isEmail($bcc)) {
            self::dieOrLog($die, 'Error: parameter "bcc" is corrupted');
            $bcc = null;
        }

        if (!is_array($templateVars)) {
            $templateVars = [];
        }

        if (is_string($toName) && !empty($toName) && !Validate::isMailName($toName)) {
            $toName = null;
        }

        if (!Validate::isTplName($template)) {
            self::dieOrLog($die, 'Error: invalid e-mail template');

            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            self::dieOrLog($die, 'Error: invalid e-mail subject');

            return false;
        }

        /* Construct multiple recipients list if needed */
        if (method_exists('Swift_Message', 'newInstance')) {
            $message = \Swift_Message::newInstance();
        } else {
            $message = new Swift_Message();
        }
        /* Create new message and DKIM sign it, if enabled and all data for signature are provided */
        if (isset($configuration['PS_MAIL_DKIM_ENABLE'])
            && (bool)$configuration['PS_MAIL_DKIM_ENABLE'] === true
            && !empty($configuration['PS_MAIL_DKIM_DOMAIN'])
            && !empty($configuration['PS_MAIL_DKIM_SELECTOR'])
            && !empty($configuration['PS_MAIL_DKIM_KEY'])
        ) {
            $signer = new Swift_Signers_DKIMSigner(
                $configuration['PS_MAIL_DKIM_KEY'],
                $configuration['PS_MAIL_DKIM_DOMAIN'],
                $configuration['PS_MAIL_DKIM_SELECTOR']
            );
            $message->attachSigner($signer);
        }
        if (is_array($to) && isset($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    self::dieOrLog($die, 'Error: invalid e-mail address');

                    return false;
                }

                if (is_array($toName) && isset($toName[$key])) {
                    $addrName = $toName[$key];
                } else {
                    $addrName = $toName;
                }

                $addrName = ($addrName == null || $addrName == $addr || !Validate::isGenericName($addrName)) ? '' : self::mimeEncode($addrName);
                $message->addTo(self::toPunycode($addr), $addrName);
            }
            $toPlugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $toPlugin = $to;
            $toName = (($toName == null || $toName == $to) ? '' : self::mimeEncode($toName));
            $message->addTo(self::toPunycode($to), $toName);
        }

        if (isset($bcc) && is_array($bcc)) {
            foreach ($bcc as $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    self::dieOrLog($die, 'Error: invalid e-mail address');

                    return false;
                }

                $message->addBcc(self::toPunycode($addr));
            }
        } elseif (isset($bcc)) {
            $message->addBcc(self::toPunycode($bcc));
        }

        try {
            /* Connect with the appropriate configuration */
            if (!$isMailApi) {
                if ($is17 && $configuration['PS_MAIL_METHOD'] == self::METHOD_SMTP || !$is17 && $configuration['PS_MAIL_METHOD'] == 2) {
                    if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                        self::dieOrLog($die, 'Error: invalid SMTP server or SMTP port');
                        return false;
                    }
                    if (method_exists('Swift_SmtpTransport', 'newInstance')) {
                        $connection = \Swift_SmtpTransport::newInstance(
                            $configuration['PS_MAIL_SERVER'],
                            $configuration['PS_MAIL_SMTP_PORT'],
                            $configuration['PS_MAIL_SMTP_ENCRYPTION']
                        )
                            ->setUsername($configuration['PS_MAIL_USER'])
                            ->setPassword($configuration['PS_MAIL_PASSWD']);
                    } else {
                        $connection = (new Swift_SmtpTransport($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], $configuration['PS_MAIL_SMTP_ENCRYPTION']))
                            ->setUsername($configuration['PS_MAIL_USER'])
                            ->setPassword($configuration['PS_MAIL_PASSWD']);

                    }

                } else {
                    if (version_compare(_PS_VERSION_, '1.7.6.0', '<=') && method_exists('Swift_SmtpTransport', 'newInstance')) {
                        $connection = \Swift_MailTransport::newInstance();
                    } else {
                        $connection = new Swift_SendmailTransport();
                    }
                }
                if (!$connection) {
                    return false;
                }
                $swift = new Swift_Mailer($connection);
            }
            /*---build config email---*/

            /* Get templates content */
            $iso = Language::getIsoById((int)$idLang);
            $isoDefault = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
            $isoArray = [];
            if ($iso) {
                $isoArray[] = $iso;
            }

            if ($isoDefault && $iso !== $isoDefault) {
                $isoArray[] = $isoDefault;
            }

            if (!in_array('en', $isoArray)) {
                $isoArray[] = 'en';
            }

            $moduleName = false;

            if (preg_match('#' . $shop->physical_uri . 'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath)) &&
                preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath), $res)
            ) {
                $moduleName = $res[1];
            }
            foreach ($isoArray as $isoCode) {
                $isoTemplate = $isoCode . '/' . $template;
                $templatePath = self::getTemplateBasePath($isoTemplate, $moduleName, ($is17 ? $shop->theme : $shop->getTheme()));

                if (!file_exists($templatePath . $isoTemplate . '.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                    PrestaShopLogger::addLog(
                        $is17 ? Context::getContext()->getTranslator()->trans(
                            'Error - The following e-mail template is missing: %s',
                            [$templatePath . $isoTemplate . '.txt'],
                            'Admin.Advparameters.Notification'
                        ) : sprintf(Tools::displayError('Error - The following e-mail template is missing: %s'), [$templatePath . $isoTemplate . '.txt'])
                    );
                } elseif (!file_exists($templatePath . $isoTemplate . '.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)) {

                    PrestaShopLogger::addLog(
                        $is17 ? Context::getContext()->getTranslator()->trans(
                            'Error - The following e-mail template is missing: %s',
                            [$templatePath . $isoTemplate . '.html'],
                            'Admin.Advparameters.Notification'
                        ) : sprintf(Tools::displayError('Error - The following e-mail template is missing: %s'), [$templatePath . $isoTemplate . '.txt'])
                    );
                } else {
                    $templatePathExists = true;

                    break;
                }
            }
            if (empty($templatePathExists)) {
                self::dieOrLog($die, 'Error - The following e-mail template is missing: %s', [$template]);

                return false;
            }
            $templateHtml = '';
            $templateTxt = '';
            Hook::exec(
                'actionEmailAddBeforeContent',
                array(
                    'template' => $template,
                    'template_html' => &$templateHtml,
                    'template_txt' => &$templateTxt,
                    'id_lang' => (int)$idLang,
                ),
                null,
                true
            );
            $templateHtml .= EtsAbancartHelper::file_get_contents($templatePath . $isoTemplate . '.html');
            $templateTxt .= strip_tags(
                html_entity_decode(
                    EtsAbancartHelper::file_get_contents($templatePath . $isoTemplate . '.txt'),
                    null,
                    'utf-8'
                )
            );
            Hook::exec(
                'actionEmailAddAfterContent',
                array(
                    'template' => $template,
                    'template_html' => &$templateHtml,
                    'template_txt' => &$templateTxt,
                    'id_lang' => (int)$idLang,
                ),
                null,
                true
            );
            /* Create mail and attach differents parts */
            $message->setSubject($subject);

            $message->setCharset('utf-8');

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $message->setId(Mail::generateId());

            if (!($replyTo && Validate::isEmail($replyTo))) {
                $replyTo = $from;
            }

            if (isset($replyTo) && $replyTo) {
                $message->setReplyTo($replyTo, ($replyToName !== '' ? $replyToName : null));
            }

            $templateVars = array_map(['Tools', 'htmlentitiesDecodeUTF8'], $templateVars);
            $templateVars = array_map(['Tools', 'stripslashes'], $templateVars);

            if (false !== Configuration::get('PS_LOGO_MAIL') && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop))) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop);
                } else {
                    $templateVars['{shop_logo}'] = '';
                }
            }
            ShopUrl::cacheMainDomainForShop((int)$idShop);
            /* don't attach the logo as */
            if (isset($logo)) {
                //$templateVars['{shop_logo}'] = $isMailApi ? Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_LOGO', null, null, $idShop) : $message->embed(\Swift_Image::fromPath($logo));
                $templateVars['{shop_logo}'] = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_LOGO', null, null, $idShop);
            }
            if ((Context::getContext()->link instanceof Link) === false) {
                Context::getContext()->link = new Link();
            }
            $url_params = isset($templateVars['{url_params}']) && $templateVars['{url_params}'] ? $templateVars['{url_params}'] : [];
            $templateVars['{shop_name}'] = Tools::safeOutput($shop->name);
            $templateVars['{shop_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('index', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{my_account_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('my-account', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{login_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('authentication', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{register_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('registration', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{guest_tracking_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('guest-tracking', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{history_url}'] = EtsAbancartTools::getInstance()->getCanonicalUrl(Context::getContext()->link->getPageLink('history', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $idShop));
            if (!empty($templateVars['{content}']) && (int)Configuration::get('ETS_ABANCART_EMAIL_GENERATE_URL') > 0) {
                preg_match_all('/<a\s+([^>]*\s+)?href=["\']([^"\']*)["\']([^>]*)>/i', $templateVars['{content}'], $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $fullTag = $match[0];
                    $otherAttributes = $match[1];
                    $hrefValue = $match[2];
                    $remainingTag = $match[3];
                    if (filter_var($hrefValue, FILTER_VALIDATE_URL) && !preg_match('/\/link\?_ab=.+$/', $hrefValue)) {
                        $newHrefValue = EtsAbancartTools::getInstance()->getCanonicalUrl($hrefValue, $url_params);
                        $newTag = '<a ' . $otherAttributes . 'href="' . $newHrefValue . '"' . $remainingTag . '>';
                        $templateVars['{content}'] = str_replace($fullTag, $newTag, $templateVars['{content}']);
                    }
                }
            }
            if (isset($templateVars['{content}']))
                $templateVars['{context}'] = $templateVars['{content}'];
            $extraTemplateVars = [];
            Hook::exec(
                'actionGetExtraMailTemplateVars',
                array(
                    'template' => $template,
                    'template_vars' => $templateVars,
                    'extra_template_vars' => &$extraTemplateVars,
                    'id_lang' => (int)$idLang,
                ),
                null,
                true
            );
            $templateVars = array_merge($templateVars, $extraTemplateVars);
            $send = false;
            if ($isMailApi && $mailApiType) {
                $mailMsg = false;
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT) {
                    $mailMsg = $templateTxt;
                } elseif ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                    $mailMsg = $templateHtml;
                }
                if ($mailMsg) {
                    foreach ($templateVars as $key => $value)
                        $mailMsg = str_replace($key, $value, $mailMsg);
                } else
                    return false;

                $apiKeyId = Configuration::get('ETS_ABANCART_MAIL_API_KEY_' . Tools::strtoupper($mailApiType), null, null, $idShop);
                switch ($mailApiType) {
                    case 'sendgrid' :
                        $send = self::sendGrid($apiKeyId, $message->getSubject(), $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        break;
                    case 'sendinblue' :
                        $send = self::sendinBlue($apiKeyId, $message->getSubject(), $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        break;
                    case 'mailjet' :
                        $send = self::mailJet($apiKeyId, Configuration::get('ETS_ABANCART_MAIL_SECRET_KEY_' . Tools::strtoupper($mailApiType), null, null, $idShop), $message->getSubject(), $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        if (isset($send['error']) && $send['error'] !== '') {
                            $errors[] = $send['error'];

                            return false;
                        }
                        break;
                }
            } elseif (!empty($swift)) {
                $swift->registerPlugin(new \Swift_Plugins_DecoratorPlugin(array(self::toPunycode($toPlugin) => $templateVars)));
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH ||
                    $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML
                ) {
                    $message->addPart($templateHtml, 'text/html', 'utf-8');
                    if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH) {
                        $message->addPart($templateTxt, 'text/plain', 'utf-8');
                    }
                } else {
                    $message->addPart($templateTxt, 'text/plain', 'utf-8');
                }
                if (!empty($fileAttachment)) {
                    // Multiple attachments?
                    if (!is_array(current($fileAttachment))) {
                        $fileAttachment = [$fileAttachment];
                    }

                    foreach ($fileAttachment as $attachment) {
                        if (isset($attachment['content'], $attachment['name'], $attachment['mime'])) {
                            $message->attach(
                                (new Swift_Attachment())->setFilename(
                                    $attachment['name']
                                )->setContentType($attachment['mime'])
                                    ->setBody($attachment['content'])
                            );
                        }
                    }
                }
                $message->setFrom(array($from => $fromName));
                Hook::exec('actionMailAlterMessageBeforeSend', array(
                    'message' => &$message,
                ));
                $send = $swift->send($message);
            }

            ShopUrl::resetMainDomainCache();

            if ($send && Configuration::get('PS_LOG_EMAILS')) {
                $mail = new Mail();
                $mail->template = Tools::substr($template, 0, 62);
                $mail->subject = Tools::substr($subject, 0, 255);
                $mail->id_lang = (int)$idLang;
                $recipientsTo = $message->getTo();
                $recipientsCc = $message->getCc();
                $recipientsBcc = $message->getBcc();
                if (!is_array($recipientsTo)) {
                    $recipientsTo = [];
                }
                if (!is_array($recipientsCc)) {
                    $recipientsCc = [];
                }
                if (!is_array($recipientsBcc)) {
                    $recipientsBcc = [];
                }
                foreach (array_merge($recipientsTo, $recipientsCc, $recipientsBcc) as $email => $recipient_name) {
                    $mail->id = null;
                    $mail->recipient = Tools::substr($email, 0, 255);
                    $mail->add();
                }
                unset($recipient_name);
            }

            return $send;
        } catch (\Swift_SwiftException $e) {
            PrestaShopLogger::addLog(
                'Swift Error: ' . $e->getMessage(),
                3,
                null,
                version_compare(_PS_VERSION_, '8.0.0', '>=') ? 'SwiftMessage' : 'Swift_Message'
            );
            $errors = $e->getMessage();
            return false;
        }
    }

    const PS_SMTP_METHOD = 2;

    public static function send1611(
        $id_lang,
        $template,
        $subject,
        $template_vars,
        $to,
        $to_name = null,
        $from = null,
        $from_name = null,
        $file_attachment = null,
        $template_path = _PS_MAIL_DIR_,
        $die = false,
        $id_shop = null,
        $bcc = null,
        $reply_to = null,
        &$errors = []
    )
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $mailApiType = Configuration::get('ETS_ABANCART_MAIL_SERVICE') ?: 'default';
        $isMailApi = in_array($mailApiType, self::$mailAPIs);
        $configs = array(
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
        );
        $configs_extra = array(
            'PS_MAIL_METHOD',
            'PS_SHOP_EMAIL',
            'PS_SHOP_NAME',
            'PS_MAIL_TYPE'
        );
        $configs = (!$isMailApi ? array_merge($configs, $configs_extra) : $configs_extra);
        $configuration = Configuration::getMultiple($configs, null, null, $id_shop);
        if ($mailApiType != 'default' && $configs && !$isMailApi) {
            $prefix = Tools::strtoupper($mailApiType);
            foreach ($configs as $config) {
                $configuration[$config] = Configuration::get(preg_replace('/^PS_(.+?)$/', 'ETS_ABANCART_$1_' . $prefix, $config), null, null, $id_shop);
            }
            $configuration['PS_MAIL_METHOD'] = self::PS_SMTP_METHOD;
            $configuration['PS_SHOP_EMAIL'] = $configuration['PS_MAIL_USER'];
        }

        if ($configuration['PS_MAIL_METHOD'] == 3) {
            return true;
        }

        $theme_path = _PS_THEME_DIR_;

        if (is_numeric($id_shop) && $id_shop) {
            $shop = new Shop((int)$id_shop);
            $theme_name = $shop->getTheme();

            if (_THEME_NAME_ != $theme_name) {
                $theme_path = _PS_ROOT_DIR_ . '/themes/' . $theme_name . '/';
            }
        }

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION'])) {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
        }
        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }

        if (!Validate::isEmail($from)) {
            $from = null;
        }

        if (!isset($from_name) || !Validate::isMailName($from_name)) {
            $from_name = $configuration['PS_SHOP_NAME'];
        }
        if (!Validate::isMailName($from_name)) {
            $from_name = null;
        }

        if (!is_array($to) && !Validate::isEmail($to)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);
            return false;
        }

        if (!is_null($bcc) && !is_array($bcc) && !Validate::isEmail($bcc)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "bcc" is corrupted'), $die);
            $bcc = null;
        }

        if (!is_array($template_vars)) {
            $template_vars = array();
        }

        if (is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name)) {
            $to_name = null;
        }

        if (!Validate::isTplName($template)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail template'), $die);
            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);
            return false;
        }

        /* Construct multiple recipients list if needed */
        $to_list = new Swift_RecipientList();
        if (is_array($to) && isset($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);
                    return false;
                }

                if (is_array($to_name) && $to_name && is_array($to_name) && Validate::isGenericName($to_name[$key])) {
                    $to_name = $to_name[$key];
                }

                $to_name = (($to_name == null || $to_name == $addr) ? '' : self::mimeEncode($to_name));
                $to_list->addTo($addr, $to_name);
            }
            $to_plugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $to_plugin = $to;
            $to_name = (($to_name == null || $to_name == $to) ? '' : self::mimeEncode($to_name));
            $to_list->addTo($to, $to_name);
        }
        if (isset($bcc)) {
            $to_list->addBcc($bcc);
        }

        try {
            /* Connect with the appropriate configuration */
            if (!$isMailApi) {
                if ($configuration['PS_MAIL_METHOD'] == 2) {
                    if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                        Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);
                        return false;
                    }
                    $connection = new Swift_Connection_SMTP(
                        $configuration['PS_MAIL_SERVER'],
                        $configuration['PS_MAIL_SMTP_PORT'],
                        $configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl' ? Swift_Connection_SMTP::ENC_SSL : (($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls' ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF))
                    );
                    $connection->setTimeout(4);
                    if (!$connection) {
                        return false;
                    }
                    if (!empty($configuration['PS_MAIL_USER'])) {
                        $connection->setUsername($configuration['PS_MAIL_USER']);
                    }
                    if (!empty($configuration['PS_MAIL_PASSWD'])) {
                        $connection->setPassword($configuration['PS_MAIL_PASSWD']);
                    }
                } else {
                    $connection = new Swift_Connection_NativeMail();
                }
                if (!$connection) {
                    return false;
                }
                $classSwift = 'Swift';
                $swift = new $classSwift($connection, Configuration::get('PS_MAIL_DOMAIN', null, null, $id_shop));
            }
            /*---build config email---*/

            /* Get templates content */
            $iso = Language::getIsoById((int)$id_lang);
            if (!$iso) {
                Tools::dieOrLog(Tools::displayError('Error - No ISO code for email'), $die);
                return false;
            }
            $iso_template = $iso . '/' . $template;

            $module_name = false;
            $override_mail = false;

            if (preg_match('#' . $shop->physical_uri . 'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $template_path)) && preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $template_path), $res)) {
                $module_name = $res[1];
            }

            if ($module_name !== false && (file_exists($theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . '.txt') ||
                    file_exists($theme_path . 'modules/' . $module_name . '/mails/' . $iso_template . '.html'))) {
                $template_path = $theme_path . 'modules/' . $module_name . '/mails/';
            } elseif (file_exists($theme_path . 'mails/' . $iso_template . '.txt') || file_exists($theme_path . 'mails/' . $iso_template . '.html')) {
                $template_path = $theme_path . 'mails/';
                $override_mail = true;
            }
            if (!file_exists($template_path . $iso_template . '.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:') . ' ' . $template_path . $iso_template . '.txt', $die);
                return false;
            } elseif (!file_exists($template_path . $iso_template . '.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)) {
                Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:') . ' ' . $template_path . $iso_template . '.html', $die);
                return false;
            }
            $template_html = '';
            $template_txt = '';
            Hook::exec('actionEmailAddBeforeContent', array(
                'template' => $template,
                'template_html' => &$template_html,
                'template_txt' => &$template_txt,
                'id_lang' => (int)$id_lang
            ), null, true);
            $template_html .= EtsAbancartHelper::file_get_contents($template_path . $iso_template . '.html');
            $template_txt .= strip_tags(html_entity_decode(EtsAbancartHelper::file_get_contents($template_path . $iso_template . '.txt'), null, 'utf-8'));
            Hook::exec('actionEmailAddAfterContent', array(
                'template' => $template,
                'template_html' => &$template_html,
                'template_txt' => &$template_txt,
                'id_lang' => (int)$id_lang
            ), null, true);
            if ($override_mail && file_exists($template_path . $iso . '/lang.php')) {
                include_once($template_path . $iso . '/lang.php');
            } elseif ($module_name && file_exists($theme_path . 'mails/' . $iso . '/lang.php')) {
                include_once($theme_path . 'mails/' . $iso . '/lang.php');
            } elseif (file_exists(_PS_MAIL_DIR_ . $iso . '/lang.php')) {
                include_once(_PS_MAIL_DIR_ . $iso . '/lang.php');
            } else {
                Tools::dieOrLog(Tools::displayError('Error - The language file is missing for:') . ' ' . $iso, $die);
                return false;
            }

            $message = new Swift_Message($subject);

            $message->setCharset('utf-8');

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $message->setId(Mail::generateId());

            $reflect = new ReflectionObject($message);
            foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED) as $prop) {
                if ($prop->getName() == 'headers') {
                    $message->{$prop->getName()}->setEncoding('Q');
                    break;
                }
            }

            if (!($reply_to && Validate::isEmail($reply_to))) {
                $reply_to = $from;
            }

            if (isset($reply_to) && $reply_to) {
                $message->setReplyTo($reply_to);
            }

            $template_vars = array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $template_vars);
            $template_vars = array_map(array('Tools', 'stripslashes'), $template_vars);

            if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $id_shop))) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $id_shop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $id_shop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $id_shop);
                } else {
                    $template_vars['{shop_logo}'] = '';
                }
            }
            ShopUrl::cacheMainDomainForShop((int)$id_shop);
            /* don't attach the logo as */
            if (isset($logo)) {
                $template_vars['{shop_logo}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File($logo), null, ImageManager::getMimeTypeByExtension($logo)));
            }

            if ((Context::getContext()->link instanceof Link) === false) {
                Context::getContext()->link = new Link();
            }

            $template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
            $template_vars['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{my_account_url}'] = Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{login_url}'] = Context::getContext()->link->getPageLink('authentication', true, $id_lang, null, false, $id_shop);
            $template_vars['{register_url}'] = Context::getContext()->link->getPageLink('registration', true, $id_lang, null, false, $id_shop);
            $template_vars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink('guest-tracking', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{history_url}'] = Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $id_shop));

            $extra_template_vars = array();
            Hook::exec('actionGetExtraMailTemplateVars', array(
                'template' => $template,
                'template_vars' => $template_vars,
                'extra_template_vars' => &$extra_template_vars,
                'id_lang' => (int)$id_lang
            ), null, true);
            $template_vars = array_merge($template_vars, $extra_template_vars);
            $send = false;
            /*---API Email---*/
            if ($isMailApi && $mailApiType) {
                $mailMsg = false;
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT) {
                    $mailMsg = $template_txt;
                } elseif ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                    $mailMsg = $template_html;
                }
                if ($mailMsg) {
                    foreach ($template_vars as $key => $value)
                        $mailMsg = str_replace($key, $value, $mailMsg);
                } else
                    return false;

                $apiKeyId = Configuration::get('ETS_ABANCART_MAIL_API_KEY_' . Tools::strtoupper($mailApiType), null, null, $id_shop);
                switch ($mailApiType) {
                    case 'sendgrid' :
                        $send = self::sendGrid($apiKeyId, $message->getSubject(), $to, $to_name, $from, $from_name, $bcc, $reply_to, null, $mailMsg);
                        break;
                    case 'sendinblue' :
                        $send = self::sendinBlue($apiKeyId, $message->getSubject(), $to, $to_name, $from, $from_name, $bcc, $reply_to, null, $mailMsg);
                        break;
                    case 'mailjet' :
                        $send = self::mailJet($apiKeyId, Configuration::get('ETS_ABANCART_MAIL_SECRET_KEY_' . Tools::strtoupper($mailApiType), null, null, $id_shop), $message->getSubject(), $to, $to_name, $from, $from_name, $bcc, $reply_to, null, $mailMsg);
                        if (isset($send['error']) && $send['error'] !== '') {
                            $errors[] = $send['error'];

                            return false;
                        }
                        break;
                }
            } /*---End API Email---*/
            elseif (!empty($swift)) {
                $swift->attachPlugin(new Swift_Plugin_Decorator(array($to_plugin => $template_vars)), 'decorator');
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT) {
                    $message->attach(new Swift_Message_Part($template_txt, 'text/plain', '8bit', 'utf-8'));
                }
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                    $message->attach(new Swift_Message_Part($template_html, 'text/html', '8bit', 'utf-8'));
                }
                if ($file_attachment && !empty($file_attachment)) {
                    if (!is_array(current($file_attachment))) {
                        $file_attachment = array($file_attachment);
                    }

                    foreach ($file_attachment as $attachment) {
                        if (isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime'])) {
                            $message->attach(new Swift_Message_Attachment($attachment['content'], $attachment['name'], $attachment['mime']));
                        }
                    }
                }
                /* Send mail */
                $send = $swift->send($message, $to_list, new Swift_Address($from, $from_name));
                $swift->disconnect();
            }

            ShopUrl::resetMainDomainCache();

            if ($send && Configuration::get('PS_LOG_EMAILS')) {
                $mail = new Mail();
                $mail->template = Tools::substr($template, 0, 62);
                $mail->subject = Tools::substr($subject, 0, 254);
                $mail->id_lang = (int)$id_lang;
                foreach (array_merge($to_list->getTo(), $to_list->getCc(), $to_list->getBcc()) as $recipient) {
                    /** @var Swift_Address $recipient */
                    $mail->id = null;
                    $mail->recipient = Tools::substr($recipient->getAddress(), 0, 126);
                    $mail->add();
                }
            }

            return $send;
        } catch (Swift_Exception $e) {
            PrestaShopLogger::addLog(
                'Swift Error: ' . $e->getMessage(),
                3,
                null,
                version_compare(_PS_VERSION_, '8.0.0', '>=') ? 'SwiftMessage' : 'Swift_Message'
            );
            $errors = $e->getMessage();
            return false;
        }
    }

    protected static function getTemplateBasePath($isoTemplate, $moduleName, $theme)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>')) {
            return parent::getTemplateBasePath($isoTemplate, $moduleName, $theme);
        }
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/',
            _PS_ROOT_DIR_,
        );

        if ($moduleName !== false) {
            $templateRelativePath = '/modules/' . $moduleName . '/mails/';
        } else {
            $templateRelativePath = '/mails/';
        }

        /*---get iso_code---*/
        if (($isoCode = explode('/', $isoTemplate)) && isset($isoCode[0]))
            $isoCode = $isoCode[0];
        /*---end get iso_code---*/

        foreach ($basePathList as $base) {
            $templatePath = $base . $templateRelativePath;
            if (file_exists($templatePath . $isoTemplate . '.txt') || file_exists($templatePath . $isoTemplate . '.html')) {
                /*---include language---*/
                if (file_exists($templatePath . $isoCode . '/lang.php')) {
                    include_once($templatePath . $isoCode . '/lang.php');
                } elseif (file_exists(_PS_MAIL_DIR_ . $isoCode . '/lang.php')) {
                    include_once(_PS_MAIL_DIR_ . $isoCode . '/lang.php');
                } else {
                    Tools::dieOrLog(Tools::displayError('Error - The language file is missing for:') . ' ' . $isoCode, false);
                    return false;
                }
                /*---end include language---*/
                return $templatePath;
            }
        }

        return '';
    }

    public static function toPunycode($to)
    {
        if (method_exists('Mail', 'toPunycode'))
            return parent::toPunycode($to);
        return $to;
    }

    protected static function dieOrLog(
        $die,
        $message,
        $templates = [],
        $domain = 'Admin.Advparameters.Notification'
    )
    {
        if (method_exists('Mail', 'dieOrLog')) {
            parent::dieOrLog($die, $message, $templates, $domain);
        } else {
            Tools::dieOrLog(sprintf(Tools::displayError($message), $templates), $die);
        }
    }

    public static function sendGrid($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.sendgrid.com/v3/mail/send';
        $headers = array(
            "Authorization: Bearer " . $apiKeyId,
            "Content-Type: application/json",
        );
        $http_build_query = '{
            "personalizations": [{
                "to": [
                    {
                      "email": "' . $to . '",
                      "name": "' . $toName . '"
                    }
                ],
                ' . ($bcc ? '
                "bcc": [
                    {
                        "email": "' . $bcc . '"
                    }
                ],' : '') . '
                "subject": "' . $subject . '"
            }],
            "from": {
                "email": "' . $from . '",
                "name": "' . $fromName . '"
            },
            "reply_to": {
                "email": "' . $replyTo . '",
                "name": "' . $replyToName . '"
            },
            "content" : [{
                "type" : "text/html",
                "value" : ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . '
            }]
        }';

        return self::cURL($headers, $url, $http_build_query) !== false;
    }

    public static function sendinBlue($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.sendinblue.com/v3/smtp/email';
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "api-key: " . $apiKeyId,
        );
        $http_build_query = '{
            "sender": {
                "name": "' . $fromName . '",
                "email": "' . $from . '"
            },
            "to": [
                {
                    "email": "' . $to . '",
                    "name": "' . $toName . '"
                }
            ],
            ' . ($bcc ? '
            "bcc": [
                {
                    "email": "' . $bcc . '"
                }
            ],' : '') . '
            "htmlContent": ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . ',
            "subject": "' . $subject . '",
            "replyTo": {
                "email": "' . $replyTo . '",
                "name": "' . ($replyToName ?: $fromName) . '"
            }
        }';

        return self::cURL($headers, $url, $http_build_query) !== false;
    }

    public static function mailJet($apiKeyId, $secretKey, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.mailjet.com/v3.1/send';
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Basic " . call_user_func('base64_encode', $apiKeyId . ':' . $secretKey),
        );
        $sender = self::getSenderMailJet($headers);
        if (empty($sender)) {
            return [
                'error' => 'Error - Sender is Inactive or Empty. Please active sender link: <a target="_blank" rel="noreferrer noopener" href="https://app.mailjet.com/account/sender">https://app.mailjet.com/account/sender</a>',
            ];
        } elseif (isset($sender['error'])) {
            return [
                'error' => $sender['error']
            ];
        }
        $http_build_query = '{
            "Messages": [
                {
                    "From": {
                        "Email": "' . $sender['email'] . '",
                        "Name": "' . $sender['name'] . '"
                    },
                    "To": [
                        {
                            "Email": "' . $to . '",
                            "Name": "' . $toName . '"
                        }
                    ],
                    ' . ($bcc ? '
                    "Bcc": [
                        {
                            "Email": "' . $bcc . '"
                        }
                    ],' : '') . '
                    "Reply-To": [
                        {
                            "Email": "' . $replyTo . '",
                            "Name": "' . $replyToName . '"
                        }
                    ],
                    "Subject": "' . $subject . '",
                    "HTMLPart": ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . '
                }
            ]
        }';

        return self::cURL($headers, $url, $http_build_query) !== false;
    }

    public static function getSenderMailJet($headers)
    {
        $url = 'https://api.mailjet.com/v3/REST/sender';
        $res = self::cURL($headers, $url, [], 'GET');
        if (!$res) {
            return ['error' => 'Error: API Key error!'];
        }
        $res = json_decode($res);
        $sender = [];
        if ($res && property_exists($res, 'Data') && is_array($res->Data) && count($res->Data) > 0) {
            foreach ($res->Data as $data) {
                if (property_exists($data, 'Status') && $data->Status === 'Active') {
                    $sender = [
                        'email' => $data->Email,
                        'name' => $data->Name,
                    ];
                    break;
                }
            }
        }
        return $sender;
    }

    public static function cURL($headers, $url, $http_build_query, $request = 'POST')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        if ($http_build_query) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $http_build_query);
        }
        return self::execute($curl);
    }

    public static function execute($ch, $retries = 5, $closeAfterDone = true)
    {
        $result = false;
        while ($retries--) {
            if (($result = curl_exec($ch)) === false) {
                $curlErrno = curl_errno($ch);
                if (false === in_array($curlErrno, self::$retriableErrorCodes, true) || !$retries) {
                    if ($closeAfterDone) {
                        curl_close($ch);
                    }
                    return false;
                }
                continue;
            }
            if ($closeAfterDone) {
                curl_close($ch);
            }
            break;
        }
        return $result;
    }

    /*---end API mail service---*/
}