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

class NewsletterProTerminalView
{
    private $header;

    private $rows;

    private $show_columns;

    private $show;

    private $config_data = [
        'table_view' => false,
    ];

    public function __construct($header, $config_data = [])
    {
        $this->config_data = array_merge($this->config_data, $config_data);
        $this->rows = [];
        $this->header = $header;
        $this->show_columns = array_keys($header);
        $this->size = array_map(function ($value) {
            return Tools::strlen((string) $value);
        }, $this->header);
    }

    public static function newInstance($header, $config_data = [])
    {
        return new self($header, $config_data);
    }

    private function config($key)
    {
        if (array_key_exists($key, $this->config_data)) {
            return $this->config_data[$key];
        }

        return false;
    }

    public function add($row, $callback = null)
    {
        if (isset($callback)) {
            $row = $callback($row);
        }

        $this->rows[] = $row;

        $size = array_map(function ($value) {
            return Tools::strlen((string) $value);
        }, $row);

        foreach ($this->size as $key => $len) {
            if (array_key_exists($key, $size) && (int) $size[$key] > (int) $len) {
                $this->size[$key] = $size[$key];
            }
        }
    }

    public function addMultiple($rows, $callback = null)
    {
        foreach ($rows as $row) {
            $this->add($row, $callback);
        }
    }

    public function hide($columns)
    {
        foreach ($columns as $column_name) {
            $index = array_search($column_name, $this->show_columns);
            if (false !== $index) {
                unset($this->show_columns[$index]);
            }
        }
    }

    public function show($columns)
    {
        $this->show_columns = $columns;
    }

    public function render()
    {
        $output = [];
        $keys = $this->show_columns;
        $output[] = str_repeat('-', $this->rowSize()).PHP_EOL;

        $output[] = $this->config('table_view') ? '| ' : '';
        foreach ($keys as $key) {
            $diff = $this->getDiff($this->header, $key);
            $output[] = $this->header[$key].' '.str_repeat(' ', $diff).($this->config('table_view') ? '| ' : '');
        }
        $output[] = PHP_EOL;
        $output[] = str_repeat('-', $this->rowSize()).PHP_EOL;

        foreach ($this->rows as $rows) {
            $output[] = $this->config('table_view') ? '| ' : '';
            foreach ($keys as $key) {
                $diff = $this->getDiff($rows, $key);
                $output[] = $rows[$key].' '.str_repeat(' ', $diff).($this->config('table_view') ? '| ' : '');
            }
            $output[] = PHP_EOL;
        }

        $output[] = str_repeat('-', $this->rowSize()).PHP_EOL;

        return implode('', $output);
    }

    private function getDiff($data, $key)
    {
        $diff = $this->size[$key] - Tools::strlen((string) $data[$key]);
        if ($diff < 0) {
            $diff = 0;
        }

        return $diff;
    }

    private function rowSize()
    {
        $size = 0;
        foreach ($this->show_columns as $column_name) {
            $size += $this->size[$column_name] + ($this->config('table_view') ? 3 : 1);
        }

        return $size < 0 ? 0 : $size + ($this->config('table_view') ? 1 : -1);
    }
}
