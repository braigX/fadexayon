<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2018 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

class IdxOption
{
    public $id = null;
    public $id_component = null;
    public $name = null;
    public $description = null;
    public $img_ext = 'png';
    public $price_impact_type;
    public $price_impact = 0;
    public $price_impact_wodiscount;
    public $price_impact_calc;
    public $weight_impact = 0;
    public $reference = null;
    public $attach_product_type;
    public $attach_product = 'none';
    public $attach_product_qty = 0;
    public $attach_product_name = false;
    public $max_qty = 1000;
    public $tax_change = null;

    public function __construct($option = false)
    {
        if ($option) {
            $this->id = $option->id;
            $this->id_component = $option->id_component;
            $this->name = $option->name;
            $this->description = $option->description;
            $this->generateImpact();
        }
    }
    
    public function add()
    {
        $insert = array(
            'id_option' => (int) $this->id,
            'id_component' => (int) $this->id_component,
            'price_impact' => (float) $this->price_impact,
            'price_impact_wodiscount' => (float) $this->price_impact_wodiscount,
            'weight_impact' => (float) $this->weight_impact,
            'reference' => pSQL($this->reference),
            'att_product' => pSQL($this->attach_product),
            'att_qty' => (int) $this->attach_product_qty,
        );
        Db::getInstance()->insert('idxrcustomproduct_components_opt_impact', $insert);
    }
    
    public function generateImpact()
    {
        $impact_q = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_option = ' . (int)$this->id . ' and id_component = ' . (int)$this->id_component;
        $impact_row = Db::getInstance()->getRow($impact_q);
        $this->price_impact = $impact_row['price_impact'];
        $this->price_impact_wodiscount = $impact_row['price_impact_wodiscount'];
        $this->price_impact_type = $impact_row['price_impact_type'];
        $this->price_impact_calc = $impact_row['price_impact_calc'];
        $this->weight_impact = $impact_row['weight_impact'];
        $this->reference = $impact_row['reference'];
        $this->attach_product_type = $impact_row['attach_product_type'];
        $this->attach_product = $impact_row['att_product'];
        $this->attach_product_qty = $impact_row['att_qty'];
        $this->tax_change = $impact_row['taxchange'];
        if ($this->attach_product != 'none') {
            $this->setProductName();
        }
    }
    
    public function setProductName()
    {
        if ($this->attach_product) {
            $product_id = explode('_', $this->attach_product);
            $this->attach_product_name =  Product::getProductName($product_id[0], $product_id[1], Context::getContext()->language->id);
        }
    }
    
    public static function getTaxChange($id_component, $id_option)
    {
        $query = new DbQuery();
        $query->select('taxchange');
        $query->from('idxrcustomproduct_components_opt_impact');
        $query->where('id_component = '.(int)$id_component);
        $query->where('id_option = '.(int)$id_option);
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
    
    public static function fixOptionProducts()
    {
        $module = new IdxrCustomProduct();
        $token = Tools::getAdminTokenLite('AdminModules');
        $query = new DbQuery();
        $query->select('id_component, id_option, att_product');
        $query->from('idxrcustomproduct_components_opt_impact');
        $query->where("att_product != ''");
        $to_check = Db::getInstance()->executeS($query);
        $fixed_message = false;
        if ($to_check) {
            foreach ($to_check as $line) {
                $product_id = explode('_', $line['att_product'])[0];
                if (!Validate::isLoadedObject($product = new Product((int) $product_id))) {
                    $data = array(
                        'att_product' => '',
                        'att_qty' => 0
                    );
                    $where = 'id_component = '.(int)$line['id_component'].' and id_option = '.(int)$line['id_option'];
                    Db::getInstance()->update('idxrcustomproduct_components_opt_impact', $data, $where);
                    
                    $link = AdminController::$currentIndex.'&configure='.$module->name
                            .'&updatecomponent&token='.$token
                            .'&id_component='.(int)$line['id_component']
                            .'/#js_optionpanel_'.$line['id_component'].'_'.$line['id_option'];
                    $link_text = '<a href="'.$link.'">'.$module->l('Review', 'IdxOption').'</a>';
                    $fixed_message .= $module->l('Product fail, deattached from option', 'IdxOption').' '.$link_text.'<br/>';
                }
            }
        }
        return $fixed_message;
    }
}
