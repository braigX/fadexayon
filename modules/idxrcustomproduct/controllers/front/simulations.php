<?php
/**
 * 2007-2026 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innovadeluxe SL
 * @copyright 2026 Innovadeluxe SL
 *
 * @license   INNOVADELUXE
 */

class IdxrcustomproductSimulationsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password') {
            $this->setTemplate('notlogged.tpl');
            return;
        }

        $idCustomer = (int) $this->context->customer->id;
        $token = Configuration::get(Tools::strtoupper($this->module->name .'_TOKEN'));
        $rows = Db::getInstance()->executeS(
            'SELECT sc.id_saved_customisation, sc.id_product, sc.id_product_attribute, sc.customisation_name, sc.preview_html, sc.thumbnail_svg, sc.date_add
             FROM `' . _DB_PREFIX_ . 'idxrcustomproduct_saved_customisations` sc
             WHERE sc.id_customer = ' . $idCustomer . '
             ORDER BY sc.date_add DESC
             LIMIT 200'
        );

        if (!is_array($rows)) {
            $rows = array();
        }

        foreach ($rows as &$row) {
            $idProduct = (int) $row['id_product'];
            $row['product_name'] = Product::getProductName($idProduct);
            $row['product_link'] = $this->context->link->getProductLink($idProduct, null, null, null, null, null, (int) $row['id_product_attribute']);
            $row['use_product_link'] = $row['product_link'] . (strpos($row['product_link'], '?') === false ? '?' : '&') . 'idxr_restore_sim=' . (int) $row['id_saved_customisation'];
            $row['thumbnail_svg_b64'] = '';
            if (!empty($row['thumbnail_svg'])) {
                $row['thumbnail_svg_b64'] = base64_encode((string) $row['thumbnail_svg']);
            }
        }

        $this->context->controller->addJS($this->module->getLocalPath() . 'views/js/simulations.js');
        Media::addJsDef(array(
            'idxr_sim_url_ajax' => $this->context->link->getModuleLink($this->module->name, 'ajax', array('ajax' => true, 'token' => $token)),
            'idxr_sim_msg_rename' => $this->module->l('Enter a new name for this simulation'),
            'idxr_sim_msg_rename_error' => $this->module->l('Name cannot be empty'),
            'idxr_sim_msg_delete' => $this->module->l('Delete this simulation?'),
            'idxr_sim_msg_error' => $this->module->l('Action failed, please try again'),
        ));

        $this->context->smarty->assign(array(
            'simulations' => $rows,
        ));

        if (_PS_VERSION_ >= '1.7') {
            $this->setTemplate('module:idxrcustomproduct/views/templates/front/simulations17.tpl');
        } else {
            $this->setTemplate('simulations.tpl');
        }
    }
}
