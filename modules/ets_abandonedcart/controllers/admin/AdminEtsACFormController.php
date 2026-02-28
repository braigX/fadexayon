<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(dirname(__FILE__) . '/AdminEtsACController.php');

abstract class AdminEtsACFormController extends AdminEtsACController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function validateRules($class_name = false)
    {
        if (!$this->fields_form)
            return false;
        if (!$class_name) {
            $class_name = $this->className;
        }
        $id = (int)Tools::getValue($this->identifier);

        /** @var $object ObjectModel */
        $object = new $class_name($id);

        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $emailTimingOption = 0;
        if ($object instanceof EtsAbancartReminder) {
            $campaign = new EtsAbancartCampaign((int)Tools::getValue('id_ets_abancart_campaign'));
            $emailTimingOption = $campaign->email_timing_option;
        }
        if (isset($this->fields_form['input']) && $this->fields_form['input']) {
            foreach ($this->fields_form['input'] as $key => $input) {
                if ($input['type'] == 'position')
                    continue;
                if (isset($input['lang']) && $input['lang']) {
                    if (isset($input['required']) && $input['required'] && $input['type'] != 'switch' && $this->requiredFields($key, $id_lang_default, isset($input['multiple']) && $input['multiple'] ? 1 : 0)) {
                        $this->errors[] = $input['label'] . ' ' . $this->l('is required', 'AdminEtsACFormController');
                    }
                } else {
                    if (isset($input['type']) && $input['type'] == 'file') {
                        if (isset($input['required']) && $input['required'] && $object->$key == '' && !isset($_FILES[$key]['size']))
                            $this->errors[] = $input['label'] . ' ' . $this->l('is required', 'AdminEtsACFormController');
                        else
                            $this->validateUpload($key);
                    } else {
                        $val = ($val = Tools::getValue($key)) && (is_array($val) || !Validate::isCleanHtml($val)) ? $val : '';
                        if (isset($input['required']) && $input['required'] && $input['type'] != 'switch' && $this->requiredFields($key, null, isset($input['multiple']) && $input['multiple'] ? 1 : 0)) {
                            $this->errors[] = $input['label'] . ' ' . $this->l('is required', 'AdminEtsACFormController');
                        } elseif (!is_array(Tools::getValue($key)) && isset($input['validate']) && $this->validateFields($key, $input)) {
                            $validate = $input['validate'];
                            if (!Validate::$validate(trim(Tools::getValue($key))))
                                $this->errors[] = $input['label'] . ' ' . $this->l('is invalid', 'AdminEtsACFormController');
                            unset($validate);
                        } elseif ($this->customValidate($key, $input)) {
                            continue;
                        } elseif (!is_array($val) && !Validate::isCleanHtml(trim($val))) {
                            $this->errors[] = $input['label'] . ' ' . $this->l('is required', 'AdminEtsACFormController');
                        }
                    }
                }
                $adt = ($adt = Tools::getValue('apply_discount_to')) && Validate::isCleanHtml($adt) ? $adt : '';
                $discountOption = ($discountOption = Tools::getValue('discount_option')) && Validate::isCleanHtml($discountOption) ? $discountOption : '';
                if ($key == 'apply_discount_to' && $adt == 'specific' && !(int)Tools::getValue('reduction_product')) {
                    if ($discountOption == 'auto')
                        $this->errors[] = $this->l('Specific product is required', 'AdminEtsACFormController');
                } elseif ($key == 'apply_discount_to' && $adt == 'selection') {
                    $selectedProduct = Tools::getValue('selected_product');
                    if (is_array($selectedProduct)) {
                        foreach ($selectedProduct as $k => $sp) {
                            if (!is_array($sp) && !Validate::isCleanHtml($sp)) {
                                unset($selectedProduct[$k]);
                            }
                        }
                    }
                    if (!$selectedProduct && $discountOption == 'auto')
                        $this->errors[] = $this->l('Selected product(s) is required', 'AdminEtsACFormController');
                    elseif (!is_array($selectedProduct) && $discountOption == 'auto') {
                        $this->errors[] = $this->l('Selected product(s) is invalid', 'AdminEtsACFormController');
                    }
                }
                if ($key == 'product_gift') {
                    $freeGift = (int)Tools::getValue('free_gift');
                    $giftProduct = (int)Tools::getValue('gift_product');
                    if ($discountOption == 'auto' && $freeGift && !$giftProduct) {
                        $this->errors[] = $this->l('Product for free gift is required', 'AdminEtsACFormController');
                    }
                }


                if ($key == 'customer_email_schedule_time' && $emailTimingOption == EtsAbancartReminder::CUSTOMER_EMAIL_SEND_AFTER_SCHEDULE_TIME) {
                    $scheduleTime = trim(Tools::getValue('customer_email_schedule_time'));
                    if (!$scheduleTime) {
                        $this->errors[] = $this->l('Schedule time is required', 'AdminEtsACFormController');
                    } elseif (!Validate::isDate(trim($scheduleTime))) {
                        $this->errors[] = $this->l('Schedule time is invalid', 'AdminEtsACFormController');
                    } elseif (strtotime($scheduleTime) < time()) {
                        $this->errors[] = $this->l('Schedule time must be greater than current time', 'AdminEtsACFormController');
                    }
                }
            }
        }
    }

    public function customValidate($key, $input)
    {
        if ($key == 'discount_code' && ($code = Tools::getValue($key)) && trim(Tools::getValue('discount_option')) === 'fixed' && (!Validate::isCleanHtml($code) || !CartRule::cartRuleExists($code))) {
            $this->errors[] = $input['label'] . ' ' . $this->l('is invalid', 'AdminEtsACFormController');
        }
        return count($this->errors) > 0;
    }

    protected function requiredFields($key, $id_lang_default = null, $multiple = 0)
    {
        $discount_option = ($discount_option = trim(Tools::getValue('discount_option'))) && Validate::isCleanHtml($discount_option) ? $discount_option : '';
        $apply_discount = ($apply_discount = trim(Tools::getValue('ETS_ABANCART_APPLY_DISCOUNT'))) && Validate::isCleanHtml($apply_discount) ? $apply_discount : '';

        switch (trim($key)) {
            case 'discount_code':
                return ($discount_option === 'fixed' && !trim(Tools::getValue($key)));
            case 'reduction_percent':
                return ($discount_option === 'auto' && $apply_discount === 'percent' && !trim(Tools::getValue($key)));
            case 'discount_name':
                return ($discount_option === 'auto' && !trim(Tools::getValue($key . '_' . $id_lang_default)));
            case 'discount_prefix':
            case 'apply_discount_in':
                return ($discount_option === 'auto' && !trim(Tools::getValue($key)));
            case 'reduction_amount':
                return ($discount_option === 'auto' && $apply_discount === 'amount' && !trim(Tools::getValue($key)));
        }
        if ($multiple) {
            return !($res = Tools::getValue($key . ($id_lang_default !== null ? '_' . $id_lang_default : ''), [])) || !is_array($res) || count($res) <= 0;
        }
        return trim(Tools::getValue($key . ($id_lang_default !== null ? '_' . $id_lang_default : ''), '')) == '';
    }

    protected function validateFields($key, $input)
    {
        $res = Tools::getValue($key) != '';
        $discount_option = ($discount_option = trim(Tools::getValue('discount_option'))) && Validate::isCleanHtml($discount_option) ? $discount_option : '';
        $apply_discount = ($apply_discount = trim(Tools::getValue('apply_discount'))) && Validate::isCleanHtml($apply_discount) ? $apply_discount : '';

        switch ($key) {
            case 'reduction_amount':
                $res = ($apply_discount === 'amount' && $discount_option === 'auto');
                $value = Tools::getValue($key);
                if (!$value || !Validate::isUnsignedFloat($value))
                    $value = 0;
                if ($res && $value <= 0)
                    $this->errors[] = $input['label'] . ' ' . $this->l('is invalid', 'AdminEtsACFormController');
                break;
            case 'reduction_percent':
                $res = ($apply_discount === 'percent' && $discount_option === 'auto');
                break;
            case 'discount_prefix':
            case 'apply_discount_in':
                $res = ($discount_option === 'auto');
                break;
        }

        return $res && method_exists('Validate', $input['validate']);
    }

    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
        /* Multilingual fields */
        $class_vars = get_class_vars(get_class($object));
        $fields = array();
        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach ($fields as $field => $params) {
            if (isset($params['lang']) && $params['lang']) {
                foreach ($languages as $l) {
                    if (Tools::isSubmit($field . '_' . (int)$l['id_lang']) && (!($value = Tools::getValue($field . '_' . (int)$l['id_lang'])) || !Validate::isCleanHtml($value))) {
                        $object->{$field}[(int)$l['id_lang']] = ($value = Tools::getValue($field . '_' . (int)$id_lang_default)) && Validate::isCleanHtml($value) ? $value : '';
                    }
                }
            }
        }

        if (Tools::getValue('action') == 'sendMail' && $table == 'ets_abancart_reminder' && $object instanceof EtsAbancartReminder) {
            $adt = ($adt = Tools::getValue('apply_discount_to')) && Validate::isCleanHtml($adt) ? $adt : '';
            if ($adt != 'selection') {
                $object->selected_product = null;
            }
            if ($adt == 'order') {
                $object->reduction_product = 0;
            } elseif ($adt == 'specific') {
                $object->reduction_product = (int)Tools::getValue('reduction_product');

            } elseif ($adt == 'cheapest') {
                $object->reduction_product = -1;
            } elseif ($adt == 'selection') {
                $object->reduction_product = -2;
                if (($selectedProducts = Tools::getValue('selected_product')) && is_array($selectedProducts)) {
                    $products = array_map('intval', $selectedProducts);
                    $object->selected_product = implode(',', $products);
                } else
                    $object->selected_product = null;
            }
            $freeGift = (int)Tools::getValue('free_gift');
            if (!$freeGift) {
                $object->gift_product = 0;
                $object->gift_product_attribute = 0;
            }
            $object->id_ets_abancart_form = (int)Tools::getValue('id_ets_abancart_form');
        }
    }

    public function validateUpload($key)
    {
        EtsAbancartTools::createImgDir();
        $file_dest = _PS_IMG_DIR_ . Ets_abandonedcart::ETS_ABC_UPLOAD_IMG_DIR;
        $post_content_size = EtsAbancartTools::getServerVars('CONTENT_LENGTH');
        if (($post_max_size = EtsAbancartTools::getPostMaxSizeBytes()) && ($post_content_size > $post_max_size)) {
            $this->errors[] = sprintf($this->l('The uploaded file(s) exceeds the post_max_size directive in php.ini (%s > %s)', 'AdminEtsACFormController'), $post_content_size, $post_max_size);
        } elseif (!@is_writable($file_dest) && !empty($_FILES[$key]['name'])) {
            $this->errors[] = sprintf($this->l('The directory "%s" is not able to write.', 'AdminEtsACFormController'), $file_dest);
        } elseif (isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name'])) {
            if ($uploadError = $this->checkUploadError($_FILES[$key]['error'], $_FILES[$key]['name'])) {
                $this->errors[] = $uploadError;
            } elseif ($_FILES[$key]['size'] > (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024) {
                $this->errors[] = sprintf($this->l('File is too large. Maximum size allowed: %s', 'AdminEtsACFormController'), EtsAbancartTools::formatBytes($post_max_size));
            } elseif ($_FILES[$key]['size'] > Ets_abandonedcart::DEFAULT_MAX_SIZE) {
                $this->errors[] = sprintf($this->l('File is too large. Current size is %1s, maximum size is %2s.', 'AdminEtsACFormController'), $_FILES[$key]['size'], Ets_abandonedcart::DEFAULT_MAX_SIZE);
            } elseif (isset($_FILES[$key]['name']) && $_FILES[$key]['name']) {
                if (!Validate::isFileName($_FILES[$key]['name'])) {
                    $this->errors[] = sprintf($this->l('Filename "%s" is invalid. Filename must only contain letters, numbers, underscores, periods, and hyphens.', 'AdminEtsACFormController'), $_FILES[$key]['name']);
                } else {
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
                    if (!in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                        $this->errors[] = sprintf($this->l('File "%s" type is not allowed', 'AdminEtsACFormController'), $_FILES[$key]['name']);
                    }
                }
            }
        }
    }

    public function checkUploadError($error_code, $file_name)
    {
        $error = 0;
        switch ($error_code) {
            case 1:
                $error = sprintf($this->l('File "%1s" uploaded exceeds %2s', 'AdminEtsACFormController'), $file_name, ini_get('upload_max_filesize'));
                break;
            case 2:
                $error = sprintf($this->l('The uploaded file exceeds %s', 'AdminEtsACFormController'), ini_get('post_max_size'));
                break;
            case 3:
                $error = sprintf($this->l('Uploaded file "%s" was only partially uploaded', 'AdminEtsACFormController'), $file_name);
                break;
            case 6:
                $error = $this->l('Missing temporary folder', 'AdminEtsACFormController');
                break;
            case 7:
                $error = sprintf($this->l('Failed to write file "%s" to disk', 'AdminEtsACFormController'), $file_name);
                break;
            case 8:
                $error = sprintf($this->l('A PHP extension stopped the file "%s" to upload', 'AdminEtsACFormController'), $file_name);
                break;
            default:
                break;
        }
        return $error;
    }

    public function uploadFiles($key, &$object)
    {
        if (isset($_FILES[$key]['tmp_name']) && !empty($_FILES[$key]['tmp_name'])) {
            $salt = Tools::strtolower(Tools::passwdGen(20));
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
            $image = $salt . '.' . $type;
            EtsAbancartTools::createImgDir();
            $file_name = _PS_IMG_DIR_ . Ets_abandonedcart::ETS_ABC_UPLOAD_IMG_DIR . $image;

            if (@file_exists($file_name)) {
                $this->errors[] = $this->l('File name already exists. Try to rename the file and upload again', 'AdminEtsACFormController');
            } else {
                $image_size = @getimagesize($_FILES[$key]['tmp_name']);
                if (isset($_FILES[$key]) && !empty($_FILES[$key]['tmp_name']) && !empty($image_size) && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                    if (!($temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !@move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name)) {
                        $this->errors[] = $this->l('An error occurred while uploading the image.', 'AdminEtsACFormController');
                    } elseif (!ImageManager::resize($temp_name, $file_name, null, null, $type))
                        $this->errors[] = sprintf($this->l('An error occurred while copying this image: %s', 'AdminEtsACFormController'), Tools::stripslashes($image));
                }
                EtsAbancartHelper::unlink($temp_name);
            }
            if (!$this->errors)
                $object->{$key} = $image;
        }
    }

    public function displayFormatNumber($number)
    {
        return Tools::displayPrice($number, (int)Configuration::get('PS_CURRENCY_DEFAULT'));
    }
    
    public function displayTimeElapsedString($datetime, $tr)
    {
        if (empty($datetime)) return null;
        if (!empty($tr)) {
            $attrs = [
                'class' => 'time-elapsed-string',
                'title' => $datetime
            ];
            return EtsAbancartTools::displayText($this->timeElapsedString($datetime), 'span', $attrs);
        }
    }

    static $translate = [];

    public function timeElapsedString($datetime, $full = false)
    {
        if (!$datetime)
            return 0;
        if (empty(self::$translate)) {
            self::$translate = [
                'ago' => $this->l(' ago', 'AdminEtsACFormController'),
                'just_now' => $this->l('Just now', 'AdminEtsACFormController'),
                'year' => $this->l('year', 'AdminEtsACFormController'),
                'years' => $this->l('years', 'AdminEtsACFormController'),
                'month' => $this->l('month', 'AdminEtsACFormController'),
                'months' => $this->l('months', 'AdminEtsACFormController'),
                'week' => $this->l('week', 'AdminEtsACFormController'),
                'weeks' => $this->l('weeks', 'AdminEtsACFormController'),
                'day' => $this->l('day', 'AdminEtsACFormController'),
                'days' => $this->l('days', 'AdminEtsACFormController'),
                'hour' => $this->l('hour', 'AdminEtsACFormController'),
                'hours' => $this->l('hours', 'AdminEtsACFormController'),
                'minute' => $this->l('minute', 'AdminEtsACFormController'),
                'minutes' => $this->l('minutes', 'AdminEtsACFormController'),
                'second' => $this->l('second', 'AdminEtsACFormController'),
                'seconds' => $this->l('seconds', 'AdminEtsACFormController'),
            ];
        }
        $now = new DateTime(date('Y-m-d H:i:s'));
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => self::$translate['year'],
            'm' => self::$translate['month'],
            'w' => self::$translate['week'],
            'd' => self::$translate['day'],
            'h' => self::$translate['hour'],
            'i' => self::$translate['minute'],
            's' => self::$translate['second'],
        );

        $string2 = array(
            'y' => self::$translate['years'],
            'm' => self::$translate['months'],
            'w' => self::$translate['weeks'],
            'd' => self::$translate['days'],
            'h' => self::$translate['hours'],
            'i' => self::$translate['minutes'],
            's' => self::$translate['seconds'],
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . ($diff->$k > 1 ? $string2[$k] : $v);
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);

        return $string ? implode(', ', $string) . self::$translate['ago'] : self::$translate['just_now'];
    }
}