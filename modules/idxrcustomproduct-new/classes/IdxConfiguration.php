<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2022 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

class IdxConfiguration
{

    public $id_configuration;
    public $name;
    public $active;
    public $hook;
    public $categories;
    public $products;
    public $components;
    public $components_order;
    public $visualization;
    public $first_open;
    public $resume_open;
    public $button_section;
    public $color;
    public $final_color;
    public $add_base;
    public $show_increment;
    public $show_topprice;
    public $productbase_component;
    public $breakdown_attachment;
    public $discount;
    public $discount_type;
    public $discount_amount;
    public $discount_createdas;
    public $constraints_options;
    public $default_configuration;

    const SEPARATOR = '|';

    public function __construct($id_configuration = null, $full = false)
    {
        if ($id_configuration) {
            $this->id_configuration = (int) $id_configuration;
        }
        if ($full && $id_configuration) {
            $this->fillObject();
        }
    }

    public function add()
    {
        $data = array(
            'name' => pSQL($this->name),
            'categories' => $this->categories ? implode(',', $this->categories) : '',
            'products' => $this->products ? implode(',', $this->products) : '', 'components' => $this->components ? implode(',', $this->components) : '',
            'visualization' => pSQL($this->visualization),
            'color' => pSQL($this->color),
            'final_color' => pSQL($this->final_color),
            'active' => (int) $this->active,
            'hook' => pSQL($this->hook),
            'add_base' => (int) $this->add_base,
            'show_increment' => (int) $this->show_increment,
            'show_topprice' => (int) $this->show_topprice,
            'productbase_component' => (int) $this->productbase_component,
            'first_open' => (int) $this->first_open,
            'resume_open' => (int) $this->resume_open,
            'button_section' => (int) $this->button_section,
            'breakdown_attachment' => (int) $this->breakdown_attachment,
            'discount' => (int) $this->discount,
            'discount_type' => pSQL($this->discount_type),
            'discount_amount' => pSQL($this->discount_amount),
            'discount_createdas' => pSQL($this->discount_createdas),
            'constraints_options' => pSQL($this->constraints_options),
            'default_configuration' => pSQL($this->default_configuration)
        );
        if (Db::getInstance()->insert('idxrcustomproduct_configurations', $data)) {
            $this->id_configuration = Db::getInstance()->Insert_ID();
            return $this->id_configuration;
        } else {
            return false;
        }
    }

    public function update()
    {
        if (!$this->id_configuration) {
            return $this->add();
        }
        $data = array(
            'name' => pSQL($this->name),
            'categories' => $this->categories ? implode(',', $this->categories) : '',
            'products' => $this->products ? implode(',', $this->products) : '',
            'components' => $this->components_order,
            'visualization' => pSQL($this->visualization),
            'color' => pSQL($this->color),
            'final_color' => pSQL($this->final_color),
            'active' => (int) $this->active,
            'hook' => pSQL($this->hook),
            'add_base' => (int) $this->add_base,
            'show_increment' => (int) $this->show_increment,
            'show_topprice' => (int) $this->show_topprice,
            'productbase_component' => (int) $this->productbase_component,
            'first_open' => (int) $this->first_open,
            'resume_open' => (int) $this->resume_open,
            'button_section' => (int) $this->button_section,
            'breakdown_attachment' => (int) $this->breakdown_attachment,
            'discount' => (int) $this->discount,
            'discount_type' => pSQL($this->discount_type),
            'discount_amount' => pSQL($this->discount_amount),
            'discount_createdas' => pSQL($this->discount_createdas)
        );
        $where = 'id_configuration = ' . (int) $this->id_configuration;
        Db::getInstance()->update('idxrcustomproduct_configurations', $data, $where);
        $this->cleanClones();
        return $this->id_configuration;
    }

    public function delete()
    {
        $del_configuration = 'delete from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $this->id_configuration . ';';
        return Db::getInstance()->execute($del_configuration);
    }

