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

class NewsletterProFilters
{
    const TYPE_SELECTION = 'selection';
    const TYPE_FILTER = 'filter';

    private $selection_tables = [
            'customer' => 'c',
            'newsletter' => 's',
            'newsletter_pro' => 'a',
        ];

    public function get($srt)
    {
        if (self::TYPE_SELECTION == $this->getType($srt)) {
            return $this->getSelection($srt);
        } elseif (self::TYPE_FILTER == $this->getType($srt)) {
            return $this->getFilter($srt);
        }

        return [];
    }

    private function getType($srt)
    {
        $srt = Tools::substr($srt, 0, 20);
        if (preg_match('/^'.self::TYPE_SELECTION.':/', $srt)) {
            return self::TYPE_SELECTION;
        } elseif (preg_match('/^'.self::TYPE_FILTER.':/', $srt)) {
            return self::TYPE_FILTER;
        }

        return false;
    }

    public function getSelection($str)
    {
        $selection = $this->matchSelection($str);

        $sqls = $this->getSelectionSql($selection);
        $result = $this->runSelectSql($sqls);

        return $result;
    }

    private function matchSelection($str)
    {
        $selection = [];
        foreach ($this->selection_tables as $table => $short) {
            if (preg_match('/'.$short.':\[(?P<selection>[^]]+)/', $str, $match)) {
                $selection[$table] = str_replace(' ', '', $match['selection']);
            }
        }

        return $selection;
    }

    private function getSelectionSql($selection)
    {
        $sqls = [];
        foreach ($selection as $table => $ids) {
            $array_ids = explode(',', $ids);
            $list = [];
            $range = [];

            foreach ($array_ids as $value) {
                if (false !== strpos($value, '-')) {
                    $range[] = $value;
                } else {
                    $list[] = $value;
                }
            }

            $fields = $this->getTableFields($table);
            $id_field = $fields[0];

            if ((isset($fields[1]) && '*' == $fields[1]) || 1 == count($fields)) {
                $sql_fields = '*';
            } else {
                $sql_fields = '`'.join($fields, '`,`').'` ';
            }

            $sqls[$table] = 'SELECT '.$sql_fields.' FROM `'._DB_PREFIX_.$table.'` ';
            $sqls[$table] .= 'WHERE `'.$id_field.'` ';

            $between = '';
            $range_end = ' OR `'.$id_field.'` ';
            foreach ($range as $value) {
                $between .= ' BETWEEN '.str_replace('-', ' AND ', $value).$range_end;
            }

            $sqls[$table] .= rtrim($between, $range_end).' ';

            $list_join = join($list, ',');

            if (!empty($list)) {
                $sqls[$table] .= ' OR `'.$id_field.'` IN ('.$list_join.') ';
            }

            $sqls[$table] .= ';';
        }

        return $sqls;
    }

    private function runSelectSql($sqls)
    {
        $result = [];
        foreach ($sqls as $table => $sql) {
            $res = Db::getInstance()->executeS($sql);
            if (!empty($res)) {
                $result[$table] = $res;
            }
        }

        return $result;
    }

    private function getTableFields($table)
    {
        $tables = [];

        // If the second parameter is * the entire fields will be selected.
        // Write more then two arguments to select sepecific fields.

        $tables['customer'] = ['id_customer'];
        $tables['newsletter'] = ['id'];
        $tables['newsletter_pro'] = ['newsletter_pro'];

        return $tables[$table];
    }
}
