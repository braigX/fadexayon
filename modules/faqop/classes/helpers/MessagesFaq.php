<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';

class MessagesFaq extends BasicHelper
{
    public function getMultiLanguageInfoMsg()
    {
        $class = 'warning';
        $message = $this->module->getMultilangMessage();
        Context::getContext()->smarty->assign(
            [
                'class' => $class,
                'message' => $message,
            ]
        );

        return $this->module->displayMessage();
    }

    public function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            $class = 'warning';
            $message = $this->module->getMultishopMessage();
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $message,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getShopContextError($shop_contextualized_name, $mode)
    {
        if (is_array($shop_contextualized_name)) {
            $shop_contextualized_name = implode('<br/>', $shop_contextualized_name);
        }

        if ($mode == 'edit') {
            $class = 'danger';
            $message = $this->module->getShopContextErrorMessageOne($shop_contextualized_name);
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $message,
                ]
            );

            return $this->module->displayMessage();
        } else {
            $class = 'danger';
            $message = $this->module->getShopContextErrorMessageTwo();
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $message,
                ]
            );

            return $this->module->displayMessage();
        }
    }

    public function getCurrentShopInfoMsg()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->module->getCurrentShopInfoMsg();
            }

            $class = 'info';
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $shop_info,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getErrorMsg($message)
    {
        $class = 'danger';

        Context::getContext()->smarty->assign(
            [
                'class' => $class,
                'message' => $message,
            ]
        );

        return $this->module->displayMessage();
    }

    public function getWarningMultishopAboutItem()
    {
        $class = 'warning';
        $message = $this->module->getWarningMultishopAboutItem();

        Context::getContext()->smarty->assign(
            [
                'class' => $class,
                'message' => $message,
            ]
        );

        return $this->module->displayMessage();
    }

    public function getCurrentShopInfoMsgBlock($back)
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->module->getCurrentShopInfoMsgBlock($back);
            }

            $class = 'info';
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $shop_info,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getCurrentShopInfoMsgItemBlock()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->module->getCurrentShopInfoMsgItemBlock();
            }

            $class = 'info';
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $shop_info,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getInfoMultishopAboutItem()
    {
        if (Shop::isFeatureActive()) {
            $class = 'info';
            $message = $this->module->getInfoMultishopAboutItem();

            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $message,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getCurrentShopInfoMsgPage()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->module->getCurrentShopInfoMsgPage();
            }

            $class = 'info';
            Context::getContext()->smarty->assign(
                [
                    'class' => $class,
                    'message' => $shop_info,
                ]
            );

            return $this->module->displayMessage();
        } else {
            return '';
        }
    }

    public function getCreateListInfoMessage()
    {
        $class = 'info';
        $message = $this->module->getCreateListInfoMessage();

        Context::getContext()->smarty->assign(
            [
                'class' => $class,
                'message' => $message,
            ]
        );

        return $this->module->displayMessage();
    }

    public function getItemInfoMessage()
    {
        $class = 'info';
        $message = $this->module->getItemInfoMessage();

        Context::getContext()->smarty->assign(
            [
                'class' => $class,
                'message' => $message,
            ]
        );

        return $this->module->displayMessage();
    }
}