    public function clonar($index = false)
    {
        $this->fillObject();
        if (!$index) {
            $new_name = $this->name . '_clon';
        } else {
            $new_name = $this->name . '_clon' . (int) $index;
        }
        $exist = Db::getInstance()->getValue('Select id_configuration from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where name ="' . pSQL($new_name) . '"');
        if ($exist) {
            $index++;
            return $this->clonar($index);
        }

        unset($this->id_configuration);
        $this->name = $new_name;
        $new_id = $this->add();

        return $new_id;
    }

    public function fillObject()
    {
        $data = Db::getInstance()->getRow('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where id_configuration = ' . (int) $this->id_configuration);
        if (!$data) {
            return false;
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'components':
                case 'categories':
                case 'products':
                    if ($value) {
                        $this->$key = explode(',', $value);
                    }
                    break;

                default:
                    $this->$key = $value;
                    break;
            }
        }
    }

    public static function getConfigurations($hook = '')
    {
        $query = 'Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations where 1=1 '
                . (($hook) ? 'and hook = "' . pSQL($hook) . '"' : '');

        if (Tools::isSubmit('submitFilteridxrcustomproduct_configurations') and Tools::getValue('submitFilteridxrcustomproduct_configurations') == '1') {
            $name_filter = Tools::getValue('idxrcustomproduct_configurationsFilter_name');
            if ($name_filter) {
                $query .= ' and name like "%' . pSQL($name_filter) . '%"';
            }

            $active_filter = Tools::getValue('idxrcustomproduct_configurationsFilter_active');
            if ($active_filter != "") {
                $query .= ' and active = ' . (int) $active_filter;
            }
        }

        if (Tools::isSubmit('idxrcustomproduct_configurationsOrderby')) {
            $query .= ' order by ' . pSQL(Tools::getValue('idxrcustomproduct_configurationsOrderby') . ' ' . pSQL(Tools::getValue('idxrcustomproduct_configurationsOrderway')));
        }

        return Db::getInstance()->executeS($query);
    }

    public function getProducts()
    {
        if (!$this->products && !$this->categories) {
            $this->fillObject();
        }
        if (!$this->products && !$this->categories) {
            return false;
        }

        $products = $this->products;
        if ($this->categories) {
            foreach ($this->categories as $category) {
                $cat_obj = new Category($category);
                if ($cat_products = $cat_obj->getProductsWs()) {
                    foreach ($cat_products as $cat_product) {
                        $products[] = (isset($cat_product['id_product']) ? $cat_product['id_product'] : $cat_product['id']);
                    }
                }
            }
        }
        return array_unique($products);
    }

    public static function addImage($conf_id)
    {
        $next_id = self::getNextImageIndex($conf_id);
        $data = array(
            'id_configuration' => (int) $conf_id,
            'conf_index' => (int) $next_id,
            'attached_values' => ''
        );
        Db::getInstance()->insert('idxrcustomproduct_configurationimage', $data);
        return Db::getInstance()->Insert_ID();
    }

    public static function saveConfigurationImage($source, $id_configuration, &$return)
    {
        $module = new IdxrCustomProduct();
        $image_id = self::addImage($id_configuration);
        $array = explode('.', $source['name']);
        $extension = end($array);
        $destination_folder = _PS_IMG_DIR_ . $module->name . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR . self::getImgFolder($image_id);
        if (!file_exists($destination_folder) && !mkdir($destination_folder, 0755, true)) {
            $return['result'] = 'ko';
            $return['message'] = $module->l('Fail when try to create image folder', 'ajax');
            self::delImage($id_configuration, self::getImgIndexFromId($image_id));
            return $return;
        }

        $types = IdxrCustomProduct::getImagesTypes();
        $target_path = $destination_folder . DIRECTORY_SEPARATOR . $image_id . '.png';
        if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png'))) {
            $return['result'] = 'ko';
            $return['message'] = $extension . ' ' . $module->l('is not an allowed extension for image', 'ajax');
        } else {
            $uploaded_image = imagecreatefromstring(Tools::file_get_contents($source['tmp_name']));
            imageAlphaBlending($uploaded_image, false);
            imageSaveAlpha($uploaded_image, true);
            if ($uploaded_image && imagepng($uploaded_image, $target_path)) {
                $return['result'] = 'ok';
                $return['message'] = $module->l('The image was upload sucessfully', 'ajax');
                list($ancho, $alto) = getimagesize($target_path);
                foreach ($types as $type) {
                    $image_type = new ImageType($type);
                    $target_type = $destination_folder . DIRECTORY_SEPARATOR . $image_id . '-' . $image_type->name . '.png';
                    $imagen_new = imagecreatetruecolor($image_type->width, $image_type->height);
                    $imagen_base = imagecreatefrompng($target_path);
                    imageAlphaBlending($imagen_new, false);
                    imageSaveAlpha($imagen_new, true);
                    imageAlphaBlending($imagen_base, false);
                    imageSaveAlpha($imagen_base, true);
                    imagecopyresampled($imagen_new, $imagen_base, 0, 0, 0, 0, $image_type->width, $image_type->height, $ancho, $alto);
                    imagepng($imagen_new, $target_type);
                }
            } else {
                $return['result'] = 'ko';
                $return['message'] = $module->l('Fail when try to conver and update the image, please try again', 'ajax');
                self::delImage($id_configuration, self::getImgIndexFromId($image_id));
            }
        }
    }

    public static function generateImgScaled($image_id, $type)
    {
        $module = new IdxrCustomProduct();
        if (!is_numeric($type)) {
            $type_id = Db::getInstance()->getValue('
			SELECT `id_image_type`
			FROM `' . _DB_PREFIX_ . 'image_type`
			WHERE `name` = \'' . pSQL($type) . '\'');
        } else {
            $type_id = $type;
        }

        if (!$type_id) {
            return false;
        }
        $destination_folder = _PS_IMG_DIR_ . $module->name . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR . self::getImgFolder($image_id);
        $target_path = $destination_folder . $image_id . '.png';
        list($ancho, $alto) = getimagesize($target_path);
        $image_type = new ImageType($type_id);
        $target_type = $destination_folder . $image_id . '-' . $image_type->name . '.png';
        $imagen_new = imagecreatetruecolor($image_type->width, $image_type->height);
        $imagen_base = imagecreatefrompng($target_path);
        imagecopyresampled($imagen_new, $imagen_base, 0, 0, 0, 0, $image_type->width, $image_type->height, $ancho, $alto);
        imagepng($imagen_new, $target_type);
    }

    public static function delImage($conf_id, $index)
    {
        $image_folder = _PS_IMG_DIR_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR . self::getImgFolder(self::getImgIdFromReference($conf_id . '_' . $index));
        $files = glob($image_folder . '*.png');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $where = 'id_configuration = ' . (int) $conf_id . ' and conf_index = ' . (int) $index;
        Db::getInstance()->delete('idxrcustomproduct_configurationimage', $where);
    }

    public static function getNextImageIndex($conf_id)
    {
        $max_id = (int) Db::getInstance()->getValue('Select max(conf_index) from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configuration = ' . (int) $conf_id);
        return $max_id + 1;
    }

    public static function getAllImages($conf_id, $type = 'original')
    {
        $images = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configuration = ' . (int) $conf_id);

        foreach ($images as &$image) {
            $image['path'] = self::getImage($image['id_configurationimage'], 'url', $type);
            $image['name'] = $conf_id . '_' . $image['conf_index'] . '.png';
            $image['size'] = filesize(self::getImage($image['id_configurationimage'], 'file', $type));
        }
        return $images;
    }

    public static function getImage($id_image, $path = 'file', $type = 'original')
    {
        if (is_numeric($type)) {
            $image_type = new ImageType($type);
            $type = $image_type->name;
        }
        $id_image = self::getImgIdFromReference($id_image);
        $image_file_base = _PS_IMG_DIR_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR;
        $image_src_base = _PS_IMG_ . 'idxrcustomproduct' . DIRECTORY_SEPARATOR . 'configurations' . DIRECTORY_SEPARATOR;

        $image_route = self::getImgFolder($id_image);
        if ($type != 'original') {
            $image = $id_image . '-' . $type . '.png';
            if (!file_exists($image_file_base . $image_route . $image) && file_exists($image_file_base . $image_route . $id_image . '.png')) {
                self::generateImgScaled($id_image, $type);
            }
        } else {
            $image = $id_image . '.png';
        }

        if ($path == 'file') {
            return $image_file_base . $image_route . $image;
        } else {
            return $image_src_base . $image_route . $image;
        }
    }

    public static function getImgFolder($idImage)
    {
        if ($idImage && is_numeric($idImage)) {
            $folders = str_split((string) $idImage);
            $folderpath = implode(DIRECTORY_SEPARATOR, $folders) . DIRECTORY_SEPARATOR;
            return $folderpath;
        } else {
            return false;
        }
    }

    public static function getImgIdFromReference($idImage)
    {
        if (!is_numeric($idImage) && strpos($idImage, '_')) {
            $value = explode('_', $idImage);
            $idImage = Db::getInstance()->getValue('Select id_configurationimage from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configuration = ' . (int) $value[0] . ' and conf_index = ' . (int) $value[1]);
        }
        return $idImage;
    }

    public static function getImgIndexFromId($id_image)
    {
        return Db::getInstance()->getValue('Select conf_index from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configurationimage = ' . (int) $id_image);
    }

    public static function saveConfigurationImageStatus($id_image, $status)
    {
        $id_configuration = Db::getInstance()->getValue('Select id_configuration from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configurationimage = ' . (int) $id_image);
        if (!$id_configuration) {
            return false;
        }
        self::deleteStatusImage($id_configuration, $status);
        self::addStatusImage($id_image, $status);
    }

    public static function getImageidFromStatus($id_configuration, $status)
    {
        $rows = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configuration = ' . (int) $id_configuration);
        foreach ($rows as $row) {
            $row['attached_values'] = explode(IdxConfiguration::SEPARATOR, $row['attached_values']);
            foreach ($row['attached_values'] as $row_status) {
                if ($row_status == $status) {
                    return $row['id_configurationimage'];
                }
            }
        }
        return false;
    }

    public static function addStatusImage($id_image, $status)
    {
        $actual_status = Db::getInstance()->getValue('Select attached_values from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configurationimage = ' . (int) $id_image);
        $new_status = explode(IdxConfiguration::SEPARATOR, $actual_status);
        $new_status[] = $status;
        $new_status_string = implode(IdxConfiguration::SEPARATOR, array_filter($new_status));
        $data = array(
            'attached_values' => pSQL($new_status_string)
        );
        $where = 'id_configurationimage = ' . (int) $id_image;
        Db::getInstance()->update('idxrcustomproduct_configurationimage', $data, $where);
    }

    public static function deleteStatusImage($id_configuration, $status)
    {
        $rows = Db::getInstance()->executeS('Select * from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurationimage where id_configuration = ' . (int) $id_configuration);
        foreach ($rows as $row) {
            $row['attached_values'] = explode(IdxConfiguration::SEPARATOR, $row['attached_values']);

            foreach ($row['attached_values'] as $key => $row_status) {
                if ($row_status == $status) {
                    unset($row['attached_values'][$key]);
                }
            }
            $new_attached_values = implode(IdxConfiguration::SEPARATOR, array_filter($row['attached_values']));
            $data = array(
                'attached_values' => pSQL($new_attached_values)
            );
            $where = 'id_configurationimage = ' . (int) $row['id_configurationimage'];
            Db::getInstance()->update('idxrcustomproduct_configurationimage', $data, $where);
        }
    }

    public static function deleteBulkComponent($id_component)
    {
        $configurations_components = Db::getInstance()->executeS('Select id_configuration, components from ' . _DB_PREFIX_ . 'idxrcustomproduct_configurations');

        foreach ($configurations_components as $configuration_components) {
            $components = explode(',', $configuration_components['components']);
            if (($key = array_search($id_component, $components)) !== false) {
                unset($components[$key]);
                $update = array(
                    'components' => pSQL(implode(',', $components))
                );
                Db::getInstance()->update('idxrcustomproduct_configurations', $update, 'id_configuration = ' . (int) $configuration_components['id_configuration']);
            }
        }
    }

    /**
     * Return the new price impact values if changes from original with this option checked
     * @param type $id_configuration
     * @param type $id_component
     * @param type $id_option
     * @param type $attached_product
     * @return array
     */
    public static function getImpactChanges($id_configuration, $id_component, $id_option, $attached_product, $baseproduct)
    {
        $context = Context::getContext();
        $changes = array();
        if (!$attached_product) {
            return $changes;
        }
        $module = Module::getInstanceByName('idxrcustomproduct');
        $base_product = Db::getInstance()->getValue('select id_configuration from `' . _DB_PREFIX_ . 'idxrcustomproduct_configurations` '
                . 'where id_configuration = ' . (int) $id_configuration . ' and productbase_component = 1 ');

        if (!$base_product) {
            return $changes;
        }

        if (isset($context->customer)) {
            $without_taxes = Group::getPriceDisplayMethod($context->customer->id_default_group);
            if (method_exists($context->cart, 'getTaxAddressId')) {
                $addressId = $context->cart->getTaxAddressId();
            } else {
                $addressId = $context->cart->id_address_delivery;
            }

            if ($addressId) { // first we need id_product
                $customer_product_tax = Tax::getProductTaxRate($baseproduct, $addressId);
                if ($customer_product_tax == 0) {
                    $without_taxes = true;
                }
            }
        } else {
            $without_taxes = false;
        }

        $product = explode('_', $attached_product);

        $configuration_comps = explode(',', Db::getInstance()->getValue('select components from `' . _DB_PREFIX_ . 'idxrcustomproduct_configurations` '
                        . 'where id_configuration = ' . (int) $id_configuration));
        if (!in_array($id_component, $configuration_comps)) { //If is not in configuration is base product
            foreach ($configuration_comps as $comp) {
                $component = new IdxComponent($comp, true);
                $options = array_shift($component->options_lang)['options'];
                if (!$options) {
                    continue;
                }
                foreach ($options as &$option) {
                    if ($option->price_impact_type == 'calculated') {
                        $module->generateImpact($option, $without_taxes, false, $comp, $product[0], $product[1]);
                        $changes[$comp . '_' . $option->id] = [
                            'price_impact' => $option->price_impact,
                            'price_impact_formatted' => $module->formatPrice($option->price_impact, _PS_PRICE_COMPUTE_PRECISION_)
                        ];
                    }
                }
            }
        }
        return $changes;
    }

    public function getCategoriasSeleccionadas()
    {
        if ($this->categories) {
            $categorias = array();
            foreach ($this->categories as $cat_id) {
                $cat_obj = new Category($cat_id, Context::getContext()->language->id);
                $categoria = array(
                    'id' => (int) $cat_id,
                    'name' => $cat_obj->name
                );
                $categorias[] = $categoria;
            }
            return $categorias;
        }
        return false;
    }

    public function getProductosSeleccionadas()
    {
        if ($this->products) {
            $productos = array();
            foreach ($this->products as $prod_id) {
                $prod_obj = new Product($prod_id, Context::getContext()->language->id);
                $producto = array(
                    'id' => (int) $prod_id,
                    'name' => $prod_obj->name[Context::getContext()->language->id],
                    'reference' => $prod_obj->reference
                );
                $productos[] = $producto;
            }
            return $productos;
        }
        return false;
    }

    public static function getCustomProducts($id_configuration = false)
    {
        $context = Context::getContext();
        $query = new DbQuery();
        $query->select('products, categories');
        $query->from('idxrcustomproduct_configurations');
        if ($id_configuration) {
            $query->where('id_configuration = ' . (int) $id_configuration);
        } else {
            $query->where('active = 1');
        }

        $configured = Db::getInstance()->executeS($query);
        $custom_products = array();
        $products = array();
        if ($configured) {
            foreach ($configured as $conf) {
                $products = array_merge($products, explode(',', $conf['products']));
                $categories = array_filter(explode(',', $conf['categories']));
                if ($categories) {
                    foreach ($categories as $cat_id) {
                        $cat_products = Db::getInstance()->executeS('
                            SELECT cp.`id_product` as id
                            FROM `' . _DB_PREFIX_ . 'category_product` cp
                            WHERE cp.`id_category` = ' . (int) $cat_id);
                        foreach ($cat_products as $cat_product) {
                            $products[] = $cat_product['id'];
                        };
                    }
                }
            }
        }
        //Clean
        $products = array_unique(array_filter($products));

        $custom_products = array();
        $module = Module::getInstanceByName('idxrcustomproduct');
        foreach ($products as $prod_id) {
            $custom_products[] = array(
                'products' => $prod_id,
                'link' => $context->link->getProductLink($prod_id),
                'min_price' => $module->formatPrice(IdxCustomizedProduct::getMinPrice($prod_id))
            );
        }

        return $custom_products;
    }

    public function cleanClones()
    {
        $custom_products = self::getCustomProducts($this->id_configuration);
        foreach ($custom_products as $product) {
            Db::getInstance()->delete('idxrcustomproduct_clones', 'id_producto = ' . (int) $product['products']);
        }
    }

    public function setOptionImpact($impact)
    {
        $data = array(
            'impact_options' => json_encode($impact)
        );
        $where = 'id_configuration = ' . (int) $this->id_configuration;
        Db::getInstance()->update('idxrcustomproduct_configurations', $data, $where);
    }

    public function getOptionsImpact()
    {
        return self::getOptionsImpactStatic($this->id_configuration);
    }

    public static function getOptionsImpactStatic($id_configuration)
    {
        $impacts = [];
        $query = new DbQuery();
        $query->select('impact_options');
        $query->from('idxrcustomproduct_configurations');
        $query->where('id_configuration = ' . (int) $id_configuration);

        if ($impact_text = Db::getInstance()->getValue($query)) {
            $impacts = json_decode($impact_text, true);
        }

        return $impacts;
    }

    public function addOptionImpact($impact)
    {
        $current_impact = $this->getOptionsImpact();
        //Only one type of impact is valid
        if ($impact['impact_percent'] > 0 && $impact['impact_fixed'] > 0) {
            $impact['impact_fixed'] = '';
        }

        if ($current_impact) {
            foreach ($current_impact as $key => $value) {
                if ($impact['option_trigger'] == $value['option_trigger'] && $impact['option_impacted'] == $value['option_impacted']) {
                    unset($current_impact[$key]);
                }
            }
        }

        $current_impact[] = $impact;
        $this->setOptionImpact($current_impact);
    }

    public function delOptionImpact($impact)
    {
        $current_optionimpacts = $this->getOptionsImpact();
        $impact_parts = explode('to', $impact);
        foreach ($current_optionimpacts as $key => $value) {
            if ($value['option_trigger'] == $impact_parts[0] && $value['option_impacted'] == $impact_parts[1]) {
                unset($current_optionimpacts[$key]);
            }
        }
        $this->setOptionImpact($current_optionimpacts);
    }
}
