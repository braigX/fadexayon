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

class NewsletterProHTMLRender
{
    /**
     * @version: 2.0
     *
     * Format columns
     * Example;
     *
     * {# title({product_name}, 'Product Name') #}    {# title({serial_nubmer.value}, 'S/N') #}    {# title({product_reference}, 'Reference') #}    {# title({warranty_time_month}, 'Warranty') #}
     * <!-- @foreach {serial_nubmer} as {sn} -->
     * {# column({product_name}) #}    {# column({sn.value}) #}    {# column({product_reference}) #}    {# column({warranty_time_month} months) #}
     * <!-- @endforeach -->
     *
     * <!-- @if intVal({warranty_valid}) == 1 -->
     *      Condition content here.
     * <!-- @endif -->
     *
     * <!-- @if intVal({warranty_valid}) == 1 -->
     *      If content here.
     * <!-- @else -->
     *       Else content here.
     * <!-- @endif -->
     *
     * <!-- @foreach {serial_nubmer} as {sn} -->
     *       Serial number: {sn.value}
     * <!-- @endforeach -->
     *
     * <input condition-if="intval({display_list_of_interest}) == 1" checked="true" condition-else checked="false" condition-endif type="checkbox" name="list_of_interest[]" value="{item.id_newsletter_pro_list_of_interest}">
     *
     * Example of the render attributes:
     *
     * {np_zip_code attrs="class: pqnp-popup-form-control"}
     * {np_select attrs="class: pqnp-popup-form-control"}
     * {np_checkbox containerAttrs="class: pqnp-popup-row" labelAttrs="class: pqnp-popup-col-sm-6"}
     * {np_radio containerAttrs="class: pqnp-custom-radio-inline"}
     * {np_textarea attrs="class: pqnp-popup-form-control"}
     */
    public static function output($content, $variables = [], $use_conditions = true, $format_columns = false)
    {
        if ($format_columns) {
            $columns = [];
            $column_index = 0;

            $content = preg_replace_callback('/\{\#(?:\s+?)(title)\((.*?),(?:\s+?)(\'|\")(.*?)\3\)(?:\s+?)\#\}/', function ($match) use ($variables, &$columns) {
                $func_name = $match[1];
                $argument = $match[2];
                $title = $match[4];

                if ('{' === Tools::substr($argument, 0, 1)) {
                    $variable_content = Tools::substr($argument, 1, -1);

                    $variable_exp = explode('.', $variable_content);
                    $variable_name = $variable_exp[0];

                    if (array_key_exists($variable_name, $variables)) {
                        $variable_value = $variables[$variable_name];
                        $title_len = Tools::strlen($title);

                        if (is_array($variable_value)) {
                            $max = 0;

                            foreach ($variable_value as $value) {
                                $len = Tools::strlen((string) $value[$variable_exp[1]]);
                                if ($len > $max) {
                                    $max = $len;
                                }
                            }

                            $columns[] = [
                                'title' => $title,
                                'len' => $max,
                                'title_len' => $title_len,
                            ];
                            if ($title_len > $max) {
                                return $title.str_repeat(' ', 1);
                            } else {
                                return $title.str_repeat(' ', $max - $title_len);
                            }
                        } else {
                            $item_len = Tools::strlen((string) $variable_value);

                            $columns[] = [
                                'title' => $title,
                                'len' => $item_len,
                                'title_len' => $title_len,
                            ];

                            if ($title_len > $item_len) {
                                return $title.str_repeat(' ', 1);
                            } else {
                                return $title.str_repeat(' ', $item_len - $title_len);
                            }
                        }
                    }
                }

                return ' ';
            }, $content);

            $content = preg_replace_callback('/\{\#(?:\s+?)(column)\((.*?)\)(?:\s+?)\#\}/', function ($match) use ($columns, &$column_index) {
                $column = $columns[$column_index++];
                if ($column['title_len'] > $column['len']) {
                    return $match[2].str_repeat(' ', $column['title_len'] - $column['len'] + 1);
                }

                return $match[2];
            }, $content);
        }

        if ($use_conditions) {
            $content = self::renderIfElse($content, $variables, false, false);
            $content = self::renderIfElse($content, $variables, false, true);

            $content = preg_replace_callback('/<!--\s+?@foreach\s+\{(?P<variable_name>\w+)\}\s+as\s+\{(?P<item>\w+)\}\s+?-->(?P<row>[\s\S]+?)<!--\s+?@endforeach\s+-->/', function ($match) use ($variables) {
                $variable_name = $match['variable_name'];
                $item = $match['item'];
                $row = $match['row'];

                if (array_key_exists($variable_name, $variables) && is_array($variables[$variable_name])) {
                    $loop_data = [];
                    foreach ($variables[$variable_name] as $variable_value) {
                        $row_conditions = self::renderIfElse($row, $variable_value, true, false);
                        $row_conditions = self::renderIfElse($row, $variable_value, true, true);
                        $row_render = preg_replace_callback('/\{'.preg_quote($item).'(\.(?P<key>[\w]+))?\}/', function ($row_match) use ($variable_value) {
                            if (is_array($variable_value)) {
                                if (array_key_exists($row_match['key'], $variable_value)) {
                                    return $variable_value[$row_match['key']];
                                }
                            } else {
                                return $variable_value;
                            }

                            return $row_match[0];
                        }, $row_conditions);
                        $loop_data[] = $row_render;
                    }

                    return implode("\n", $loop_data);
                }

                return $match[0];
            }, $content);
        }

        // render attributes
        $render_attribues = preg_replace_callback('/\{(\w+)(?:\.(\w+))?(\s+?(?:\w+)="[\s\S]+?")\}/', function ($match) use ($variables) {
            $variable = $match[0];
            $key = $match[1];
            $array_key = array_key_exists(2, $match) ? $match[2] : null;

            if (preg_match_all('/(\w+)="([\s\S]+?)"/', $match[3], $match_params)) {
                $data = [];
                foreach ($match_params[1] as $k => $value) {
                    $data[$value] = $match_params[2][$k];
                }

                foreach ($data as $date_key => $data_value) {
                    $attrs_exp = explode(',', $data_value);

                    $data[$date_key] = array_map(function ($row) {
                        $row_exp = explode(':', $row);
                        foreach ($row_exp as $kk => $value) {
                            $row_exp[$kk] = trim($value);
                        }

                        return $row_exp[0].'="'.$row_exp[1].'"';
                    }, $attrs_exp);

                    $data[$date_key] = trim(implode(' ', $data[$date_key]));
                }
            }

            $value = $variable;
            if (isset($array_key) && array_key_exists($key, $variables) && is_array($variables[$key]) && array_key_exists($array_key, $variables[$key])) {
                $value = $variables[$key][$array_key];
            } elseif (array_key_exists($key, $variables) && !is_array($variables[$key])) {
                $value = $variables[$key];
            }

            $value = preg_replace_callback('/%(\w+)%/', function ($value_match) use ($data) {
                if (array_key_exists($value_match[1], $data)) {
                    return $data[$value_match[1]];
                }

                return '';
            }, $value);

            return $value;
        }, $content);

        $render = preg_replace_callback('/\{(\w+)(?:\.(\w+))?\}/', function ($match) use ($variables) {
            $variable = $match[0];
            $key = $match[1];
            $array_key = array_key_exists(2, $match) ? $match[2] : null;

            if (isset($array_key) && array_key_exists($key, $variables) && is_array($variables[$key]) && array_key_exists($array_key, $variables[$key])) {
                return $variables[$key][$array_key];
            } elseif (array_key_exists($key, $variables) && !is_array($variables[$key])) {
                return $variables[$key];
            }

            return $variable;
        }, $render_attribues);

        return $render;
    }

