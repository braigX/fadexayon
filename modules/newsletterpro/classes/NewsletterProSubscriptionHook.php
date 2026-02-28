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

class NewsletterProSubscriptionHook
{
    private $context;

    public static $hooks = [
        'displayFooterBefore',
        'displayFooter',
        'displayRightColumn',
        'displayLeftColumn',
    ];

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function getContent($tail = '')
    {
        $templateName = 'newsletter_subscribe_block';

        foreach ([$templateName.$tail, $templateName] as $name) {
            if (NewsletterProTools::hasTemplatePath(NewsletterProTools::module()->dir_location."views/templates/hook/{$name}.tpl")) {
                return $this->context->smarty->fetch(NewsletterProTools::loadTemplatePath($this->dir_location."views/templates/hook/{$name}.tpl"));
            }

            if (file_exists(NewsletterProTools::module()->dir_location.'views/templates/hook/'.NewsletterProTools::getVersion()."/{$name}.tpl")) {
                return $this->context->smarty->fetch(NewsletterProTools::module()->dir_location.'views/templates/hook/'.NewsletterProTools::getVersion()."/{$name}.tpl");
            }
        }

        return '';
    }

    public function display($hookName = null)
    {
        $output = new NewsletterProOutput();
        if ((bool) pqnp_config('SUBSCRIPTION_ACTIVE')) {
            $popupTypes = pqnp_config_get('SUBSCRIPTION_HOOK_POPUP_TYPE', array_fill_keys(NewsletterProSubscriptionHook::getHooksUpper(), 0));

            $upperHookName = Tools::strtoupper($hookName);

            if (array_key_exists($upperHookName, $popupTypes) && true == (bool) $popupTypes[$upperHookName]) {
                $output->append('<div class="pqnp-subscription-content"></div>');
            } else {
                $lowerHookName = Tools::strtolower(preg_replace('/([A-Z])/', '_$1', $hookName));

                if (isset($hookName)) {
                    $this->context->smarty->assign([
                        'display_hook' => Tools::strtolower(preg_replace('/([A-Z])/', '-$1', $hookName)),
                    ]);
                }
                $output->append($this->getContent('_'.$lowerHookName));
            }
        }

        return $output->render();
    }

    public static function getHooksUpper()
    {
        $hooks = [];

        foreach (self::$hooks as $key => $value) {
            $hooks[$key] = Tools::strtoupper($value);
        }

        return $hooks;
    }

    public static function convertToUpper($hooks)
    {
        $newHooks = [];
        foreach ($hooks as $key => $value) {
            $newHooks[Tools::strtoupper($key)] = $value;
        }

        return $newHooks;
    }
}
