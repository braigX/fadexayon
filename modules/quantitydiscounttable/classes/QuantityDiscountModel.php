<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class QuantityDiscountModel extends ObjectModel
{
    public $id_product;
    public $product_attribute_id;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $id_customer;
    public $price;
    public $from_quantity;
    public $reduction;
    public $reduction_type;
    public $from;
    public $to;

    public static $definition = [
        'table' => 'specific_price',
        'primary' => 'id_specific_price',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_country' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'from_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'reduction' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'reduction_type' => ['type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'size' => 255],
            'from' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'to' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function save($null_values = false, $auto_date = true)
    {
        if (empty($this->price)) {
            $this->price = -1;
        }
        $result = [];
        if (!empty($this->id_product) && is_array($this->id_product)) {
            foreach ($this->id_product as $key => $id) {
                // Prepare the data array for insertion
                $product_attribute_id = isset($this->product_attribute_id[$key]) ? (int) $this->product_attribute_id[$key] : 0;
                $data = [
                    'id_product' => (int) $id,
                    'id_currency' => (int) $this->id_currency,
                    'id_country' => (int) $this->id_country,
                    'id_group' => (int) $this->id_group,
                    'id_product_attribute' => $product_attribute_id,
                    'price' => (float) $this->price,
                    'from_quantity' => (int) $this->from_quantity,
                    'reduction' => (float) $this->reduction,
                    'reduction_type' => pSQL($this->reduction_type),
                    'from' => pSQL($this->from),
                    'to' => pSQL($this->to),
                ];
                // Check if the record already exists
                $existing = Db::getInstance()->getValue('
                    SELECT `id_specific_price`
                    FROM `' . _DB_PREFIX_ . 'specific_price`
                    WHERE `id_product` = ' . (int) $id . '
                    AND `id_currency` = ' . (int) $this->id_currency . '
                    AND `id_country` = ' . (int) $this->id_country . '
                    AND `id_group` = ' . (int) $this->id_group . '
                    AND `id_product_attribute` = ' . $product_attribute_id . '
                    AND `price` = ' . (float) $this->price . '
                    AND `from_quantity` = ' . (int) $this->from_quantity . '
                    AND `reduction` = ' . (float) $this->reduction . '
                    AND `reduction_type` = "' . pSQL($this->reduction_type) . '"
                    
                ');

                if ($existing) {
                    // Update existing record
                    $result[] = Db::getInstance()->update('specific_price', $data, 'id_specific_price = ' . (int) $existing);
                } else {
                    // Insert new record
                    $result[] = Db::getInstance()->insert('specific_price', $data);
                }
            }

            return $result;
        }

        return parent::save($null_values, $auto_date);
    }
}
