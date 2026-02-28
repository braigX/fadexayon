<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2020 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

include('IdxOption.php');

class IdxComponent
{

    public $id_component;
    public $name;
    public $type;
    public $optional;
    public $columns;
    public $zoom;
    public $color;
    public $show_price;
    public $default_opt = -1;
    public $parent = false;
    public $title_lang = array();
    public $description_lang = array();
    public $options_lang = array();
    public $module;
    public $has_constraints = false;
    public $icon_preview = false;
    public $multivalue = "unique";
    //only for type file
    public $size;
    public $allowed_extensions = array();

    public function __construct($id_component = null, $full = false, $lang = false)
    {
        if ($id_component) {
            $this->id_component = (int) $id_component;
            if ($full) {
                $this->fillObject($lang);
            }
        }
    }

    public function fillObject($lang = false)
    {
        $q = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components '
                . 'where id_component = ' . (int) $this->id_component;
        $db_row = Db::getInstance()->getRow($q);
        $this->id_component = $db_row['id_component'];
        $this->name = $db_row['name'];
        $this->type = $db_row['type'];
        $this->optional = $db_row['optional'];
        $this->columns = $db_row['columns'];
        $this->zoom = $db_row['zoom'];
        $this->show_price = $db_row['show_price'];
        $this->color = $db_row['color'];
        $this->default_opt = $db_row['default_opt'];
        $this->parent = $db_row['parent'];
        $this->multivalue = $db_row['multivalue'];
        $this->generateOptions($lang);
    }

    public function getOptions($lang_id = false)
    {
        if (!$lang_id) {
            return $this->options_lang;
        } else {
            return $this->option_lang[$lang_id];
        }
    }

    public function generateOptions($id_lang = false)
    {
        $lang_q = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $this->id_component;
        if ($id_lang) {
            $lang_q .= ' and id_lang = ' . (int) $id_lang;
        }
        $languages = Db::getInstance()->executeS($lang_q);
        if ($this->type == 'file') {
            $values = array_shift($languages);
            if ($values && $values['json_values']) {
                $values_raw = json_decode($values['json_values']);
                $this->size = $values_raw->size;
                $this->allowed_extensions = $values_raw->allowed_extension;
            }
        } else {
            foreach ($languages as $language) {
                $lang = array();
                $this->title_lang[$language['id_lang']] = $lang['title'] = $language['title'];
                $this->description_lang[$language['id_lang']] = $lang['description'] = $language['description'];
                $lang['options'] = array();
                $options_raw = json_decode($language['json_values']);
                if ($options_raw) {
                    if (isset($options_raw->options)) {
                        foreach ($options_raw->options as $option_raw) {
                            $option_raw->id_component = $this->id_component;
                            $option = new IdxOption($option_raw);
                            $lang['options'][] = $option;
                        }
                    }
                    $this->options_lang[$language['id_lang']] = $lang;
                }
            }
        }
    }

    public function add()
    {
        $main_data = $this->getMainTableArray();
        Db::getInstance()->insert('idxrcustomproduct_components', $main_data);
        $this->id_component = Db::getInstance()->Insert_ID();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $data_lang = array(
                'id_component' => (int) $this->id_component,
                'id_lang' => (int) $lang['id_lang'],
                'title' => isset($this->title_lang[$lang['id_lang']]) ? pSQL($this->title_lang[$lang['id_lang']]) : '',
                'description' => isset($this->description_lang[$lang['id_lang']]) ? pSQL($this->description_lang[$lang['id_lang']]) : ''
            );
            Db::getInstance()->insert('idxrcustomproduct_components_lang', $data_lang);
            unset($data_lang);
        }

