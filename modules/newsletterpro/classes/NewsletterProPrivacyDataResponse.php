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

class NewsletterProPrivacyDataResponse
{
    const TYPE_CLEAR = 1;

    const TYPE_SEARCH = 2;

    const TYPE_EXPORT = 3;

    private $type;

    private $name;

    private $email;

    private $count;

    private $export;

    private $errors;

    private $errors_trace;

    public function __construct($type, $name, $email)
    {
        $this->type = $type;
        $this->name = $name;
        $this->email = $email;
        $this->count = 0;
        $this->export = [];
        $this->errors = [];
        $this->errors_trace = [];
    }

    public function setCount($count)
    {
        $this->count = (int) $count;

        return $this;
    }

    public function addToCount($number)
    {
        $this->count += (int) $number;

        return $this;
    }

    public function addError($error)
    {
        if (is_string($error)) {
            $this->errors[] = $error;
        } else {
            foreach ($error as $err) {
                $this->errors[] = $err;
            }
        }

        return $this;
    }

    public function addErrorTrace($error)
    {
        if (is_string($error)) {
            $this->errors_trace[] = $error;
        } else {
            foreach ($error as $err) {
                $this->errors_trace[] = $err;
            }
        }

        return $this;
    }

    public function addException(Exception $e)
    {
        $this->errors[] = $e->getMessage();
        $this->errors_trace[] = $e->__toString();

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function appendErrors(array &$obj)
    {
        foreach ($this->errors as $error) {
            $obj[] = $error;
        }
    }

    public function setExport(array $export)
    {
        $this->export = [$export];
    }

    public function addToExport(array $data)
    {
        $this->export[] = $data;
    }

    public function getExport()
    {
        return $this->export;
    }

    public function hasExport()
    {
        return $this->export;
    }

    public function toArray()
    {
        if (self::TYPE_EXPORT === $this->type) {
            return [
                'type' => $this->type,
                'name' => $this->name,
                'export' => $this->export,
                'errors' => $this->errors,
                'errors_trace' => $this->errors_trace,
            ];
        }

        return [
            'type' => $this->type,
            'name' => $this->name,
            'count' => $this->count,
            'errors' => $this->errors,
            'errors_trace' => $this->errors_trace,
        ];
    }

    public static function collectionToArray($collection)
    {
        return array_map(function (NewsletterProPrivacyDataResponse $response) {
            return $response->toArray();
        }, $collection);
    }
}
