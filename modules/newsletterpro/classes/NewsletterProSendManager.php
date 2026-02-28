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

require_once dirname(__FILE__).'/NewsletterProPrepareNewsletter.php';

class NewsletterProSendManager
{
    public $prepare;

    public $fwd_success_count = 0;

    public static $instance;

    private $template_name;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->prepare = NewsletterProPrepareNewsletter::newInstance();
    }

    public static function newInstance()
    {
        return new self();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = self::newInstance();
        }

        return self::$instance;
    }

    public function setTemplateNameForAttachment($template_name)
    {
        $this->template_name = $template_name;
    }

    /**
     * Send newsletter.
     *
     * @param string $subject
     * @param string $template
     * @param string $to
     * @param array  $data
     * @param array  $forward
     * @param bool   $shut_down_register This should be true only in the progress of sending newsletters in the bakcofice
     *
     * @return array
     */
    public function sendNewsletter($subject, $template, $to, $data = [], $forward = [], $shut_down_register = true, $exclude_email = false)
    {
        if ($shut_down_register) {
            register_shutdown_function([$this, 'sendNewslettersShutdown']);
        }

        $errors = [];

        $id_smtp = null;
        $send_method = null;
        $user = null;

        // id_smtp, send_method, user
        extract($data);

        if (isset($user)) {
            $this->setUserContext($user);
        }

        if (isset($send_method) && 'mail' == $send_method) {
            $mail = NewsletterProMail::getInstance(NewsletterProMail::getDefaultMail());
        } elseif (isset($id_smtp)) {
            $mail = NewsletterProMail::newInstance((int) $id_smtp);
        } else {
            $mail = NewsletterProMail::getInstanceByContext();
        }

        if ($mail) {
            if (isset($this->template_name)) {
                $mail->setTemplateNameForAttachment($this->template_name);
            }

            if ($exclude_email) {
                $errors[] = NewsletterPro::getInstance()->l('The email is excluded from the process of sending. The email exists into exclusion list.');

                return $errors;
            }
            $this->fwd_success_count = 0;

            if (pqnp_config('DEBUG_MODE')) {
                $send = $mail->send($subject, $template, $to);
            } else {
                $send = @$mail->send($subject, $template, $to);
            }

            if ($send) {
                if ((bool) pqnp_config('FWD_FEATURE_ACTIVE') && self::isForward($forward)) {
                    $mail->sendForward($forward['data'], $forward['type'], $forward['from'], (int) pqnp_config('SLEEP'));
                    $this->fwd_success_count += $mail->getSuccessFwdCount();
                }

                return true;
            } else {
                $errors = array_merge($errors, $mail->getErrors());
            }
        } else {
            $errors[] = NewsletterPro::getInstance()->l('Cannot establish the email connection.');
        }

        return $errors;
    }

    /**
     * Register send newsletter shutdown function.
     *
     * @return die
     */
    public function sendNewslettersShutdown()
    {
        $errors = [];
        $response = [
            'status' => false,
            'exit' => false,
            'errors' => &$errors,
        ];

        $ob_content = ob_get_contents();
        @ob_end_clean();

        if (preg_match('/Fatal error/', $ob_content)) {
            if (preg_match('/Maximum execution time/', $ob_content)) {
                $errors[] = NewsletterPro::getInstance()->l('PHP max execution time exceeded because of the followers. The newsletter will jump to the next email address.');
            } else {
                $errors[] = $ob_content;
            }

            exit(NewsletterProTools::jsonEncode($response));
        }
        exit($ob_content);
    }

    /**
     * Set user context.
     *
     * @param object $user
     */
    public function setUserContext($user)
    {
        if (isset($user) && 'object' == gettype($user)) {
            if ('customer' === $user->user_type || $user->hasEmployeeCustomerData()) {
                $this->context = NewsletterProCustomerContext::getContext((int) $user->id);
            }
        }
    }

    /**
     * Check if the email si forward.
     *
     * @param string $forward
     *
     * @return bool
     */
    public static function isForward($forward)
    {
        if (is_array($forward) && !empty($forward) && isset($forward['data'], $forward['type'], $forward['from'])) {
            return true;
        }

        return false;
    }

    /**
     * Build forwarder.
     *
     * @param array  $data
     * @param string $type
     * @param string $to
     *
     * @return array
     */
    public static function buildForward($data, $type, $to)
    {
        return [
            'data' => $data,
            'type' => $type,
            'from' => $to,
        ];
    }

    public static function sendTestTerminal($to, $id_smtp = 0)
    {
        $output = [
            'success' => [],
            'errors' => [],
        ];

        if ($id_smtp > 0) {
            $mail = NewsletterProMail::newInstance((int) $id_smtp);
            if (!Validate::isLoadedObject($mail)) {
                $output['errors'][] = 'Unable to load the mail object.';

                return false;
            }
        } else {
            $mail = NewsletterProMail::getInstanceByContext();
            if (!$mail) {
                $output['errors'][] = 'Unable to load the mail object.';

                return false;
            }
        }

        $subject = Context::getContext()->shop->name.' - '.NewsletterPro::getInstance()->l('Test Email Connection');
        $template = '<h2>'.NewsletterPro::getInstance()->l('The connection is valid!').'</h2>';

        if (@$mail->send($subject, $template, $to)) {
            $output['success'][] = sprintf('An emails was sent to "%s".', $to);
        } else {
            $output['errors'] = array_merge($output['errors'], $mail->getErrors());
        }

        return $output;
    }

    /**
     * Send a test email.
     *
     * @param string $to
     *
     * @return json
     */
    public function sendMailTest($to, $id_smtp = null)
    {
        $response = NewsletterProAjaxResponse::newInstance([
            'status' => false,
            'msg' => '',
        ]);

        try {
            if (isset($id_smtp) && $id_smtp > 0) {
                $mail = NewsletterProMail::newInstance((int) $id_smtp);

                if (!Validate::isLoadedObject($mail)) {
                    $mail = false;
                }
            } else {
                $mail = NewsletterProMail::getInstanceByContext();
            }

            if ($mail) {
                $subject = (string) $this->context->shop->name.' - '.NewsletterPro::getInstance()->l('Test Email Connection');
                $template = '<h2>'.NewsletterPro::getInstance()->l('The connection is valid!').'</h2>';

                if (pqnp_config('DEBUG_MODE')) {
                    $send = $mail->send($subject, $template, $to);
                } else {
                    $send = @$mail->send($subject, $template, $to);
                }

                if (!$send) {
                    $response->mergeErrors($mail->getErrors());
                } else {
                    $response->set('status', true);
                }
            } else {
                $response->addError(NewsletterPro::getInstance()->l('Cannot establish the email connection.'));
            }
        } catch (Exception $e) {
            $response->addError($e->getMessage());
        }

        if (!$response->success()) {
            $response->set('msg', implode('<br>', $response->getErrors()));
        }

        return $response->display();
    }

    /**
     * Send a test email.
     *
     * @param string $email
     * @param string $template_name
     * @param int    $id_smtp
     * @param bool   $send_method
     *
     * @return json
     */
    public function sendTestNewsletter($email, $template_name = null, $id_smtp = null, $send_method = null, $id_lang = null)
    {
        $response = NewsletterProAjaxResponse::newInstance([
            'status' => false,
            'msg' => '',
        ]);

        if (!isset($id_lang)) {
            $id_lang = pqnp_config('PS_LANG_DEFAULT');
        }

        try {
            if (Validate::isEmail($email)) {
                $template_name = isset($template_name) ? $template_name : pqnp_config('NEWSLETTER_TEMPLATE');

                $template = NewsletterProTemplate::newFile($template_name, [$email, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE])->load();

                if ($template->user) {
                    $this->setUserContext($template->user);
                }

                $message = $template->message(null, false, $id_lang, true);

                $title = $message['title'];
                $body = $message['body'];

                if ($template->user) {
                    if (!isset($id_smtp)) {
                        $connections = NewsletterProSendConnection::getConnections();
                        if (count($connections)) {
                            $id_smtp = $connections[0]['id_newsletter_pro_smtp'];
                        }
                    }

                    $this->setTemplateNameForAttachment($template->name);
                    $send = $this->sendNewsletter($title, $body, $template->user->to(), [
                        'user' => $template->user,
                        'id_smtp' => $id_smtp,
                        'send_method' => $send_method,
                    ]);

                    if (!is_array($send) && true == $send) {
                        $response->set('msg', NewsletterPro::getInstance()->l('Email sent'));
                    } elseif (is_array($send)) {
                        $response->addError(implode('<br>', $send));
                    } else {
                        $response->addError(NewsletterPro::getInstance()->l('Error sending test email'));
                    }
                } else {
                    $response->addError(NewsletterPro::getInstance()->l('Invalid user creation!'));
                }
            } else {
                $response->addError(NewsletterPro::getInstance()->l('The email is not not valid'));
            }
        } catch (Exception $e) {
            $response->addError($e->getMessage());
        }

        if (!$response->success()) {
            $response->set('msg', implode('<br>', $response->getErrors()));
        }

        return $response->display();
    }
}