    public static function renderIfElse($content, $variables = [], $match_iterator = false, $tag_conditions = false)
    {
        $regexp = '/<!--\s+?@if\s+?(\w+)\((?:\s+)?\{(\w+)\}(?:\s+)?\)\s+?(<=|<|>=|>|==|!=)\s+?([0-9.]+|\{\w+\}|(\'|\").*\5)\s+?-->([\s\S]+?)<!--\s+?@endif\s+?-->/';

        if ($tag_conditions) {
            $regexp = '/condition-if=\"(?:\s+)?(\w+)\((?:\s+)?\{(\w+)\}(?:\s+)?\)(?:\s+)?(<=|<|>=|>|==|!=)(?:\s)?([0-9.]+|\{\w+\}|(\'|\").*\5)(?:\s+)?\"(.*)?condition-endif(?:=\"\")?/';
        }

        if ($match_iterator) {
            $regexp = '/<!--\s+?@if\s+?(\w+)\((?:\s+)?\{\w+\.(\w+)\}(?:\s+)?\)\s+?(<=|<|>=|>|==|!=)\s+?([0-9.]+|\{\w+\}|(\'|\").*\5)\s+?-->([\s\S]+?)<!--\s+?@endif\s+?-->/';

            if ($tag_conditions) {
                $regexp = '/condition-if=\"(?:\s+)?(\w+)\((?:\s+)?\{\w+\.(\w+)\}(?:\s+)?\)(?:\s+)?(<=|<|>=|>|==|!=)(?:\s)?([0-9.]+|\{\w+\}|(\'|\").*\5)(?:\s+)?\"(.*)?condition-endif(?:=\"\")?/';
            }
        }

        return preg_replace_callback($regexp, function ($match) use ($variables, $tag_conditions) {
            $success = false;
            $has_else = false;
            $func_name = $match[1];
            $variable_name = $match[2];
            $sign = $match[3];
            $compare = $match[4];
            $full_condition_value = $match[6];
            $if_content = '';
            $else_content = '';
            $first = Tools::substr($compare, 0, 1);

            if (is_numeric($first)) {
                $compare = (int) $compare;
            } elseif ('\'' == $first || '"' == $first) {
                $compare = str_replace(['\'', '"'], '', $compare);
            } else {
                $compare = str_replace(['{', '}'], '', $compare);

                if (array_key_exists($compare, $variables)) {
                    $compare = $variables[$compare];
                } else {
                    $compare = null;
                }
            }

            $else_regex = '/<!--\s+?@else\s+?-->/';

            if ($tag_conditions) {
                $else_regex = '/condition-else(?:=\"\")?/';
            }

            if (preg_match($else_regex, $full_condition_value, $match_else)) {
                $start_pos = strpos($full_condition_value, $match_else[0]);
                $match_else_len = Tools::strlen($match_else[0]);

                $if_content = Tools::substr($full_condition_value, 0, $start_pos);
                $else_content = Tools::substr($full_condition_value, $start_pos + $match_else_len);
                $has_else = true;
            }

            if (array_key_exists($variable_name, $variables)) {
                $variable = $variables[$variable_name];

                if (function_exists($func_name)) {
                    $result = call_user_func($func_name, $variable);

                    switch ($sign) {
                        case '<':
                            $success = $result < $compare;
                            break;

                        case '<=':
                            $success = $result <= $compare;
                            break;

                        case '>':
                            $success = $result > $compare;
                            break;

                        case '>=':
                            $success = $result >= $compare;
                            break;

                        case '==':
                            $success = $result == $compare;
                            break;

                        case '!=':
                            $success = $result != $compare;
                            break;
                    }
                }
            }

            if ($has_else) {
                if ($success) {
                    return $if_content;
                } else {
                    return $else_content;
                }
            } else {
                if ($success) {
                    return $full_condition_value;
                } else {
                    return false;
                }
            }

            return $success ? $full_condition_value : '';
        }, $content);
    }

    public static function clearHTML($content)
    {
        return preg_replace_callback('/(>)([\s\S]+?)(<)/', function ($match) {
            return $match[1].trim($match[2]).$match[3];
        }, trim($content));
    }

    public static function getTitle($content)
    {
        if (preg_match('/<((?:\s+)?title(?:\s+)?>)([\s\S]+?)<\/\1/', $content, $match)) {
            return $match[2];
        }

        return '';
    }
}
