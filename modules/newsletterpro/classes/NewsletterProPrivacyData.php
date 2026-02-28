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

class NewsletterProPrivacyData
{
    public static $anonymous_email = 'anonymous@anonymous.com';

    public static $log_files = [
        'errors.log',
        'info.log',
        'send.log',
        'task.log',
    ];

    public function __construct()
    {
    }

    public function search($email)
    {
        $results = [
            'info' => [
                'db_prefix' => _DB_PREFIX_,
                'is_email' => Validate::isEmail($email),
            ],
            'tables' => NewsletterProPrivacyDataResponse::collectionToArray($this->serachTables($email)),
            'logs' => NewsletterProPrivacyDataResponse::collectionToArray($this->searchLogs($email)),
            'csv' => NewsletterProPrivacyDataResponse::collectionToArray($this->searchCSV($email)),
        ];

        return $results;
    }

    public function clear($email)
    {
        $results = [
            'info' => [
                'db_prefix' => _DB_PREFIX_,
                'is_email' => Validate::isEmail($email),
            ],
            'tables' => NewsletterProPrivacyDataResponse::collectionToArray($this->clearTables($email)),
            'logs' => NewsletterProPrivacyDataResponse::collectionToArray($this->clearLogs($email)),
            'csv' => NewsletterProPrivacyDataResponse::collectionToArray($this->clearCSV($email)),
        ];

        return $results;
    }

    public function export($email)
    {
        $responses = [
            NewsletterProEmail::exportPrivacy($email),
            NewsletterProEmailExclusion::exportPrivacy($email),
            NewsletterProForward::exportPrivacy($email),
            NewsletterProFwdUnsubscribed::exportPrivacy($email),
            NewsletterProSend::exportPrivacy($email),
            NewsletterProSendStep::exportPrivacy($email),
            NewsletterProSubscribers::exportPrivacy($email),
            NewsletterProSubscribersTemp::exportPrivacy($email),
            NewsletterProSubscriptionConsent::exportPrivacy($email),
            NewsletterProTask::exportPrivacy($email),
            NewsletterProTaskStep::exportPrivacy($email),
            NewsletterProUnsubscribed::exportPrivacy($email),
            NewsletterProCustomerCategory::exportPrivacy($email),
            NewsletterProCustomerListOfInterests::exportPrivacy($email),
            $this->emailsubscriptionExportPrivacy($email),
            $this->newsletterExportPrivacy($email),
            $this->customerExportPrivacy($email),
        ];

        $export = [];
        $errors = [];

        foreach ($responses as $response) {
            $data = $response->toArray();
            if (!empty($data['export'])) {
                foreach ($data['export'] as $value) {
                    $export[] = $value;
                }
            }

            if ($response->hasErrors()) {
                $response->appendErrors($errors);
            }
        }

        return [
            'data' => $export,
            'errors' => $errors,
        ];
    }

    public function hookActionDelete($email)
    {
        $response = [];

        foreach ($this->clearTables($email) as $response_obj) {
            if ($response_obj->hasErrors()) {
                $response_obj->appendErrors($response);
            }
        }

        foreach ($this->clearLogs($email) as $response_obj) {
            if ($response_obj->hasErrors()) {
                $response_obj->appendErrors($response);
            }
        }

        foreach ($this->clearCSV($email) as $response_obj) {
            if ($response_obj->hasErrors()) {
                $response_obj->appendErrors($response);
            }
        }

        if (empty($response)) {
            return true;
        }

        return $response;
    }

    private function clearTables($email)
    {
        return [
            NewsletterProEmail::clearPrivacy($email),
            NewsletterProEmailExclusion::clearPrivacy($email),
            NewsletterProForward::clearPrivacy($email),
            NewsletterProFwdUnsubscribed::clearPrivacy($email),
            NewsletterProSend::clearPrivacy($email),
            NewsletterProSendStep::clearPrivacy($email),
            NewsletterProSubscribers::clearPrivacy($email),
            NewsletterProSubscribersTemp::clearPrivacy($email),
            NewsletterProSubscriptionConsent::clearPrivacy($email),
            NewsletterProTask::clearPrivacy($email),
            NewsletterProTaskStep::clearPrivacy($email),
            NewsletterProUnsubscribed::clearPrivacy($email),
            NewsletterProCustomerCategory::clearPrivacy($email),
            NewsletterProCustomerListOfInterests::clearPrivacy($email),
            $this->emailsubscriptionClearPrivacy($email),
            $this->newsletterClearPrivacy($email),
            $this->customerClearPrivacy($email),
        ];
    }

