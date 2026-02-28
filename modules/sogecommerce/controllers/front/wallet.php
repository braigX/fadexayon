<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

class SogecommerceWalletModuleFrontController extends ModuleFrontController
{
    /**
     * @var Sogecommerce
     */
    public $module;

    /**
     * @var bool
     */
    public $display_column_right;

    /**
     * @var bool
     */
    public $display_column_left;

    public function initContent()
    {
        $this->display_column_right = false;
        $this->display_column_left = false;

        $context = Context::getContext();
        if (empty($context->customer->id)) {
            Tools::redirect('index.php');
        }

        parent::initContent();

        $showWallet = false;
        $showTokensOnly = false;

        $standard = new SogecommerceStandardPayment();

        if ($standard->isAvailable($context->cart) && $standard->isOneClickActive()) {
            $vars = $standard->getTplVars($context->cart, true);

            if (isset($vars['sogecommerce_rest_form_token']) && ! empty($vars['sogecommerce_rest_form_token'])) {
                $this->context->smarty->assign($vars);
                $showWallet = true;

                if (! $standard->isEmbedded() ||(Configuration::get('SOGECOMMERCE_STD_USE_WALLET') != 'True')) {
                    $showTokensOnly = true;
                }
            }
        }

        if (isset($this->context->cookie->sogecommerceIdentifierOperationSuccess)) {
            $this->success[] = $this->context->cookie->sogecommerceIdentifierOperationSuccess;
            $this->context->smarty->assign('sogecommerce_confirm_msg', $this->context->cookie->sogecommerceIdentifierOperationSuccess);

            unset($this->context->cookie->sogecommerceIdentifierOperationSuccess);
        } elseif ($this->context->cookie->sogecommerceCreateIdentifierError) {
            $this->errors[] = $this->context->cookie->sogecommerceCreateIdentifierError;
            $this->context->smarty->assign('sogecommerce_error_msg', $this->context->cookie->sogecommerceCreateIdentifierError);

            unset($this->context->cookie->sogecommerceCreateIdentifierError);
        }

        $this->context->smarty->tpl_vars['page']->value['body_classes']['page-customer-account'] = true;
        $this->context->smarty->assign('sogecommerce_show_wallet', $showWallet);
        $this->context->smarty->assign('sogecommerce_show_tokens_only', $showTokensOnly);

        $template = (version_compare(_PS_VERSION_, '1.7', '>=')) ? 'module:sogecommerce/views/templates/front/customer_wallet.tpl' : 'customer_wallet_bc.tpl';

        return $this->setTemplate($template);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->trans('My payment means', [], 'Modules.Sogecommerce.Customer_wallet'),
            'url' => $this->context->link->getModuleLink($this->module->name, 'wallet', [], true)
        ];

        return $breadcrumb;
    }
}
