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

class NewsletterProValidate extends NewsletterProObjectClass
{
    const ACTION_UPDATE = 1;

    const ACTION_ADD = 2;

    private $data;

    private $obj_model;

    private $obj_model_definition;

    private $obj_model_classname;

    private $loaded = false;

    public static function newInstance()
    {
        return new self();
    }

    public function set($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif (is_object($data)) {
            if (!($data instanceof ObjectModel)) {
                throw new Exception('The Validate->set($data) is not an instance of ObjectModel.');
            }

            $this->obj_model = $data;
            $this->obj_model_classname = get_class($data);
            $properties = get_class_vars($this->obj_model_classname);
            $this->obj_model_definition = $properties['definition'];
        } else {
            throw new Exception('The Validate->set($data) must be the type of Array or ObjectModel.');
        }

        $this->loaded = true;

        return $this;
    }

    public function merge(array $data)
    {
        if (!isset($this->data)) {
            $this->data = [];
        }

        $this->data = array_merge($this->data, $data);
        $this->loaded = true;

        return $this;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function get($key = null, $grep_fields = [])
    {
        if (!isset($key)) {
            if (!empty($grep_fields)) {
                $data = [];
                foreach ($grep_fields as $field_name) {
                    if ($this->has($field_name)) {
                        $data[$field_name] = $this->data[$field_name];
                    }
                }

                return $data;
            }

            return $this->data;
        }
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return null;
    }

    public function grep($fields = [])
    {
        $data = [];

        foreach ($fields as $field_name) {
            if ($this->has($field_name)) {
                $data[$field_name] = $this->data[$field_name];
            }
        }

        return $data;
    }

    public function success(&$errors = [], &$form_errors = [], $fields = [])
    {
        if (!$this->loaded) {
            throw new Exception('You forgot to validate the Validate->data. You must call the function Validate->set() or Validate->merge().');
        }

        if (isset($this->obj_model)) {
            $obj_model_fields = $this->obj_model_definition['fields'];

            foreach ($fields as $field_name => $filed_data) {
                if (array_key_exists($field_name, $obj_model_fields)) {
                    $fields[$field_name]['type'] = $obj_model_fields[$field_name]['type'];

                    $message = sprintf($this->l('The field [%s] is not valid.'), $field_name);

                    if (array_key_exists('default_message', $filed_data)) {
                        $message = $filed_data['default_message'];
                    }

                    if (!array_key_exists('validate', $filed_data)) {
                        $fields[$field_name]['validate'] = [];
                    }

                    array_push($fields[$field_name]['validate'], [
                        'func' => $obj_model_fields[$field_name]['validate'],
                        'message' => $message,
                    ]);

                    $this->data[$field_name] = $this->obj_model->{$field_name};
                }
            }
        }

        foreach ($fields as $field_name => $filed_data) {
            $form_error = false;
            if (array_key_exists('form_error', $filed_data)) {
                $form_error = $filed_data['form_error'];
            }

            if (!$this->has($field_name)) {
                $msg = sprintf($this->l('The field name [%s] is missing.'), $field_name);

                if ($form_error) {
                    if (is_string($form_error)) {
                        $form_errors[$form_error] = $msg;
                    } else {
                        $form_errors[$field_name] = $msg;
                    }
                } else {
                    $errors[] = $msg;
                }
                continue;
            }

            $value = $this->get($field_name);

            if (array_key_exists('modifier', $filed_data)) {
                if (!is_array($filed_data['modifier'])) {
                    throw new Exception('The modifier should be the type of array.');
                }

                foreach ($filed_data['modifier'] as $modifier) {
                    if (false !== strpos($modifier, '::')) {
                        $static_class_exp = explode('::', $modifier);
                        $static_class_name = $static_class_exp[0];
                        $static_func_name = $static_class_exp[1];

                        if (!method_exists($static_class_name, $static_func_name)) {
                            throw new Exception(sprintf('Invalid function %s.', $modifier));
                        }
                        $value = call_user_func_array($modifier, [$value]);
                    } elseif (function_exists($modifier)) {
                        $value = call_user_func($modifier, $value);
                    } else {
                        throw new Exception(sprintf('The modifier function [%s] does not exists.', $modifier));
                    }
                }
            }

            if (array_key_exists('validate', $filed_data)) {
                $validators = $filed_data['validate'];

                foreach ($validators as $validate) {
                    if (is_string($validate['func'])) {
                        if (false === strpos($validate['func'], '::')) {
                            $static_call = 'Validate::'.$validate['func'];
                        } else {
                            $static_call = $validate['func'];
                        }

                        $static_class_exp = explode('::', $static_call);
                        $static_class_name = $static_class_exp[0];
                        $static_func_name = $static_class_exp[1];

                        if (!method_exists($static_class_name, $static_func_name)) {
                            throw new Exception(sprintf('Invalid function %s.', $static_call));
                        }

                        if (!call_user_func_array($static_call, [$value])) {
                            if ($form_error) {
                                if (is_string($form_error)) {
                                    $form_errors[$form_error] = $validate['message'];
                                } else {
                                    $form_errors[$field_name] = $validate['message'];
                                }
                            } else {
                                $errors[] = $validate['message'];
                            }

                            if (array_key_exists('error_break', $filed_data) && (bool) $filed_data['error_break']) {
                                return;
                            }
                            break;
                        }
                    } elseif (is_callable($validate['func'])) {
                        $callback = $validate['func'];
                        $callback_message = $callback($value);

                        if ($callback_message) {
                            if ($form_error) {
                                if (is_string($form_error)) {
                                    $form_errors[$form_error] = $callback_message;
                                } else {
                                    $form_errors[$field_name] = $callback_message;
                                }
                            } else {
                                $errors[] = $callback_message;
                            }

                            if (array_key_exists('error_break', $filed_data) && (bool) $filed_data['error_break']) {
                                return;
                            }
                            break;
                        }
                    }
                }
            }

            if (array_key_exists('type', $filed_data)) {
                switch ((int) $filed_data['type']) {
                    case ObjectModel::TYPE_INT:
                        $value = (int) $value;
                        break;

                    case ObjectModel::TYPE_BOOL:
                        $value = (bool) $value;
                        break;

                    case ObjectModel::TYPE_FLOAT:
                        $value = (float) $value;
                        break;

                    case ObjectModel::TYPE_STRING:
                    default:
                        $value = (string) $value;
                        break;
                }
            }

            if ($this->has($field_name)) {
                $this->data[$field_name] = $value;
            }

            if (isset($this->obj_model) && array_key_exists($field_name, $this->obj_model_definition['fields'])) {
                $this->obj_model->{$field_name} = $value;
            }
        }

        return empty($errors) && empty($form_errors);
    }

    public static function isFilled($value)
    {
        return Tools::strlen(trim((string) $value)) > 0;
    }

    public static function isValidName($name)
    {
        return preg_match('/^[0-9a-zA-Z\s_\.àâçéèêëîïôûùüÿñæœčšđžćČŠĐĆŽİıÖöÜüÇçĞğŞş₤\(\)\-]+$/', $name);
    }

    public static function isValidReference($reference_prefix)
    {
        if (0 == Tools::strlen($reference_prefix)) {
            return true;
        } elseif (preg_match('/^[A-Za-z]+$/', $reference_prefix)) {
            return true;
        }

        return false;
    }
}
