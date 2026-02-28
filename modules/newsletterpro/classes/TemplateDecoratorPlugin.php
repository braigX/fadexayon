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

namespace PQNP;

if (!defined('_PS_VERSION_')) {
    exit;
}


use NewsletterProEmbedImages;
use NewsletterProHtmlToText;
use NewsletterProMailAttachment;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use Swift_MimePart;

class TemplateDecoratorPlugin implements Swift_Events_SendListener, TemplateDecoratorReplacementInterface
{
    private $_replacements;

    public function __construct($replacements)
    {
        $this->setReplacements($replacements);
    }

    public static function newInstance($replacements)
    {
        return new self($replacements);
    }

    public function setReplacements($replacements)
    {
        if (!($replacements instanceof TemplateDecoratorReplacementInterface)) {
            $this->_replacements = (array) $replacements;
        } else {
            $this->_replacements = $replacements;
        }
    }

    public function getReplacements()
    {
        return $this->_replacements;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $to = array_keys($message->getTo());
        $address = array_shift($to);

        $template = $this->getTemplateFor($address);

        if (!$template) {
            throw new \Exception('Invalid sending template');
        }

        $body_final = $template['body'];
        $subject_final = $template['title'];

        // set to email and the full name
        $message->setTo($this->getTo());
        $message->setSubject($subject_final);

        $content = [];
        $embed = new NewsletterProEmbedImages($body_final);
        $body_final = $embed->getTemplate();
        $attachments = NewsletterProMailAttachment::get($this->getTemplate()->name);
        $content[] = (new Swift_MimePart($body_final, 'text/html'));
        if ((bool) pqnp_config('EMAIL_MIME_TEXT')) {
            $content[] = new Swift_MimePart(NewsletterProHtmlToText::convert($body_final), 'text/plain');
        }

        $message->setChildren(array_merge($content, $embed->getImages(), $attachments));
    }

    public function getTemplateFor($address = null)
    {
        if ($this->_replacements instanceof TemplateDecoratorReplacementInterface) {
            return $this->_replacements->getTemplateFor($address);
        } else {
            return isset($this->_replacements[$address])
                ? $this->_replacements[$address]
                : null;
        }
    }

    public function getTemplate()
    {
        if ($this->_replacements instanceof TemplateDecoratorReplacementInterface) {
            return $this->_replacements->template;
        }
    }

    public function getTo()
    {
        if ($this->_replacements instanceof TemplateDecoratorReplacementInterface) {
            return $this->_replacements->template->user->to();
        }
    }

    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }
}