        return $this->id_component;
    }

    public function update()
    {
        $data = $this->getMainTableArray();
        $where = 'id_component = ' . (int) $this->id_component;
        Db::getInstance()->update('idxrcustomproduct_components', $data, $where);
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $data = array(
                'title' => pSQL($this->title_lang[$lang['id_lang']]),
                'description' => pSQL($this->description_lang[$lang['id_lang']],true)
            );
            $where = 'id_component = ' . (int) $this->id_component . ' and id_lang = ' . (int) $lang['id_lang'] . ';';
            if (Db::getInstance()->getValue('Select id_components_lang from '._DB_PREFIX_.'idxrcustomproduct_components_lang where '.$where)) {
                Db::getInstance()->update('idxrcustomproduct_components_lang', $data, $where);
            } else {
                $data['id_component'] = (int) $this->id_component;
                $data['id_lang'] = (int) $lang['id_lang'];
                Db::getInstance()->insert('idxrcustomproduct_components_lang', $data);
            }
        }
    }

    private function getMainTableArray()
    {
        $data = array(
            'name' => pSQL($this->name),
            'type' => pSQL($this->type),
            'optional' => (bool) $this->optional,
            'columns' => (int) $this->columns,
            'zoom' => (bool) $this->zoom,
            'show_price' => (bool) $this->show_price,
            'color' => pSQL($this->color),
            'default_opt' => (int) $this->default_opt,
            'parent' => (int) $this->parent,
            'multivalue' => pSQL($this->multivalue),
        );
        return $data;
    }

    public function clonar($index = 0)
    {
        $this->fillObject();

        if (!$index) {
            $new_name = $this->name . '_clon';
        } else {
            $new_name = $this->name . '_clon' . (int) $index;
        }
        $exist = Db::getInstance()->getValue('Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where name ="' . pSQL($new_name) . '"');
        if ($exist) {
            $index++;
            return $this->clonar($index);
        }

        $source = $this->id_component;
        unset($this->id_component);
        $this->name = $new_name;
        $new_component = $this->add();
        $source_icon = _PS_MODULE_DIR_ . 'idxrcustomproduct/img/icon/' . $source . '.png';
        if (file_exists($source_icon)) {
            $new_icon = _PS_MODULE_DIR_ . 'idxrcustomproduct/img/icon/' . $new_component . '.png';
            copy($source_icon, $new_icon);
        }

        Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang as t1
            inner join (
            select json_values, id_lang from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $source . ') as t2
            set t1.json_values = t2.json_values
            where t1.id_lang = t2.id_lang and t1.id_component = ' . (int) $new_component . ';');

        $image_file_base = _PS_IMG_DIR_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR;
        $json_options = Db::getInstance()->getValue('select json_values from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $source . ' ORDER BY LENGTH(json_values) DESC');
        $options = json_decode($json_options);
        foreach ($options->options as $option) {
            $image = $source . '_' . $option->id;
            if (file_exists($image_file_base . $image . '.png')) {
                copy($image_file_base . $image . '.png', $image_file_base . $new_component . '_' . $option->id . '.png');
            }
        }

        $impact = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $source);
        if ($impact) {
            foreach ($impact as $row) {
                unset($row['id_comp_opt']);
                $row['id_component'] = $new_component;
                Db::getInstance()->insert('idxrcustomproduct_components_opt_impact', $row);
            }
        }
        return $new_component;
    }

    public function delete()
    {
        $del_components = 'delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where id_component = ' . (int) $this->id_component . ';';
        $del_lang = 'delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $this->id_component . ';';
        $del_impact = 'delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $this->id_component . ';';
        $del_component_attr = 'delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_component_attribute where id_component = ' . (int) $this->id_component . ';';
        Db::getInstance()->execute($del_components);
        Db::getInstance()->execute($del_lang);
        Db::getInstance()->execute($del_impact);
        Db::getInstance()->execute($del_component_attr);
        IdxConfiguration::deleteBulkComponent($this->id_component);
        if (file_exists(_PS_MODULE_DIR_ . 'idxrcustomproduct/img/icon/' . $this->id_component . '.png')) {
            unlink(_PS_MODULE_DIR_ . 'idxrcustomproduct/img/icon/' . $this->id_component . '.png');
        }
        $path = _PS_IMG_DIR_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'options' . DIRECTORY_SEPARATOR;
        ;
        $files = glob($path . $this->id_component . '_*');
        foreach ($files as $file) {
            unlink($file);
        }
        if ($children = Db::getInstance()->executeS('Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where parent = ' . (int) $this->id_component)) {
            foreach ($children as $child) {
                $comp_child = new IdxComponent($child['id_component']);
                $comp_child->delete();
            }
        }
    }

    public function getComponentOptions()
    {
        $getjsonsql = 'Select icl.id_lang ,icl.json_values, ic.type, ic.default_opt from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang icl '
                . 'left join ' . _DB_PREFIX_ . 'idxrcustomproduct_components ic on icl.id_component = ic.id_component '
                . 'where icl.id_component = ' . (int) $this->id_component . ';';
        $results = Db::getInstance()->executeS($getjsonsql);
        $data = array();
        if ($results && count($results) > 0) {
            $data['type'] = $results[0]['type'];
            $data['lang'] = array();
            foreach ($results as $result) {
                $json_data = json_decode($result['json_values']);
                $options = array();
                if (isset($json_data->options)) {
                    foreach ($json_data->options as $option) {
                        $sql_impact = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $this->id_component . ' and id_option = ' . (int) $option->id;
                        $impact = Db::getInstance()->getRow($sql_impact);
                        $option->price_impact = (float) $impact['price_impact'];
                        $option->weight_impact = (float) $impact['weight_impact'];
                        $option->attach_product = ($impact['attach_product_type'] == 'base')?'base':$impact['att_product'];
                        $option->attach_product_qty = $impact['att_qty'];
                        $option->reference = $impact['reference'];
                        if ($option->id == $results[0]['default_opt']) {
                            $option->default = true;
                        } else {
                            $option->default = false;
                        }
                        $options[$option->id] = $option;
                    }
                    unset($json_data->options);
                    $json_data->options = $options;
                }

                $data['lang'][$result['id_lang']] = $json_data;
            }
        }
        return $data;
    }

    public static function getComponentTypes()
    {
        $module = Module::getInstanceByName('idxrcustomproduct');
        $types = array();
        $types[] = array(
            'id' => 'sel',
            'name' => $module->l('select', 'IdxComponent')
        );
        $types[] = array(
            'id' => 'sel_img',
            'name' => $module->l('select with images', 'IdxComponent')
        );
        $types[] = array(
            'id' => 'text',
            'name' => $module->l('text field', 'IdxComponent')
        );
        $types[] = array(
            'id' => 'file',
            'name' => $module->l('attach a file', 'IdxComponent')
        );
//        $components[] = array(
//            'id' => 'textarea',
//            'name' => $this->l('text area box', 'IdxComponent')
//        );
//        $components[] = array(
//            'id' => 'product',
//            'name' => $this->l('product additional', 'IdxComponent')
//        );
        return $types;
    }

    public static function getComponentColumns()
    {
        $module = Module::getInstanceByName('idxrcustomproduct');
        $columns = array();
        $columns[] = array(
            'id' => '12',
            'name' => $module->l('12 options', 'IdxComponent')
        );
        $columns[] = array(
            'id' => '6',
            'name' => $module->l('6 options', 'IdxComponent')
        );
        $columns[] = array(
            'id' => '4',
            'name' => $module->l('4 options', 'IdxComponent')
        );
        $columns[] = array(
            'id' => '3',
            'name' => $module->l('3 options', 'IdxComponent')
        );
        $columns[] = array(
            'id' => '2',
            'name' => $module->l('2 options', 'IdxComponent')
        );
        $columns[] = array(
            'id' => '1',
            'name' => $module->l('1 options', 'IdxComponent')
        );
        return $columns;
    }
    
    public static function getMultivalueTypes($multioptionblocked = false)
    {
        $module = Module::getInstanceByName('idxrcustomproduct');
        $mv_typès = array();
        $mv_typès[] = array(
            'id' => 'unique',
            'name' => $module->l('Unique value', 'IdxComponent')
        );
        $mv_typès[] = array(
            'id' => 'unique_qty',
            'name' => $module->l('Unique value with qty selector', 'IdxComponent')
        );
        if (!$multioptionblocked) {
            $mv_typès[] = array(
                'id' => 'multi_simple',
                'name' => $module->l('Multiple value', 'IdxComponent')
            );
            $mv_typès[] = array(
                'id' => 'multi_qty',
                'name' => $module->l('Multiple with qty selector', 'IdxComponent')
            );
        }
        return $mv_typès;
    }

    public static function hasConstraint($id_component, $id_configuration)
    {
        $sql = 'Select constraints_options '
                . 'from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations '
                . 'where id_configuration = ' . (int) $id_configuration;
        $actual_constraint = Db::getInstance()->getValue($sql);
        if (!$actual_constraint) {
            return false;
        }
        $constraint_array = explode(',', $actual_constraint);
        $contraints = array();
        foreach ($constraint_array as $constraint) {
            if (!$constraint) {
                continue;
            }
            $contraint_parts = explode('@', $constraint);
            if ($contraint_parts[0] == $id_component) {
                $contraints[] = $contraint_parts[1];
            }
        }
        return $contraints;
    }

    public static function generateFromProduct($id_product)
    {
        //Primero generamos el componente base que va a ser el padre del resto
        $product_name = Product::getProductName($id_product, 0, Context::getContext()->language->id);
        $parent = new IdxComponent();
        $parent->name = $product_name;
        $parent->type = 'product';
        $parent->add();
        $data = array(
            'id_component' => (int) $parent->id_component,
            'id_option' => 0,
            'att_product' => (int) $id_product
        );
        Db::getInstance()->insert('idxrcustomproduct_components_opt_impact', $data);
        //Por cada grupo de atributos creamos un componente con sus opciones
        $attr_groups = Db::getInstance()->executeS('
            select att.id_attribute_group from ' . _DB_PREFIX_ . 'product_attribute pa
            inner join ' . _DB_PREFIX_ . 'product_attribute_combination pac on pa.id_product_attribute = pac.id_product_attribute
            inner join ' . _DB_PREFIX_ . 'attribute att on pac.id_attribute = att.id_attribute
            inner join ' . _DB_PREFIX_ . 'attribute_group pag on att.id_attribute_group = pag.id_attribute_group
            where pa.id_product = ' . (int) $id_product . '
            group by att.id_attribute_group, pag.position  order by pag.position;');

        foreach ($attr_groups as $attr_group) {
            $attribute_group = new AttributeGroup($attr_group['id_attribute_group']);

            $attr_comp = new IdxComponent();
            $attr_comp->name = $product_name . ' - ' . $attribute_group->name[Context::getContext()->language->id];
            //if ($attribute_group->is_color_group) { //No da tiempo en este desarrollo
            //    $attr_comp->type = 'sel_img';
            //} else {
            $attr_comp->type = 'sel';
            //}
            $attr_comp->optional = false;
            $attr_comp->zoom = false;
            $attr_comp->columns = 4;
            $attr_comp->parent = $parent->id_component;
            $attr_comp->title_lang = $attribute_group->public_name;
            $attr_comp->add();

            $comp_attr = array(
                'id_component' => (int) $attr_comp->id_component,
                'id_attribute_group' => (int) $attr_group['id_attribute_group']
            );
            Db::getInstance()->insert('idxrcustomproduct_component_attribute', $comp_attr);

            $options = Db::getInstance()->executeS('select distinct pac.id_attribute from ' . _DB_PREFIX_ . 'product_attribute pa
                inner join ' . _DB_PREFIX_ . 'product_attribute_combination pac on pa.id_product_attribute = pac.id_product_attribute
                inner join ' . _DB_PREFIX_ . 'attribute att on pac.id_attribute = att.id_attribute
                where pa.id_product = ' . (int) $id_product . ' and att.id_attribute_group = ' . (int) $attr_group['id_attribute_group'] . ' order by att.position;');

            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $values = new stdClass();
                $values->options = array();

                foreach ($options as $option) {
                    $ps_option = new Attribute($option['id_attribute'], $lang['id_lang']);
                    $idxoption = new stdClass();
                    $idxoption->id = $option['id_attribute'];
                    $idxoption->name = addslashes($ps_option->name);
                    $idxoption->description = '';
                    $idxoption->img_ext = 'png';
                    $values->options[] = $idxoption;
                }
                $update = array(
                    'json_values' => json_encode($values, JSON_UNESCAPED_UNICODE)
                );
                $where = 'id_component = ' . (int) $attr_comp->id_component . ' and id_lang = ' . (int) $lang['id_lang'];
                Db::getInstance()->update('idxrcustomproduct_components_lang', $update, $where);
            }

            foreach ($options as $option) {
                $idx_option = new IdxOption();
                $idx_option->id = (int) $option['id_attribute'];
                $idx_option->id_component = (int) $attr_comp->id_component;
                $idx_option->add();
            }
        }
    }

    public static function getChildrenComponent($parent, $language = false)
    {
        if (!$language) {
            $language = Context::getContext()->language->id;
        }
        $childrens = array();
        $product_id = Db::getInstance()->getValue('Select att_product from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $parent);

        $components = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where parent = ' . (int) $parent);
        if ($components) {
            $childrens = IdxComponent::generateChildren($components, $parent, $language, $product_id);
        }

        return $childrens;
    }

    public static function generateChildren($components, $parent, $language, $product_id, $contraint = false, $attributes = false)
    {
        if (!isset($attributes) || !$attributes) {
            $attributes = array();
        }
        // Every component is a attribute_group from product
        $component = array_shift($components);
        $component_obj = new IdxComponent($component['id_component'], true, $language);
        $attribute_group = self::getAttributeGroupbyComponentId($component['id_component']);
        $json_values = Db::getInstance()->getValue('select json_values from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_lang where id_component = ' . (int) $component['id_component'] . ' and id_lang = ' . (int) $language);
        $json = preg_replace('/[[:cntrl:]]/', '', $json_values);
        $options = json_decode($json);
        $attributes_search = $attributes;
        if (count($options->options) >= 1) {
            foreach ($options->options as $key => &$option) {
                $attributes_search[$attribute_group] = $option->id;
                $id_attributes = implode(',', $attributes_search);
                if ($id_attributes) {
                    $combinations = Db::getInstance()->executeS('select pa.id_product_attribute from ' . _DB_PREFIX_ . 'product_attribute pa
                                    inner join ' . _DB_PREFIX_ . 'product_attribute_combination pac on pa.id_product_attribute = pac.id_product_attribute
                                    where pac.id_attribute in (' . $id_attributes . ') and pa.id_product = ' . (int) $product_id . '
                                    group by pa.id_product_attribute  having count(id_product) = ' . (int) count($attributes_search));
                    if (count($combinations) == 1) {
                        $option->att_product = $product_id . '_' . $combinations[0]['id_product_attribute'];
                    }
                    if (count($combinations) == 0) {
                        unset($options->options[$key]);
                    }
                }
            }
        } else {
            $component_obj->default_opt = array_values($options->options)[0]->id;
        }

        if ($options->options) {
            $component_array = array(
                "id_component" => $component_obj->id_component,
                "name" => $component_obj->name,
                "type" => $component_obj->type,
                "optional" => $component_obj->optional,
                "columns" => $component_obj->columns,
                "zoom" => $component_obj->zoom,
                "color" => $component_obj->color,
                "default_opt" => $component_obj->default_opt,
                "parent" => $component_obj->parent,
                "show_price" => $component_obj->show_price,
                "multivalue" => $component_obj->multivalue,
                "id_components_lang" => $component_obj->id_component,
                "id_lang" => $language,
                "title" => $component_obj->title_lang[$language],
                "description" => $component_obj->description_lang[$language],
                "json_values" => json_encode($options),
                "constraint" => $contraint ? array($contraint) : array()
            );

            if ($contraint) {
                $component_array['id_component'] = $component_obj->id_component . ($contraint ? 'f' . str_replace('_', '', $contraint) : 'f');
            }
        } else {
            $component_array = false;
        }
        $childrens = array();
        if ($component_array) {
            $childrens[] = $component_array;
        }
        if ($components) {
            if (!$component_array) {//Si no hay opciones para este componente continuamos con constraint padre
                $subchildrens = IdxComponent::generateChildren($components, $parent, $language, $product_id, $contraint, $attributes);
                $childrens = array_merge($childrens, $subchildrens);
            } else {
                $constraints = array_shift($component_obj->options_lang);
                foreach ($constraints['options'] as $option) {
                    $opt_constraint = $component_array['id_component'] . '_' . $option->id;
                    $attributes[$attribute_group] = $option->id;
                    $subchildrens = IdxComponent::generateChildren($components, $parent, $language, $product_id, $opt_constraint, $attributes);
                    $childrens = array_merge($childrens, $subchildrens);
                }
            }
        }
        return $childrens;
    }

    private static function getAttributeGroupbyComponentId($component_id)
    {
        return Db::getInstance()->getValue('Select id_attribute_group from ' . _DB_PREFIX_ . 'idxrcustomproduct_component_attribute where id_component = ' . (int) $component_id);
    }

    public static function getComponentIdByProduct($id_product)
    {
        if ($exist = self::existComponentIdByProduct($id_product)) {
            return $exist;
        }
        self::generateFromProduct($id_product);
        return self::existComponentIdByProduct($id_product);
    }

    public static function existComponentIdByProduct($id_product)
    {
        return Db::getInstance()->getValue('select comp.id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components comp'
                        . ' inner join ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact compim on comp.id_component = compim.id_component'
                        . ' where comp.type = "product" and att_product = ' . (int) $id_product . ';');
    }

    public static function getProductByCustomization($final_component, $customization)
    {
        //Only if this component is the last with this parent in this customization
        $parent = Db::getInstance()->getValue('select parent from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where id_component = ' . (int) $final_component);
        $components_same_parent = Db::getInstance()->executeS('Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where parent = ' . (int) $parent);
        $parent_components = array();
        foreach ($components_same_parent as $component_same_parent) {
            $parent_components[] = $component_same_parent['id_component'];
        }
        $last = false;
        foreach ($customization as $component) {
            $id = explode('f', $component['id_component'])[0];
            if (in_array($id, $parent_components)) {
                $last = $id;
            }
        }
        if ($last != $final_component) {
            return false;
        }

        $product = Db::getInstance()->getValue('Select att_product from ' . _DB_PREFIX_ . 'idxrcustomproduct_components_opt_impact where id_component = ' . (int) $parent);
        $attributes = array();
        foreach ($customization as $component) {
            $valid = Db::getInstance()->executeS('Select id_component from ' . _DB_PREFIX_ . 'idxrcustomproduct_components where id_component = ' . (int) $component['id_component'] . ' and parent = ' . (int) $parent);
            if ($valid) {
                $attributes[] = $component['id_option'];
            }
        }
        $sql = 'select id_product_attribute from ' . _DB_PREFIX_ . 'product_attribute where id_product = ' . (int) $product . ' and id_product_attribute  in (select id_product_attribute from ' . _DB_PREFIX_ . 'product_attribute_combination where id_attribute in (' . implode(',', $attributes) . ') group by id_product_attribute having count(id_product_attribute) = ' . count($attributes) . ')';
        $id_attribute = Db::getInstance()->getValue($sql);
        return $product . '_' . ($id_attribute ? $id_attribute : 0);
    }
    
    public static function hasTaxchange($id_component)
    {
        $query = new DbQuery();
        $query->select('id_comp_opt');
        $query->from('idxrcustomproduct_components_opt_impact');
        $query->where('taxchange > 0');
        $query->where('id_component = '.(int)$id_component);
        $query->limit(1);
        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
    
    public static function disableMultiOption($id_component)
    {
        $component = new IdxComponent($id_component);
        if ($component->multivalue == 'multi_simple' || $component->multivalue == 'multi_qty') {
            if ($component->multivalue == 'multi_simple') {
                $component->multivalue = 'unique';
            } else {
                $component->multivalue = 'unique_qty';
            }
            $component->update();
            return true;
        }
        return false;
    }
    
    public static function checkComponentLangs($id_component)
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $where = 'id_component = ' . (int) $id_component . ' and id_lang = ' . (int) $lang['id_lang']; 
            if (!Db::getInstance()->getValue('Select id_components_lang from '._DB_PREFIX_.'idxrcustomproduct_components_lang where '.$where)) {
                $data['id_component'] = (int)$id_component;
                $data['id_lang'] = (int) $lang['id_lang'];
                Db::getInstance()->insert('idxrcustomproduct_components_lang', $data);
            }
        }
        $components = Db::getInstance()->executeS("select id_component from "._DB_PREFIX_."idxrcustomproduct_components where type in ('sel','sel_img')");
        if (!$components) {
            return;
        }
        
        foreach ($components as $comp) {
            $options = Db::getInstance()->executeS('select id_components_lang, json_values from '._DB_PREFIX_.'idxrcustomproduct_components_lang where id_component = '.(int)$comp['id_component']);
            if (!$options) {
                continue;
            }
            $options_data = array();
            $max_opt = 0;
            $opts_id = array();
            foreach ($options as $value) {
                $options_data[$value['id_components_lang']] = json_decode($value['json_values']);
                if (count($options_data[$value['id_components_lang']]->options) > $max_opt) {
                    $max_opt = count($options_data[$value['id_components_lang']]->options);
                    foreach ($options_data[$value['id_components_lang']]->options as $option_lang) {
                        $opts_id[] = $option_lang->id;
                    }
                    $opts_id = array_unique($opts_id);
                }
            }
            foreach ($options_data as $id => $options) {
                if (count($options->options) < $max_opt) {
                    foreach ($opts_id as $opt_id) {
                        $exist = false;
                        foreach ($options->options as $opt) {
                            if ($opt->id == $opt_id) {
                                $exist = true;
                            }
                        }
                        if (!$exist) {
                            $new_opt =  new stdClass();
                            $new_opt->id = $opt_id;
                            $new_opt->name = '';
                            $new_opt->description = '';
                            $new_opt->img_ext = 'png';
                            $options->options[] = $new_opt;
                        }
                    }
                    $json_data = json_encode($options);
                    
                    Db::getInstance()->update('idxrcustomproduct_components_lang',array('json_values' => $json_data), 'id_components_lang = '.(int)$id);
                }
            }
        }
    }
}
