<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPAjaxPsModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->processAjax();
    }

    protected function processAjax()
    {
        $context = Context::getContext();
        $prestawp = $this->module;

        if (Tools::getValue('action') == 'getPosts') {
            $content = '';
            $type = Tools::getValue('type');

            if ($type == 'product') {
                $id_product = Tools::getValue('id_product');
                $prestawp->ajax_product = false;
                $content = $prestawp->hookPSWPproduct(
                    ['posts_only' => true, 'id_product' => $id_product, 'hook' => $prestawp->hook_product]
                );
            } elseif ($type == 'block') {
                $id_block = Tools::getValue('id_block');
                $block = new PSWPBlock($id_block);
                if (Validate::isLoadedObject($block)) {
                    $block->ajax_load = false;

                    $context->smarty->assign([
                        'pswp_block' => $block,
                        'posts' => $block->getPostsFront(),
                        'psv' => $prestawp->getPSVersion(),
                        'psvd' => $prestawp->getPSVersion(true),
                        'psvwd' => $prestawp->getPSVersion(true),
                        'pswp_blank' => $prestawp->open_blank,
                        'pswp_wp_path' => $prestawp->getWPPath(),
                        'pswp_enable_posts_page' => $prestawp->enable_posts_page,
                        'pswp_posts_page_url' => $prestawp->getModuleLink($prestawp->name, 'list'),
                        'pswp_theme' => $prestawp->getCurrentThemeName(),
                    ]);
                    $content = $context->smarty->fetch(
                        _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/_block_posts.tpl'
                    );
                }
            } else {
                $prestawp->ajax = false;
                $content = $prestawp->hookPSWPposts(['posts_only' => true]);
            }

            exit($content);
        }

        exit;
    }
}
