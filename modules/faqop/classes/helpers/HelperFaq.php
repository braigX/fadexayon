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

class HelperFaq extends BasicHelper
{
    public function throwError($message)
    {
        $return = [];
        $return['message'] = $message;

        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        exit(json_encode($return));
    }

    public function startsWith($string, $startString)
    {
        $len = Tools::strlen($startString);

        return Tools::substr($string, 0, $len) === $startString;
    }

    public function saveToCookieCurrentAddress($cookie_name)
    {
        $current_url = $this->cleanUrl($_SERVER['REQUEST_URI']);

        try {
            Context::getContext()->cookie->__set($cookie_name, $current_url);
            Context::getContext()->cookie->write();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function saveToCookieItemsParent($parent)
    {
        try {
            Context::getContext()->cookie->__set(ConfigsFaq::ITEMS_PARENT_COOKIE, $parent);
            Context::getContext()->cookie->write();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function cleanUrl($url)
    {
        $url_split = explode('&', $url);
        foreach ($url_split as $key => $split) {
            if (preg_match('/(anticache)(.*?)/', $split)) {
                unset($url_split[$key]);
            }
            if (preg_match('/(conf=)(.*?)/', $split)) {
                unset($url_split[$key]);
            }
        }

        $result = implode('&', $url_split);

        return $result;
    }

    public function makeAnticache()
    {
        return rand(1, 1000000);
    }

    public function createAnticacheString()
    {
        return '&anticache=' . $this->makeAnticache();
    }

    public function registerHooks($hook_name)
    {
        $res = true;

        try {
            $hook_exists = $this->getHookIdByName($hook_name);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        // Here we add the hook to hooks in prestashop
        if (!Hook::isModuleRegisteredOnHook($this->module, $hook_name, Context::getContext()->shop->id)) {
            try {
                $res &= Hook::registerHook($this->module, $hook_name);
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
        }
        // if the hook was newly created
        if (!$hook_exists) {
            $res &= $this->module->rep->addCustomHookToTable($hook_name);
        }

        return $res;
    }

    public function unregisterHooks($hook_name)
    {
        $res = true;
        if (!$this->module->rep->getOtherBlocksInHook($hook_name)) {
            $res &= Hook::unregisterHook($this->module, $hook_name);
        }

        return $res;
    }

    public function isCurrentShopChosen()
    {
        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_GROUP
                || Shop::getContext() == Shop::CONTEXT_ALL)) {
            return false;
        }

        return true;
    }

    public function getBlocksListUrl()
    {
        if (Tools::getValue('op_type') == 'list') {
            if (Context::getContext()->cookie->__isset(ConfigsFaq::BLOCKS_COOKIE)) {
                $redirectUrl = Context::getContext()->cookie->__get(ConfigsFaq::BLOCKS_COOKIE) .
                    $this->createAnticacheString();
            } else {
                $redirectUrl = $this->getCleanBlocksListUrl();
            }
        } else {
            $redirectUrl = $this->getCleanBlocksListUrl('AdminFaqop');
        }

        return $redirectUrl;
    }

    public function getCleanBlocksListUrl($controller = 'AdminFaqopListsList')
    {
        try {
            return Context::getContext()->link->getAdminLink($controller) .
                $this->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        return null;
    }

    public function getItemsListUrl()
    {
        if (Context::getContext()->cookie->__isset(ConfigsFaq::ITEMS_COOKIE)) {
            $redirectUrl = Context::getContext()->cookie->__get(ConfigsFaq::ITEMS_COOKIE) .
                $this->createAnticacheString();
        } else {
            $redirectUrl = $this->getCleanItemsListUrl();
        }

        return $redirectUrl;
    }

    public function getCleanItemsListUrl()
    {
        $href = null;
        if (Tools::isSubmit('op_type') && Tools::isSubmit('id_list')) {
            $type_op = Tools::getValue('op_type');
            $id_list = Tools::getValue('id_list');
            $type_op_c = Tools::ucfirst($type_op);

            try {
                $this->back = Context::getContext()->link->getAdminLink('AdminFaqopAddItems' . $type_op_c) .
                    '&id_list=' . (int) $id_list .
                    '&op_type=' . $type_op .
                    '&edit=1' .
                    $this->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
        } else {
            try {
                $href = Context::getContext()->link->getAdminLink('AdminFaqopItemsList') .
                    $this->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
        }

        return $href;
    }

    public function getItemsParent()
    {
        if (Context::getContext()->cookie->__isset(ConfigsFaq::ITEMS_PARENT_COOKIE)) {
            $res = Context::getContext()->cookie->__get(ConfigsFaq::ITEMS_PARENT_COOKIE);
        } else {
            $res = $this->getCleanItemsParent();
        }

        return $res;
    }

    public function getCleanItemsParent()
    {
        $res = null;
        if (Tools::isSubmit('op_type') && Tools::isSubmit('id_list')) {
            $res = Tools::getValue('op_type');
        } else {
            $res = 'items';
        }

        return $res;
    }

    public function getCleanItemsAddReadyUrl($id_list, $op_type)
    {
        try {
            return Context::getContext()->link->getAdminLink('AdminFaqopAddReadyItem') .
                '&id_list=' . $id_list .
                '&op_type=' . $op_type .
                $this->createAnticacheString();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        return null;
    }

    public function checkIfSubmitClicked()
    {
        return Tools::isSubmit('submitBlock') || Tools::isSubmit('submitStay');
    }

    public function hasBadWordsInHook($name_hook)
    {
        $is_admin_hook = stripos($name_hook, 'Admin');
        if ($is_admin_hook !== false) {
            $is_admin_hook = true;
        }
        $is_object_hook = stripos($name_hook, 'Object');
        if ($is_object_hook !== false) {
            $is_object_hook = true;
        }
        $is_update_hook = stripos($name_hook, 'Update');
        if ($is_update_hook !== false) {
            $is_update_hook = true;
        }
        $is_delete_hook = stripos($name_hook, 'Delete');
        if ($is_delete_hook !== false) {
            $is_delete_hook = true;
        }
        $is_validate_hook = stripos($name_hook, 'Validate');
        if ($is_validate_hook !== false) {
            $is_validate_hook = true;
        }
        $is_save_hook = stripos($name_hook, 'Save');
        if ($is_save_hook !== false) {
            $is_save_hook = true;
        }
        $is_action_hook = stripos($name_hook, 'action');
        if ($is_action_hook !== false) {
            $is_action_hook = true;
        }
        $is_filter_hook = stripos($name_hook, 'filter');
        if ($is_filter_hook !== false) {
            $is_filter_hook = true;
        }
        $is_hook_hook = stripos($name_hook, 'hook');
        if ($is_hook_hook !== false) {
            $is_hook_hook = true;
        }

        if ($is_admin_hook || $is_object_hook || $is_update_hook
            || $is_delete_hook || $is_validate_hook || $is_save_hook
            || $is_action_hook || $is_filter_hook || $is_hook_hook
        ) {
            return true;
        }

        return false;
    }

    public function composeShortcode($id)
    {
        return ConfigsFaq::SHORTCODE_NAME . ':' . $id;
    }

    public function makeNavTabsUrls($id)
    {
        $constantPart = '&edit=1' .
            '&id_list=' . (int) $id .
            '&op_type=list' .
            $this->createAnticacheString();
        $array = [
            'items_url' => Context::getContext()->link->getAdminLink('AdminFaqopAddItemsList') . $constantPart,
            'general_url' => Context::getContext()->link->getAdminLink('AdminFaqopList') . $constantPart,
            'position_url' => Context::getContext()->link->getAdminLink('AdminFaqopHookList') . $constantPart,
            'styles_url' => Context::getContext()->link->getAdminLink('AdminFaqopStylesList') . $constantPart,
        ];

        return $array;
    }

    public function makeNavTabsPageUrls($id)
    {
        $constantPart = $this->getNavTabsPagesUrlsConstantPart($id);
        $array = [
            'items_url' => $this->getAddItemsToPageUrl($id),
            'general_url' => Context::getContext()->link->getAdminLink('AdminFaqopPage') . $constantPart,
            'styles_url' => Context::getContext()->link->getAdminLink('AdminFaqopStylesPage') . $constantPart,
        ];

        return $array;
    }

    public function getAddItemsToPageUrl($id)
    {
        $constantPart = $this->getNavTabsPagesUrlsConstantPart($id);

        return Context::getContext()->link->getAdminLink('AdminFaqopAddItemsPage') . $constantPart;
    }

    public function getNavTabsPagesUrlsConstantPart($id)
    {
        return '&edit=1' .
            '&id_list=' . (int) $id .
            '&op_type=page' .
            $this->createAnticacheString();
    }

    public function makeItemNavTabsUrls($id)
    {
        $constantPart = '&edit=1' .
            '&id_item=' . (int) $id .
            $this->createAnticacheString();
        $array = [
            'general_url' => Context::getContext()->link->getAdminLink('AdminFaqopItem') . $constantPart,
            'styles_url' => Context::getContext()->link->getAdminLink('AdminFaqopStylesItem') . $constantPart,
            'bindings_url' => Context::getContext()->link->getAdminLink('AdminFaqopBindItem') . $constantPart,
        ];

        return $array;
    }

    public function getHooks()
    {
        $hooks = Hook::getHooks();
        foreach ($hooks as $key => $hook_item) {
            $name_hook = $hook_item['name'];

            if ($this->hasBadWordsInHook($name_hook)) {
                unset($hooks[$key]);
            }
        }

        return $hooks;
    }

    // use only this method to get hook id
    public function getHookIdByName($hook_name)
    {
        try {
            return Hook::getIdByName($hook_name, true, true);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();

            return false;
        }
    }

    public function isClassName($class_name)
    {
        $res = true;
        $array = explode(' ', $class_name);
        $new_array = array_filter($array, function ($element) {
            return !empty($element);
        });
        foreach ($new_array as $class) {
            $res &= preg_match('/^[a-z][a-z0-9_-]+$/', $class);
        }

        return $res;
    }

    public function explodeImplode($class_name)
    {
        $array = explode(' ', $class_name);
        $new_array = array_filter($array, function ($element) {
            return !empty($element);
        });

        return implode(' ', $new_array);
    }

    public function getShopsWithoutCurrent()
    {
        $shops = Shop::getShops(true, null);
        $result = [];
        foreach ($shops as $shop) {
            if ($shop['id_shop'] != Context::getContext()->shop->id) {
                $result[] = $shop;
            }
        }

        return $result;
    }

    public function getInstallHelper($ih = null)
    {
        if (is_null($ih)) {
            require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/InstallHelper.php';
            $ih = new InstallHelper($this->module);
        }

        return $ih;
    }

    public function setItemTitle($question, $title = null)
    {
        if (!empty($title)) {
            if (Tools::strlen($title) > 55) {
                $title = Tools::substr($title, 0, 55) . '...';
            }
        } elseif (Tools::strlen($question) > 55) {
            $title = Tools::substr($question, 0, 55) . '...';
        } else {
            $title = $question;
        }

        return $title;
    }

    public function renderPageNavTabs($active_url, $id_list)
    {
        $content = '';

        $urls = $this->module->helper->makeNavTabsPageUrls($id_list);

        $content .= $this->module->displayNavTabsPage(
            $active_url,
            $urls['items_url'],
            $urls['general_url'],
            $urls['styles_url']
        );

        return $content;
    }

    public function renderBlockNavTabs($active_url, $id_list)
    {
        $content = '';

        $urls = $this->makeNavTabsUrls($id_list);
        $content .= $this->module->displayNavTabs(
            $active_url,
            $urls['items_url'],
            $urls['general_url'],
            $urls['position_url'],
            $urls['styles_url']
        );

        return $content;
    }

    public function getCurrrentShopMessage($op_type, $back)
    {
        if ($op_type == 'list') {
            return $this->module->mes->getCurrentShopInfoMsgBlock($back);
        }

        return $this->module->mes->getCurrentShopInfoMsgPage();
    }

    public function getIsMultistoreActive()
    {
        return Shop::isFeatureActive() && count(Shop::getShops(true, null, true)) > 1;
    }

    public function createOrUpdatePage(
        $id_shop,
        $show_title,
        $show_description,
        $show_markup,
        $accordion = 0,
        $title = null,
        $description = null,
        $title_tag = null,
        $block_tag = null,
        $block_class = null,
        $title_class = null,
        $content_tag = null,
        $content_class = null,
        $item_tag = null,
        $item_class = null,
        $question_tag = null,
        $question_class = null,
        $answer_tag = null,
        $answer_class = null,
        $description_tag = null,
        $description_class = null
    ) {
        $res = true;
        $id_page = $this->module->rep->getPageIdByShop($id_shop);
        if (!empty($id_page)) {
            $page = new OpFaqPage($this->module, $id_page);
        } else {
            $page = new OpFaqPage($this->module);
        }
        $page->active = 1;
        $page->block_type = 'page';
        $page->show_title = (int) $show_title;
        $page->show_markup = (int) $show_markup;
        $page->show_description = (int) $show_description;
        $page->id_shop = (int) $id_shop;
        $page->accordion = (int) $accordion;

        if (!empty($title) && !empty($description)) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $page->title[$language['id_lang']] = $title;
                $page->description[$language['id_lang']] = $description;
            }
        }

        if (empty($id_page)) {
            $res &= $page->add();
        }

        $page->hook_name = ConfigsFaq::PAGE_HOOK;

        if (!empty($block_tag)) {
            $page->block_tag = pSQL($block_tag);
        }

        if (!empty($block_class)) {
            $page->block_class = pSQL($block_class);
        }

        if (!empty($title_tag)) {
            $page->title_tag = pSQL($title_tag);
        }

        if (!empty($title_class)) {
            $page->title_class = pSQL($title_class);
        }

        if (!empty($content_tag)) {
            $page->content_tag = pSQL($content_tag);
        }

        if (!empty($content_class)) {
            $page->content_class = pSQL($content_class);
        }

        if (!empty($item_tag)) {
            $page->item_tag = pSQL($item_tag);
        }

        if (!empty($item_class)) {
            $page->item_class = pSQL($item_class);
        }

        if (!empty($question_tag)) {
            $page->question_tag = pSQL($question_tag);
        }

        if (!empty($question_class)) {
            $page->question_class = pSQL($question_class);
        }

        if (!empty($answer_tag)) {
            $page->answer_tag = pSQL($answer_tag);
        }

        if (!empty($answer_class)) {
            $page->answer_class = pSQL($answer_class);
        }

        if (!empty($description_tag)) {
            $page->description_tag = pSQL($description_tag);
        }

        if (!empty($description_class)) {
            $page->description_class = pSQL($description_class);
        }

        $res &= $page->update();

        return $res;
    }

    public function getShopsListWithSettings()
    {
        $shops = $this->getShopsWithoutCurrent();
        $newShops = [];

        foreach ($shops as $shop) {
            $id_page = $this->module->rep->getPageIdByShop($shop['id_shop']);
            if ($id_page) {
                $shop['id_page'] = $id_page;
                $newShops[] = $shop;
            }
        }

        return $newShops;
    }

    public static function sanitizeIntsFromString($ints)
    {
        return implode(',', array_map('intval', explode(',', $ints)));
    }

    public static function sanitizeIntsFromArray($ints)
    {
        return implode(',', array_map('intval', $ints));
    }
}
