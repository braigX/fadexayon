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

class NewsletterProMailChimpUsers
{
    public $user = null;
    public $users = [];
    public $errors = [];

    public $input_user;

    const ERROR_EMAIL_NOT_SET = 101;

    const USER_TYPE_CUSTOMER = 'Customer';
    const USER_TYPE_VISITOR = 'Visitor';
    const USER_TYPE_ADDED = 'Added';

    const EMAIL_TYPE_HTML = 'html';
    const EMAIL_TYPE_TEXT = 'text';

    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_CLEANED = 'cleaned';
    const STATUS_PENDING = 'pending';

    public function addUserVar($name, $value)
    {
        $this->user['merge_fields'][$name] = $value;
    }

    public function inputUserGet($name)
    {
        if (isset($this->input_user[$name])) {
            return $this->input_user[$name];
        }

        return false;
    }

    public function inputUserExists($name)
    {
        if (isset($this->input_user[$name])) {
            return true;
        }

        return false;
    }

    public function addUser($input_user = [])
    {
        $this->input_user = $input_user;
        // Subscriber's current status. Possible values: "subscribed", "unsubscribed", "cleaned", or "pending".
        $this->user = [
            // 'email_address' => '', // OK
            'email_type' => self::EMAIL_TYPE_HTML,
            // 'status' => 'subscribed', // OK
            'merge_fields' => [],
            'interests' => [],
            // 'language' => 'en', // OK
            // 'ip_signup' => '',
            // 'timestamp_signup' => '',
            // 'ip_opt' => '', // OK
            // 'timestamp_opt' => '',
        ];

        if ($this->inputUserExists('email')) {
            $this->setEmail($this->inputUserGet('email'));
        } else {
            $this->addError('The field email is not set.', self::ERROR_EMAIL_NOT_SET);

            return false;
        }

        if ($this->inputUserExists('firstname')) {
            $this->setFName($this->inputUserGet('firstname'));
        }

        if ($this->inputUserExists('lastname')) {
            $this->setLName($this->inputUserGet('lastname'));
        }

        if ($this->inputUserExists('shop')) {
            $this->setShop($this->inputUserGet('shop'));
        }

        if ($this->inputUserExists('language')) {
            $this->setLanguage($this->inputUserGet('language'));
        }

        if ($this->inputUserExists('user_type')) {
            $this->setUserType($this->inputUserGet('user_type'));
        }

        if ($this->inputUserExists('ip')) {
            $this->setIP($this->inputUserGet('ip'));
        }

        if ($this->inputUserExists('lang_iso')) {
            $this->setLanguageISO($this->inputUserGet('lang_iso'));
        }

        if ($this->inputUserExists('phone')) {
            $this->setPhone($this->inputUserGet('phone'));
        }

        if ($this->inputUserExists('birthday')) {
            $this->setBirthday($this->inputUserGet('birthday'));
        }

        if ($this->inputUserExists('birthday')) {
            $this->setBirthday($this->inputUserGet('birthday'));
        }

        if ($this->inputUserExists('last_order')) {
            $this->setLastOrder($this->inputUserGet('last_order'));
        }

        if ($this->inputUserExists('date_add')) {
            $this->setDateAdd($this->inputUserGet('date_add'));
        }

        if ($this->inputUserExists('date')) {
            $this->setDate($this->inputUserGet('date'));
        }

        if ($this->inputUserExists('subscribed')) {
            $this->setSubscribed($this->inputUserGet('subscribed'));
        }

        if ($this->inputUserExists('phone_mobile')) {
            $this->setPhoneMobile($this->inputUserGet('phone_mobile'));
        }

        if ($this->inputUserExists('company')) {
            $this->setCompany($this->inputUserGet('company'));
        }

        if ($this->inputUserExists('groups')) {
            $this->setInterest($this->inputUserGet('groups'));
        }

        if ($this->inputUserExists('address')) {
            $this->setAddress($this->inputUserGet('address'));
        }

        $this->users[] = $this->getUser();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setEmail($email)
    {
        $this->user['email_address'] = $email;
        $this->addUserVar('EMAIL', $email);
    }

    public function setFName($fname)
    {
        $this->addUserVar('FNAME', $fname);
    }

    public function setLName($lname)
    {
        $this->addUserVar('LNAME', $lname);
    }

    public function setInterest($groups = [])
    {
        $chimpGroups = pqnp_config('CHIMP.CUSTOMERS_GROUP_IDS');
        $interests = [];
        $groupsIds = [];
        if (isset($groups['groups'])) {
            $groupsIds = $groups['groups'];
        }

        if (count($groupsIds) > 0) {
            foreach ($groupsIds as $groupId) {
                if (array_key_exists($groupId, $chimpGroups) && !array_key_exists($groupId, $interests)) {
                    $interests[$chimpGroups[$groupId]] = true;
                }
            }
        }

        $this->user['interests'] = $interests;
    }

    public function setShop($shop)
    {
        $this->addUserVar('SHOP', $shop);
    }

    public function setLanguage($language)
    {
        $this->addUserVar('LANGUAGE', $language);
    }

    public function setUserType($user_type)
    {
        $this->addUserVar('USER_TYPE', $user_type);
    }

    public function setLastOrder($date)
    {
        $date_fromated = self::makeDate($date);
        $this->addUserVar('LAST_ORDER', $date_fromated);
    }

    public function setSubscribed($subscribed)
    {
        $this->user['status'] = (bool) $subscribed ? self::STATUS_SUBSCRIBED : self::STATUS_UNSUBSCRIBED;

        $value = 'yes';
        switch ($subscribed) {
            case true:
                $value = 'yes';
                break;
            case false:
                $value = 'no';
                break;
            default:
                $value = 'yes';
                break;
        }

        $this->addUserVar('SUBSCRIBED', $value);
    }

    public function setPhoneMobile($mobile)
    {
        if (isset($mobile) && $mobile) {
            $mobile_formated = self::formatPhone($mobile);
            $this->addUserVar('PHONE_MOB', $mobile_formated);
        }
    }

    public function setCompany($company)
    {
        $this->addUserVar('COMPANY', $company);
    }

    public function setIP($ip)
    {
        $this->addUserVar('OPTIN_IP', $ip);
        $this->user['ip_opt'] = $ip;
    }

    public function setBirthday($date)
    {
        $date_fromated = self::makeDate($date, 'm/d');
        $this->addUserVar('BIRTHDAY', $date_fromated);
    }

    public function setLanguageISO($iso)
    {
        $iso = Tools::strtolower($iso);
        $this->addUserVar('MC_LANGUAGE', $iso);

        $this->user['language'] = $iso;
    }

    public function setAddress($address)
    {
        $newAddress = [];
        if (isset($address['addr1'])) {
            $newAddress['addr1'] = (string) $address['addr1'];
        } else {
            $newAddress['addr1'] = '';
        }

        if (isset($address['addr2'])) {
            $newAddress['addr2'] = (string) $address['addr2'];
        } else {
            $newAddress['addr2'] = '';
        }

        if (isset($address['city'])) {
            $newAddress['city'] = (string) $address['city'];
        } else {
            $newAddress['city'] = '';
        }

        if (isset($address['state'])) {
            $newAddress['state'] = (string) $address['state'];
        } else {
            $newAddress['state'] = '';
        }

        if (isset($address['zip'])) {
            $newAddress['zip'] = (string) $address['zip'];
        } else {
            $newAddress['zip'] = '';
        }

        if (isset($address['country'])) {
            $newAddress['country'] = (string) Tools::strtoupper($address['country']);
        } else {
            $newAddress['country'] = '';
        }

        $this->addUserVar('ADDRESS', $newAddress);
    }

    public function setDateAdd($date)
    {
        $date_fromated = self::makeDate($date);
        $this->addUserVar('DATE_ADD', $date_fromated);
    }

    public function setDate($date)
    {
        $date_fromated = self::makeDate($date);
        $this->addUserVar('DATE', $date_fromated);
    }

    public function setPhone($phone)
    {
        if (isset($phone) && $phone) {
            $phone_formated = self::formatPhone($phone);
            $this->addUserVar('PHONE', $phone_formated);
        }
    }

    public static function makeDate($date, $format = 'm/d/Y')
    {
        return NewsletterProMailChimpApi::makeDate($date, $format);
    }

    public static function formatPhone($phone)
    {
        return NewsletterProMailChimpApi::formatPhone($phone);
    }

    public function addError($error, $code = null)
    {
        $add_error = [
            'code' => $code,
            'error' => Tools::displayError($error),
        ];

        $this->errors[] = $add_error;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors($only_errors = false, $collapse_same_code = false)
    {
        $errors = $this->errors;

        if ($collapse_same_code) {
            $errors_collapse = [];
            $errors_coldes = [];

            foreach ($errors as $error) {
                if (!in_array($error['code'], $errors_coldes)) {
                    $errors_collapse[] = $error;
                    $errors_coldes[] = $error['code'];
                }
            }

            $errors = $errors_collapse;
        }

        $return_errors = [];
        if ($only_errors) {
            foreach ($errors as $error) {
                $return_errors[] = $error['error'];
            }
        } else {
            $return_errors = $errors;
        }

        return $return_errors;
    }
}
