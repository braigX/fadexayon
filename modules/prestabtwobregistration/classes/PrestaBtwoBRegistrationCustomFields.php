<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class PrestaBtwoBRegistrationCustomFields extends ObjectModel
{
    public $field_title;
    public $field_type;
    public $default_value;
    public $field_validation;
    public $maximum_size;
    public $file_types;
    public $notice_message;
    public $notice_types;
    public $active;
    public $is_mandatory;
    public $position;
    public $is_dependant;
    public $id_dependant_field;
    public $id_dependant_value;
    public $is_deleted;
    public $date_add;
    public $date_upd;

    const TABLENAME = 'presta_btwob_custom_fields';
    const TABLENAME_LANG = 'presta_btwob_custom_fields_lang';
    const TABLENAME_VALUE = 'presta_btwob_custom_fields_value';
    const TABLENAME_VALUE_LANG = 'presta_btwob_custom_fields_value_lang';

    public static $definition = array(
        'table' => 'presta_btwob_custom_fields',
        'primary' => 'id_presta_btwob_custom_fields',
        'multilang' => true,
        'fields' => array(
            'field_title' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ),
            'field_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => 'true',
            ),
            'default_value' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ),
            'field_validation' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => 'true',
            ),
            'maximum_size' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'file_types' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
            ),
            'notice_message' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ),
            'notice_types' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'is_mandatory' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ),
            'is_dependant' => array(
                'type' => self::TYPE_BOOL,
            ),
            'id_dependant_field' => array(
                'type' => self::TYPE_STRING,
            ),
            'id_dependant_value' => array(
                'type' => self::TYPE_STRING,
            ),
            'is_deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false,
            ),
        )
    );

    public function getDependantField($fieldType, $deleted = 0, $idLang = true)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::TABLENAME . ' rf
        LEFT JOIN ' . _DB_PREFIX_ . self::TABLENAME_LANG . ' rfl ON
        (rf.id_presta_btwob_custom_fields = rfl.id_presta_btwob_custom_fields)
        WHERE
            rfl.`id_lang` = ' . (int) $idLang . ' AND
            `is_deleted` = ' . (int) $deleted . ' AND
            field_type IN (' . $fieldType . ') AND `active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    public function getAllRegistrationFields($active = 1, $deleted = 0, $idLang = true)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::TABLENAME . ' rf
        LEFT JOIN ' . _DB_PREFIX_ . self::TABLENAME_LANG . ' rfl ON
        (rf.id_presta_btwob_custom_fields = rfl.id_presta_btwob_custom_fields)
         WHERE
            rfl.`id_lang` = ' . (int) $idLang . ' AND
            `is_deleted` = ' . (int) $deleted . ' AND
            active = '. $active . ' ORDER BY position ASC';

        return Db::getInstance()->executeS($sql);
    }

    public function deleteMultipleValuesById($id)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value
            WHERE `id_presta_btwob_custom_fields` = ' . (int) $id;
        $data = Db::getInstance()->executeS($sql);
        if ($data) {
            foreach ($data as $value) {
                Db::getInstance()->delete(
                    'presta_btwob_custom_fields_value_lang',
                    'id_multi_value = ' . (int) $value['id_multi_value']
                );
            }
            Db::getInstance()->delete(
                'presta_btwob_custom_fields_value',
                'id_presta_btwob_custom_fields = ' . (int) $id
            );
        }
        return true;
    }

    public function getMultiplePrimaryId($id)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_multi_value`
            FROM ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value
                WHERE
                    `id_presta_btwob_custom_fields` = ' . (int) $id
        );
    }

    public function saveMultipleValues($id, $values, $idLang)
    {
        if ($values) {
            Db::getInstance()->insert(
                'presta_btwob_custom_fields_value',
                array(
                    'id_presta_btwob_custom_fields' => (int) $id,
                    'id_multi_value' => (int) $id,
                )
            );
            $langs  = Language::getLanguages();
            foreach ($langs as $lang) {
                Db::getInstance()->insert(
                    'presta_btwob_custom_fields_value_lang',
                    array(
                        'id_multi_value' => (int) $id,
                        'value' => pSQL($values),
                        'id_lang' => (int) $idLang,
                    )
                );
            }
        }
    }

    public function getMultipleValuesById($id, $idLang = false)
    {
        $sql = 'SELECT * from ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value rfv
                INNER JOIN ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value_lang rfvl ON
                (rfv.`id_multi_value` = rfvl.`id_multi_value`)
                WHERE
                    rfv.`id_presta_btwob_custom_fields` = ' . (int) $id;

        if ($idLang) {
            $sql .= ' AND rfvl.`id_lang` = ' . (int) $idLang;
        }
        return Db::getInstance()->executeS($sql);
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT `id_presta_btwob_custom_fields`, `position`
            FROM `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`
            ORDER BY `position` ASC'
        )) {
            return false;
        }
        foreach ($res as $rule) {
            if ((int) $rule['id_presta_btwob_custom_fields'] == (int) $this->id) {
                $moved_rule = $rule;
            }
        }
        if (!isset($moved_rule) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE `position`
            ' . ($way
                ? '> ' . (int) $moved_rule['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $moved_rule['position'] . ' AND `position` >= ' . (int) $position))
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`
            SET `position` = ' . (int) $position . '
            WHERE `id_presta_btwob_custom_fields` = ' .
            (int) $moved_rule['id_presta_btwob_custom_fields']);
    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `' . _DB_PREFIX_ . 'presta_btwob_custom_fields`';
        $position = Db::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }

    public function getFieldByIdHeadingGroup($idLang, $deleted = 0, $active = 1, $id_dependant = 0)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . self::TABLENAME . ' a
            LEFT JOIN ' . _DB_PREFIX_ . self::TABLENAME_LANG . ' l ON
                (a.`id_presta_btwob_custom_fields` = l.`id_presta_btwob_custom_fields`)
                WHERE
                    id_lang = ' . (int) $idLang . ' AND
                    `is_deleted` = ' . (int) $deleted . ' AND
                    active = ' . (int) $active . ' AND
                    is_dependant = ' . (int) $id_dependant . ' ORDER BY position ASC'
        );
    }

    public function getDependantFieldByIdDependant($idDependant, $idDependantValue, $idLang = true, $deleted = 0)
    {
        return Db::getInstance()->executeS(
            'SELECT * from ' . _DB_PREFIX_ . self::TABLENAME . ' rf
            LEFT JOIN ' . _DB_PREFIX_ . self::TABLENAME_LANG . ' rfl ON
            (rf.id_presta_btwob_custom_fields = rfl.id_presta_btwob_custom_fields)
                WHERE
                    `id_lang` = ' . (int) $idLang . ' AND
                    `is_deleted` = ' . (int) $deleted . ' AND
                    `id_dependant_field` = ' . (int) $idDependant . '  AND
                    `id_dependant_value` = ' . (int) $idDependantValue . ' ORDER BY position ASC'
        );
    }

    public function getAllNoticeMessage($idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . PrestaBtwoBRegistrationCustomFields::TABLENAME . ' rf
            INNER JOIN ' . _DB_PREFIX_ . self::TABLENAME_LANG . ' rfl ON
            (rf.id_presta_btwob_custom_fields = rfl.id_presta_btwob_custom_fields)
            WHERE
                rfl.id_lang = ' . (int) $idLang . '
                GROUP BY rfl.id_presta_btwob_custom_fields'
        );
    }

    public function getMultipleValuesByFieldId($idField)
    {
        $sql = 'SELECT rfv.`id_multi_value` FROM ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value rfv
            INNER JOIN ' . _DB_PREFIX_ . self::TABLENAME . ' rf
            ON (rfv.id_presta_btwob_custom_fields = rf.id_presta_btwob_custom_fields)
            WHERE rf.`id_presta_btwob_custom_fields` = ' . (int) $idField;

        return Db::getInstance()->executeS($sql);
    }

    public function getMultipleValuesLangDataByFieldId($idMultiField, $idLang = false)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'presta_btwob_custom_fields_value_lang rfvl
            WHERE rfvl.`id_multi_value` = ' . (int) $idMultiField;

        if ($idLang) {
            $sql .= ' AND id_lang = ' . (int) $idLang;
        }

        return Db::getInstance()->executeS($sql);
    }
}