    private function clearLogs($email)
    {
        $responses = [];
        $logs_dir = _NEWSLETTER_PRO_DIR_.'/logs/';

        foreach (self::$log_files as $name) {
            $filename = $logs_dir.$name;

            if (file_exists($filename) && is_readable($filename)) {
                $content = Tools::file_get_contents($filename);
                $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, $name, $email);

                if (preg_match_all('/'.preg_quote($email).'[^\w]/', $content, $match)) {
                    if (count($match) > 0) {
                        $new_content = preg_replace('/'.preg_quote($email).'([^\w])/', self::$anonymous_email.'${1}', $content);
                        if (false !== file_put_contents($filename, $new_content)) {
                            $response->addToCount(count($match[0]));
                        } else {
                            $response->addError(sprintf(NewsletterPro::getInstance()->l('Unable to write the file [%s].'), $filename));
                        }
                    }
                }

                $responses[] = $response;
            }
        }

        return $responses;
    }

    private function clearCSV($email)
    {
        $responses = [];

        $csv_filename = _NEWSLETTER_PRO_DIR_.'/csv/import/';

        $files = NewsletterProTools::getDirectoryIterator($csv_filename, '/.csv$/i');

        foreach ($files as $file) {
            $name = $file->getFilename();
            $filename = $file->getPathname();

            if (!$file->isDot() && 'sample.csv' !== $name) {
                $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, $name, $email);

                if ($file->isReadable()) {
                    $content = Tools::file_get_contents($filename);

                    if (preg_match_all('/'.preg_quote($email).'[^\w]/', $content, $match)) {
                        if (count($match) > 0) {
                            $new_content = preg_replace('/.*'.preg_quote($email).'([^\w]).*(\s+)?/', '', $content);
                            if (false !== file_put_contents($filename, $new_content)) {
                                $response->addToCount(count($match[0]));
                            } else {
                                $response->addError(sprintf(NewsletterPro::getInstance()->l('Unable to write the file [%s].'), $filename));
                            }
                        }
                    }
                }

                $responses[] = $response;
            }
        }

        return $responses;
    }

    private function serachTables($email)
    {
        return [
            NewsletterProEmail::privacySerach($email),
            NewsletterProEmailExclusion::privacySerach($email),
            NewsletterProForward::privacySerach($email),
            NewsletterProFwdUnsubscribed::privacySerach($email),
            NewsletterProSend::privacySerach($email),
            NewsletterProSendStep::privacySerach($email),
            NewsletterProSubscribers::privacySerach($email),
            NewsletterProSubscribersTemp::privacySerach($email),
            NewsletterProSubscriptionConsent::privacySerach($email),
            NewsletterProTask::privacySerach($email),
            NewsletterProTaskStep::privacySerach($email),
            NewsletterProUnsubscribed::privacySerach($email),
            NewsletterProCustomerCategory::privacySerach($email),
            NewsletterProCustomerListOfInterests::privacySerach($email),
            $this->emailsubscriptionPrivacySerach($email),
            $this->newsletterPrivacySerach($email),
            $this->customerPrivacySerach($email),
        ];
    }

    private function searchLogs($email)
    {
        $responses = [];
        $logs_dir = _NEWSLETTER_PRO_DIR_.'/logs/';

        foreach (self::$log_files as $name) {
            $filename = $logs_dir.$name;

            if (file_exists($filename) && is_readable($filename)) {
                $content = Tools::file_get_contents($filename);
                $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, $name, $email);

                if (preg_match_all('/'.preg_quote($email).'[^\w]/', $content, $match)) {
                    if (count($match) > 0) {
                        $response->addToCount(count($match[0]));
                    }
                }

                $responses[] = $response;
            }
        }

        return $responses;
    }

    private function searchCSV($email)
    {
        $responses = [];

        $csv_filename = _NEWSLETTER_PRO_DIR_.'/csv/import/';

        $files = NewsletterProTools::getDirectoryIterator($csv_filename, '/.csv$/i');

        foreach ($files as $file) {
            $name = $file->getFilename();
            $filename = $file->getPathname();

            if (!$file->isDot() && 'sample.csv' !== $name) {
                $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, $name, $email);

                if ($file->isReadable()) {
                    $content = Tools::file_get_contents($file->getPathname());

                    if (preg_match_all('/'.preg_quote($email).'[^\w]/', $content, $match)) {
                        if (count($match) > 0) {
                            $response->addToCount(count($match[0]));
                        }
                    }
                }

                $responses[] = $response;
            }
        }

        return $responses;
    }

    private function emailsubscriptionPrivacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'emailsubscription', $email);

        if (!NewsletterProTools::tableExists('emailsubscription')) {
            return $response;
        }

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'emailsubscription` WHERE `email` = "'.pSQL($email).'"
            ');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function emailsubscriptionExportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'emailsubscription', $email);

        if (!NewsletterProTools::tableExists('emailsubscription')) {
            return $response;
        }

        try {
            $results = Db::getInstance()->executeS('
                SELECT * FROM `'._DB_PREFIX_.'emailsubscription` WHERE `email` = "'.pSQL($email).'"
            ');

            if (count($results) > 0) {
                $data = null;
                foreach ($results as $row) {
                    if (true == (bool) $row['active']) {
                        $data = $row;
                        break;
                    }
                }

                if (!isset($data)) {
                    $data = $row;
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('Subscription list') => '',
                    NewsletterPro::getInstance()->l('Subscribed') => ((int) $data['active'] ? NewsletterPro::getInstance()->l('Yes') : NewsletterPro::getInstance()->l('No')),
                    NewsletterPro::getInstance()->l('Email') => (string) $data['email'],
                    NewsletterPro::getInstance()->l('IP address') => (string) $data['ip_registration_newsletter'],
                    NewsletterPro::getInstance()->l('Date add') => (string) $data['newsletter_date_add'],
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function newsletterPrivacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'newsletter', $email);

        if (!NewsletterProTools::tableExists('newsletter')) {
            return $response;
        }

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter` WHERE `email` = "'.pSQL($email).'"
            ');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function newsletterExportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'newsletter', $email);

        if (!NewsletterProTools::tableExists('newsletter')) {
            return $response;
        }

        try {
            $results = Db::getInstance()->executeS('
                SELECT * FROM `'._DB_PREFIX_.'newsletter` WHERE `email` = "'.pSQL($email).'"
            ');

            if (count($results) > 0) {
                $data = null;
                foreach ($results as $row) {
                    if (true == (bool) $row['active']) {
                        $data = $row;
                        break;
                    }
                }

                if (!isset($data)) {
                    $data = $row;
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('Newsletter list') => '',
                    NewsletterPro::getInstance()->l('Subscribed') => ((int) $data['active'] ? NewsletterPro::getInstance()->l('Yes') : NewsletterPro::getInstance()->l('No')),
                    NewsletterPro::getInstance()->l('Email') => (string) $data['email'],
                    NewsletterPro::getInstance()->l('IP address') => (string) $data['ip_registration_newsletter'],
                    NewsletterPro::getInstance()->l('Date add') => (string) $data['newsletter_date_add'],
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function customerPrivacySerach($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_SEARCH, 'customer', $email);

        try {
            $count = (int) Db::getInstance()->getValue('
                SELECT COUNT(*) FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($email).'"
            ');
            $response->addToCount($count);
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function customerExportPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_EXPORT, 'customer', $email);

        try {
            $results = Db::getInstance()->executeS('
                SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL($email).'"
            ');

            if (count($results) > 0) {
                $data = null;
                foreach ($results as $row) {
                    if (true == (bool) $row['active']) {
                        $data = $row;
                        break;
                    }
                }

                if (!isset($data)) {
                    $data = $row;
                }

                $response->addToExport([
                    NewsletterPro::getInstance()->l('Customers list') => '',
                    NewsletterPro::getInstance()->l('Subscribed') => ((int) $data['newsletter'] ? NewsletterPro::getInstance()->l('Yes') : NewsletterPro::getInstance()->l('No')),
                    NewsletterPro::getInstance()->l('Firstname') => (string) $data['firstname'],
                    NewsletterPro::getInstance()->l('Lastname') => (string) $data['lastname'],
                    NewsletterPro::getInstance()->l('Email') => (string) $data['email'],
                    NewsletterPro::getInstance()->l('IP address') => (string) $data['ip_registration_newsletter'],
                    NewsletterPro::getInstance()->l('Date add') => (string) $data['date_add'],
                ]);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function emailsubscriptionClearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'emailsubscription', $email);

        if (!NewsletterProTools::tableExists('emailsubscription')) {
            return $response;
        }

        try {
            if (Db::getInstance()->delete('emailsubscription', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function newsletterClearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'newsletter', $email);

        if (!NewsletterProTools::tableExists('newsletter')) {
            return $response;
        }

        try {
            if (Db::getInstance()->delete('newsletter', '`email` = "'.pSQL($email).'"')) {
                $response->addToCount(Db::getInstance()->Affected_Rows());
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }

    private function customerClearPrivacy($email)
    {
        $response = new NewsletterProPrivacyDataResponse(NewsletterProPrivacyDataResponse::TYPE_CLEAR, 'customer', $email);

        try {
            if (Db::getInstance()->update('customer', [
                'newsletter' => 0,
            ], '`email` = "'.pSQL($email).'"')) {
                $res = $this->customerPrivacySerach($email);
                $data = $res->toArray();
                $response->addToCount((int) $data['count']);
            }
        } catch (Exception $e) {
            $response->addException($e);
        }

        return $response;
    }
}
