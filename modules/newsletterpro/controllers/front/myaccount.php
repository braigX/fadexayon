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

class NewsletterProMyAccountModuleFrontController extends ModuleFrontController
{
    /**
     * @var NewsletterPro
     */
    public $module;

    private $translate;

    public function __construct()
    {
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = true;
        }

        parent::__construct();

        $this->translate = new NewsletterProTranslate(pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $page['body_classes'] = array_merge($page['body_classes'], [
            'page-customer-account' => true,
        ]);

        return $page;
    }

    public function initContent()
    {
        $this->display_column_left = false;
        parent::initContent();

        $this->module = Module::getInstanceByName('newsletterpro');

        if (!Validate::isLoadedObject($this->module)) {
            Tools::redirect('index.php');
        }

        if (!$this->context->customer->isLogged(true)) {
            Tools::redirect('index.php?controller=authentication&redirect=module&module=newsletterpro&back='.urlencode($this->module->my_account_url));
        }

        if (!$this->isFeatureActivated()) {
            Tools::redirect('index.php');
        }

        $is_subscribed = (bool) Db::getInstance()->getValue(
            '
			SELECT `newsletter` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int) $this->context->customer->id
        );

        $this->context->smarty->assign([
            'id_module' => $this->module->id,
            'tpl_location' => $this->module->dir_location.'views/',
            'my_account_url' => $this->module->my_account_url,
            'is_subscribed' => $is_subscribed,
            'list_of_interest' => NewsletterProListOfInterest::getListActiveCustomer($this->context->customer->id),
            'category_tree' => $this->getCategoryTree(),
            'subscribe_by_category_active' => (bool) pqnp_config('SUBSCRIBE_BY_CATEGORY'),
            'customer_subscribe_by_loi_active' => (bool) pqnp_config('CUSTOMER_SUBSCRIBE_BY_LOI'),
        ]);

        $this->context->smarty->assign([
            'subscribed_categories' => NewsletterProCustomerCategory::getCategoriesByIdCustomer($this->context->customer->id),
        ]);

        if (NewsletterProTools::is17()) {
            $this->setTemplate('module:newsletterpro/views/templates/front/1.7/my_account.tpl');
        } elseif ($this->module->isPS16()) {
            $this->setTemplate('1.6/my_account.tpl');
        } else {
            $this->setTemplate('1.5/my_account.tpl');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (NewsletterProTools::is17()) {
            $this->context->controller->addCSS([
                $this->module->uri_location.'views/css/1.7/my_account.css',
            ]);
        } else {
            $this->context->controller->addCSS([
                $this->module->uri_location.'views/css/my_account.css',
            ]);
        }
    }

    public function isFeatureActivated()
    {
        return (bool) pqnp_config('DISPLYA_MY_ACCOUNT_NP_SETTINGS');
    }

    public function getCategoryTree()
    {
        $root = Category::getRootCategory();
        $tab_root = [
            'id_category' => $root->id,
            'name' => $root->name,
        ];

        $customer_category = NewsletterProCustomerCategory::getInstanceByCustomerId((int) $this->context->customer->id);
        $selected_cat = [];
        if (Validate::isLoadedObject($customer_category)) {
            $selected_cat = $customer_category->getCategories();
        }

        $category_tree = $this->module->renderCategoryTree([
            'root' => $tab_root,
            'selected_cat' => $selected_cat,
            'input_name' => 'categoryBox',
            'use_radio' => false,
            'disabled_categories' => [],
            'use_search' => true,
            'use_in_popup' => false,
            'use_shop_context' => true,
            'ajax_request_url' => Context::getContext()->link->getModuleLink(NewsletterProTools::module()->name, 'ajax', []),
        ]);

        return $category_tree;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitNewsletterProSettings')) {
            $newsletter = (Tools::isSubmit('newsletter') ? (int) Tools::getValue('newsletter') : 0);

            if ((bool) pqnp_config('CUSTOMER_SUBSCRIBE_BY_LOI')) {
                $list_of_interest = Tools::getValue('list_of_interest');

                $customer_loi = NewsletterProCustomerListOfInterests::getInstanceByCustomerId((int) $this->context->customer->id);

                if (!empty($list_of_interest)) {
                    $customer_loi->setCategories($list_of_interest);
                    $customer_loi->id_customer = (int) $this->context->customer->id;

                    if (!$customer_loi->save()) {
                        $this->errors[] = $this->translate->l('Error on updating the list of interests.');
                    } else {
                        $subscriber = NewsletterProSubscribers::getInstanceByEmail($this->context->customer->email, (int) $this->context->shop->id);
                        if (Validate::isLoadedObject($subscriber)) {
                            $subscriber->setListOfInterest($list_of_interest);
                            $subscriber->update();
                        }
                    }
                } else {
                    if (Validate::isLoadedObject($customer_loi)) {
                        $customer_loi->setCategories([]);
                        $customer_loi->update();
                    }
                }
            }

            if (empty($this->errors)) {
                if ((bool) $newsletter) {
                    NewsletterProSubscriptionManager::newInstance()->subscribe($this->context->customer->email, (int) $this->context->shop->id, true);
                } else {
                    NewsletterProSubscriptionManager::newInstance()->unsubscribe($this->context->customer->email, (int) $this->context->shop->id, true);
                }
            }

            if ((bool) pqnp_config('SUBSCRIBE_BY_CATEGORY')) {
                $category_box = Tools::isSubmit('categoryBox') && is_array(Tools::getValue('categoryBox')) ? Tools::getValue('categoryBox') : [];

                if (empty($this->errors)) {
                    if (Tools::isSubmit('subscribed_categories')) {
                        $subscribed_categories = Tools::getValue('subscribed_categories');
                        if (Tools::strlen($subscribed_categories) > 0) {
                            $subscribed_categories = explode(',', $subscribed_categories);

                            foreach ($subscribed_categories as $id_category) {
                                $count = (int) Db::getInstance()->getValue('
									SELECT COUNT(*) FROM `'._DB_PREFIX_.'category`
									WHERE `id_category` = '.(int) $id_category.'
								');

                                if ($count > 0) {
                                    $category_box[] = $id_category;
                                }
                            }
                        }
                    }

                    $customer_category = NewsletterProCustomerCategory::getInstanceByCustomerId((int) $this->context->customer->id);
                    $customer_category->setCategories($category_box);
                    $customer_category->id_customer = (int) $this->context->customer->id;
                    if ($customer_category->save()) {
                        $this->context->customer->newsletter = $newsletter;
                    } else {
                        $this->errors[] = $this->translate->l('Error on updating the categories!');
                    }
                }
            }
        }
    }
}
