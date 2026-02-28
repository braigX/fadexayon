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

use PQNP\TemplateDecoratorReplacementInterface;

class NewsletterProTemplateDecoratorReplacements implements TemplateDecoratorReplacementInterface
{
    public $template;

    public function __construct(NewsletterProTemplate $template)
    {
        $this->template = $template;
    }

    public static function newInstance(NewsletterProTemplate $template)
    {
        return new self($template);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getTemplateFor($email = null)
    {
        return $this->template->message($email);
    }
}
