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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/EtsRVMailLog.php';

class EtsRVTools extends EtsRVCore
{
    static $_INSTANCE;

    public static function getInstance()
    {
        if (!self::$_INSTANCE)
            self::$_INSTANCE = new EtsRVTools();

        return self::$_INSTANCE;
    }

    public static function isArrayWithIds(&$ids)
    {
        if (!is_array($ids) || count($ids) < 1) {
            return false;
        }

        $loop = 0;
        foreach ($ids as $id) {
            if (trim($id) === '' || $id == 0 || !Validate::isUnsignedInt($id)) {
                unset($ids[$loop]);
            }
            $loop++;
        }

        return true;
    }

    public static function geneColor($str)
    {
        $hash = md5('color' . $str);
        $rgb = array(
            hexdec(Tools::substr($hash, 0, 2)), // r
            hexdec(Tools::substr($hash, 2, 2)), // g
            hexdec(Tools::substr($hash, 4, 2))); //b
        return 'rgba(' . implode(',', $rgb) . ', 1)';
    }

    public static function getFormattedName($type)
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ImageType::getFormattedName($type) : ImageType::getFormatedName($type);
    }

    public static function formatFileName($file_name)
    {
        return preg_match('/[\_\(\)\s\%\+]+/', '-', $file_name);
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getServerVars($var)
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : '';
    }

    public static function getPostMaxSizeBytes()
    {
        $postMaxSizeList = array(@ini_get('post_max_size'), @ini_get('upload_max_filesize'), (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') . 'M');
        $ik = 0;
        foreach ($postMaxSizeList as &$max_size) {
            $bytes = (int)trim($max_size);
            $last = Tools::strtolower($max_size[Tools::strlen($max_size) - 1]);
            switch ($last) {
                case 'g':
                    $bytes *= 1024;
                case 'm':
                    $bytes *= 1024;
                case 'k':
                    $bytes *= 1024;
            }
            if ($bytes == '') {
                unset($postMaxSizeList[$ik]);
            } else
                $max_size = $bytes;
            $ik++;
        }

        return min($postMaxSizeList);
    }

    public static function quickSort($list, $field = 'position', $ignore_value = -1)
    {
        $left = $right = array();
        if (count($list) <= 1) {
            return $list;
        }
        $pivot_key = key($list);
        $pivot = array_shift($list);
        // partial:
        foreach ($list as $key => $val) {
            if ($val[$field] <= $pivot[$field]) {
                $left[$key] = $val;
            } elseif ($val[$field] > $pivot[$field]) {
                $right[$key] = $val;
            }
        }
        // recursive:
        return array_merge(self::quickSort($left, $field, $ignore_value), array($pivot_key => $pivot), self::quickSort($right, $field, $ignore_value));
    }

    // DB:
    public static function tableExist($table)
    {
        return Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . bqSQL($table) . '\'');
    }

    public function runCronjob($token)
    {
        Configuration::updateGlobalValue('ETS_RV_LAST_CRONJOB', date('Y-m-d H:i:s'), true);

        if (!$token || !Validate::isCleanHtml($token) || $token != Configuration::getGlobalValue('ETS_RV_SECURE_TOKEN')) {
            if (Tools::isSubmit('ajax')) {
                die(json_encode(array(
                    'errors' => $this->l('Access denied'),
                    'result' => ''
                )));
            }
            die($this->l('Access denied'));
        }

        $nbCartRule = 0;
        if (Configuration::getGlobalValue('ETS_RV_AUTO_CLEAR_DISCOUNT')) {
            $nbCartRule = EtsRVCartRule::clearDiscountIsExpired();
        }

        $module = Module::getInstanceByName('ets_reviews');
        $count = 0;
        $fail = 0;
        $max_try = ($max = (int)Configuration::getGlobalValue('ETS_RV_CRONJOB_MAX_TRY')) && $max > 0 && Validate::isUnsignedInt($max) ? $max : 5;
        $max_emails = ($limit = (int)Configuration::getGlobalValue('ETS_RV_CRONJOB_EMAILS')) && $limit > 0 && Validate::isUnsignedInt($limit) ? $limit : 5;
        $context = Context::getContext();

        if ($queues = Db::getInstance()->executeS('
            SELECT * 
            FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` 
            WHERE to_email is NOT NULL 
                AND (schedule_time is NULL OR schedule_time <= 0 OR schedule_time <= ' . time() . ' )
                AND ((sent = 0 AND send_count < ' . (int)$max_try . ') OR sending_time is NULL OR TIMESTAMPDIFF(SECOND, sending_time, \'' . pSQL(date('Y-m-d H:i:s')) . '\') > 60)
            LIMIT ' . (int)$max_emails
        )) {
            foreach ($queues as $queue) {
                if (EtsRVUnsubscribe::isUnsubscribe($queue['to_email'])) {
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE to_email=\'' . pSQL($queue['to_email']) . '\'');
                    continue;
                }
                if (Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_email_queue` SET `sent` = 1, `sending_time` = \'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `id_ets_rv_email_queue` = ' . (int)$queue['id_ets_rv_email_queue'])) {
                    $templateVars = json_decode($queue['template_vars'], true);
                    if (isset($queue['content']) && trim($queue['content']) !== '')
                        $templateVars['{content}'] = $queue['content'];
                    EtsRVTools::getInstance()->replaceShortCode($templateVars, (int)$queue['id_lang']);
                    EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_TIMEOUT);
                    if (($delivered = Mail::send(
                            (int)$queue['id_lang'],
                            trim($queue['template']),
                            $queue['subject'],
                            $templateVars,
                            trim($queue['to_email']),
                            trim($queue['to_name']), null, null, null, null,
                            $module->getLocalPath() . 'mails/',
                            false,
                            (int)$queue['id_shop']
                        )) || (int)Db::getInstance()->getValue('SELECT `send_count` FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE `id_ets_rv_email_queue` = ' . (int)$queue['id_ets_rv_email_queue']) > $max_try
                    ) {
                        EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_DELIVERED);
                        if ($delivered) {
                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_tracking` SET `delivered` = 1, date_upd=\'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `queue_id` = ' . (int)$queue['id_ets_rv_email_queue']);
                            $count++;
                        }
                        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE `id_ets_rv_email_queue` = ' . (int)$queue['id_ets_rv_email_queue']);
                    } else {
                        $fail++;
                        EtsRVMailLog::writeLog($queue, EtsRVMailLog::SEND_MAIL_FAILED);
                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_email_queue` SET `sent` = 0, `send_count` = `send_count` + 1, `sending_time` = \'' . pSQL(date('Y-m-d H:i:s')) . '\' WHERE `id_ets_rv_email_queue` = ' . (int)$queue['id_ets_rv_email_queue']);
                    }
                }
            }
        }

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_queue` WHERE send_count >= ' . (int)$max_try . ' OR to_email is NULL');

        if ((int)Configuration::getGlobalValue('ETS_RV_SAVE_CRONJOB_LOG')) {
            $return = '[' . date($context->language->date_format_full) . ']';
            if ($count > 0)
                $return .= '  ' . sprintf($this->l('There were %d email(s) sent successfully'), $count);
            elseif ($fail <= 0)
                $return .= '  ' . $this->l('No email has been sent');
            if ($fail > 0)
                $return .= ($count > 0 ? ' | ' : '  ') . sprintf($this->l('Sent failed %d email(s)'), $fail);
            if ($nbCartRule) {
                $return .= ' | ' . sprintf($this->l('%s discount deleted'), $nbCartRule);
            } else {
                $return .= ' | ' . $this->l('No discount deleted');
            }
            $dest = _PS_ROOT_DIR_ . '/var/logs/';
            if (!@is_dir($dest))
                @mkdir($dest, 0755, true);

            @file_put_contents($dest . $module->name . '.cronjob.log', $return . PHP_EOL, FILE_APPEND);
        }

        $jsonArr = array(
            'result' => $this->l('Cronjob ran successfully') . ' ' . ($count <= 0 ? ($nbCartRule <= 0 && $fail <= 0 ? '. ' . $this->l('Nothing to do!') : '') : sprintf($this->l('%s email(s) was sent!'), $count)) . ($fail > 0 ? ($count > 0 ? ' | ' : '. ') . sprintf($this->l('Sent failed %d email(s)'), $fail) : '') . ($nbCartRule > 0 ? ' | ' . sprintf($this->l('%s discount deleted'), $nbCartRule) : ''),
        );
        if (isset($return)) {
            $jsonArr['log'] = $return;
        }
        die(json_encode($jsonArr));
    }

    public static function getCustomers($ids = array())
    {
        if (!$ids ||
            !Validate::isArrayWithIds($ids)
        ) {
            return false;
        }
        $dq = new DbQuery();
        $dq
            ->select('*')
            ->from('customer', 'c')
            ->where('id_customer IN (' . implode(',', $ids) . ')');

        return Db::getInstance()->executeS($dq);
    }

    public static function hasProductComments()
    {
        if (Db::getInstance()->executeS('SHOW TABLES LIKE \'' . _DB_PREFIX_ . 'product_comment\'')) {
            $dq = new DbQuery();
            $dq
                ->select('COUNT(*)')
                ->from('product_comment', 'pc');

            return (int)Db::getInstance()->getValue($dq);
        }

        return false;
    }

    /**
     * @param $type
     * @return bool
     * Write a review
     */
    public static function reviewGrand($type)
    {
        if (trim($type) == '')
            return false;

        $options = explode(',', trim(Configuration::get('ETS_RV_WHO_POST_REVIEW')));

        return is_array($options) && in_array($type, $options) ? 1 : 0;
    }

    public static function isCustomerPurchased()
    {
        $options = explode(',', trim(Configuration::get('ETS_RV_WHO_POST_REVIEW')));

        return count($options) == 1 && trim($options[0]) == 'purchased' || Configuration::get('ETS_RV_FREE_DOWNLOADS_ENABLED') && count($options) == 2 && in_array('purchased', $options) && in_array('no_purchased_incl', $options);

    }

    /**
     * @param $type
     * @return bool
     * Rating a review
     */
    public static function ratingGrand($type)
    {
        if (trim($type) == '')
            return false;

        $who_can_rate = explode(',', trim(Configuration::get('ETS_RV_WHO_POST_RATING')));

        return is_array($who_can_rate) && in_array($type, $who_can_rate) ? 1 : 0;
    }

    static $cache_subject = [];

    public function getSubjects($template = null)
    {
        if (!self::$cache_subject) {
            self::$cache_subject = [
                'person_dislike' => [
                    'og' => '{from_person_name} disliked your {object}',
                    't' => $this->l('{from_person_name} disliked your {object}', 'EtsRVTools'),
                    'desc' => $this->l('Notification email when a user clicks "dislike" for your object (reviews, comments, questions, etc.)', 'EtsRVTools'),
                ],
                'person_like' => [
                    'og' => '{from_person_name} liked your {object}',
                    't' => $this->l('{from_person_name} liked your {object}', 'EtsRVTools'),
                    'desc' => $this->l('Notification email when a user clicks "like" for your object (reviews, comments, questions, etc.)', 'EtsRVTools'),
                ],
                'person_commented' => [
                    'og' => '{from_person_name} commented on your {object}',
                    't' => $this->l('{from_person_name} commented on your {object}'),
                    'desc' => $this->l('Notification email when a user commented on your object (reviews, comments, questions, etc.)', 'EtsRVTools'),
                ],
                'person_replied' => [
                    'og' => '{from_person_name} replied to your {object}',
                    't' => $this->l('{from_person_name} replied to your {object}'),
                    'desc' => $this->l('Notification email when a user replied to your object (reviews, comments, questions, etc.)', 'EtsRVTools'),
                ],
                'person_answer' => [
                    'og' => '{from_person_name} answered to your question',
                    't' => $this->l('{from_person_name} answered to your question', 'EtsRVTools'),
                    'desc' => $this->l('Notification email when a user answered to your question', 'EtsRVTools'),
                ],
                // Customer|Admin
                'tocustomer_awaiting' => [
                    'og' => 'Your {object} has been submitted and is waiting for approval',
                    't' => $this->l('Your {object} has been submitted and is waiting for approval', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers when their object (reviews, comments, questions, etc.) has been submitted and is waiting for approval', 'EtsRVTools'),
                ],
                'toadmin_awaiting' => [
                    'og' => '{customer_name} submitted {object}. Please review it.',//[a/an]
                    't' => $this->l('{customer_name} submitted {object}. Please review it.', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to admin when an object (reviews, comments, questions, etc.) of customers has been submitted and is waiting for approval', 'EtsRVTools'),
                ],
                'tocustomer_approved' => [
                    'og' => 'Your {object} has been approved and published.',
                    't' => $this->l('Your {object} has been approved and published.', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers when their object (reviews, comments, questions, etc.) has been approved and published', 'EtsRVTools'),
                ],
                'tocustomer_get_voucher' => [
                    'og' => 'You got a voucher code from {shop_name}',
                    't' => $this->l('You got a voucher code from {shop_name}', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers when they receive a voucher code after leaving a review', 'EtsRVTools'),
                ],
                'tocustomer_rating_invitation' => [
                    'og' => 'Are you satisfied with “{product_name}”?',
                    't' => $this->l('Are you satisfied with “{product_name}”?', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers to invite them leaving a review', 'EtsRVTools'),
                ],
                'tocustomer_rating_invitation_getvoucher' => [
                    'og' => 'Are you satisfied with “{product_name}”? Rate now to get a voucher code',
                    't' => $this->l('Are you satisfied with “{product_name}”? Rate now to get a voucher code', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers to invite them leaving a review and receive a voucher', 'EtsRVTools'),
                ],
                'tocustomer_refuse' => [
                    'og' => 'Your {object} has been declined',
                    't' => $this->l('Your {object} has been declined', 'EtsRVTools'),
                    'desc' => $this->l('Notification email send to customers when their product review has been declined', 'EtsRVTools'),
                ],
            ];
        }

        return $template != null && isset(self::$cache_subject[$template]) ? self::$cache_subject[$template] : ($template == null ? self::$cache_subject : []);
    }

    public function initEmailTemplate($template = null)
    {
        $subjects = $this->getSubjects($template);
        if ($template !== null)
            $subjects = [$template => $subjects];
        $partialQueries = [];
        foreach (array_keys($subjects) as $tmp) {
            $partialQueries[] = '(\'' . pSQL($tmp) . '\')';
        }
        $res = true;
        if ($template == null) {
            $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template`');
            $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template_shop`');
            $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ets_rv_email_template_lang`');
        }
        $emailTemplateId = 0;
        if ($partialQueries) {
            $res &= Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_email_template` (`template`) VALUES' . implode(',', $partialQueries));
            if ($template != null) {
                $emailTemplateId = Db::getInstance()->Insert_ID();
            }
        }
        if ($res) {
            $idShop = Configuration::get('PS_SHOP_DEFAULT');
            $res &= Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_email_template_shop`(`id_ets_rv_email_template`, `id_shop`)
                SELECT et.id_ets_rv_email_template, IFNULL(shop.id_shop, ' . (int)$idShop . ')
                FROM `' . _DB_PREFIX_ . 'ets_rv_email_template` et CROSS JOIN `' . _DB_PREFIX_ . 'shop` shop
                WHERE 1' . ($emailTemplateId > 0 ? ' AND et.id_ets_rv_email_template=' . (int)$emailTemplateId : '') . '
            ');
            $res &= Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_email_template_lang`(`id_ets_rv_email_template`, `id_lang`, `id_shop`, `subject`)
                SELECT et.id_ets_rv_email_template, IFNULL(lang.id_lang, 0), IFNULL(shop.id_shop, ' . (int)$idShop . '), \'\' `subject`
                FROM `' . _DB_PREFIX_ . 'ets_rv_email_template` et CROSS JOIN `' . _DB_PREFIX_ . 'lang` lang CROSS JOIN `' . _DB_PREFIX_ . 'shop` shop
                WHERE 1' . ($emailTemplateId > 0 ? ' AND et.id_ets_rv_email_template=' . (int)$emailTemplateId : '') . '
            ');
            $templates = Db::getInstance()->executeS('
                SELECT etl.*, et.template, l.iso_code FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` etl
                INNER JOIN  `' . _DB_PREFIX_ . 'ets_rv_email_template` et ON (et.id_ets_rv_email_template = etl.id_ets_rv_email_template)
                INNER JOIN `' . _DB_PREFIX_ . 'lang` l ON (l.id_lang = etl.id_lang)
                WHERE 1' . ($emailTemplateId > 0 ? ' AND et.id_ets_rv_email_template=' . (int)$emailTemplateId : '') . '
            ');
            if (count($templates) > 0) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` SET ';
                $queries = [];
                foreach ($templates as $template) {
                    if (isset($template['template']) && trim($template['template']) !== '' && isset($subjects[trim($template['template'])]) && ($subject = $subjects[trim($template['template'])])) {
                        $text = self::trans($subject['og'], trim($template['iso_code']), 'EtsRVTools') ?: $subject['og'];
                        $queries[] = $sql . '`subject`=\'' . pSQL($text) . '\' WHERE `id_ets_rv_email_template`=' . (int)$template['id_ets_rv_email_template'] . ' AND `id_lang`=' . (int)$template['id_lang'] . ' AND `id_shop`=' . (int)$template['id_shop'];
                    }
                }
                if ($queries) {
                    foreach ($queries as $query) {
                        $res &= Db::getInstance()->execute($query);
                    }
                }
            }
        }

        return $res;
    }

    public function upgradeActivities()
    {
        $activities = [
            // Review
            'wrote_a_review_for_product' => '#wr(o|i)te a review (for|to) product(.*)#', // #write a review to product#
            'like_a_review' => '#like(d|.*) (a|to) review(.*)#', //#like to review(.*)#
            'dislike_a_review' => '#dislike(d|.*) (a|to) review(.*)#', //#dislike to review#

            // Comment of review
            'commented_on_a_review' => '#comment(ed|.*) (on a|to) review(.*)#', //#comment to review#
            'like_a_comment' => '#like(d|.*) (a|to) comment(.*)#', //#like to comment#
            'dislike_a_comment' => '#dislike(d|.*) (a|to) comment(.*)#', //#dislike to comment#

            // Reply
            'replied_to_a_comment' => '#replied to a comment(.*)#', //#replied to a comment#
            'like_a_reply' => '#like(d|.*) (a|to) reply(.*)#',
            'dislike_a_reply' => '#dislike(d|.*) (a|to) reply(.*)#',

            // Question
            'asked_a_question_about_product' => '#ask(ed|.*) a question (about|to|on) product(.*)#', //#ask a question to product#
            'like_a_question' => '#like(d|.*) (to|a) question(.*)#', //#like to question#
            'dislike_a_question' => '#dislike(d|.*) (to|a) question(.*)#', //#dislike to question#

            // Comment of question
            'commented_on_a_question' => '#comment(ed|.*) (on a|to) question(.*)#', //#comment to question#
            'like_comment_of_question' => '#like(d|.*) (a|to) comment( on a question|.*)(.*)#',
            'dislike_comment_of_question' => '#dislike(d|.*) (a|to) comment( on a question|.*)(.*)#',

            // Answer
            'answered_to_a_question' => '#answer(ed|.*) to( a|.*) question(.*)#', //#answer to question#
            'dislike_an_answer' => '#dislik(ed|.*) (to|an) answer(.*)#', //#dislike to answer#
            'like_an_answer' => '#like(d|.*) (an|to) answer(.*)#', //#like to answer#

            // Comment of answer
            'commented_on_an_answer' => '#comment(ed|.*) (on|to) (a|an) answer(.*)#', //#commented to a answer#
            'like_a_comment_answer' => '#like(d|.*) (a|to) comment(.*)#',
            'dislike_a_comment_answer' => '#dislike(d|.*) (a|to) comment(.*)#',
        ];

        $sql = [];
        foreach ($activities as $key => $activity) {
            if ($activity) {
                $query = 'UPDATE `' . _DB_PREFIX_ . 'ets_rv_activity` SET `content`=\'' . pSQL($key) . '\' WHERE 1';
                if (is_array($activity) && count($activity) > 1) {
                    $orWhere = [];
                    foreach ($activity as $regex) {
                        $orWhere[] = '`content` REGEXP \'' . $regex . '\'';
                    }
                    $query .= ' AND (' . implode(' OR ', $orWhere) . ')';
                } else
                    $query .= ' AND `content` REGEXP \'' . (is_array($activity) ? $activity[0] : $activity) . '\'';
                $sql[] = $query;
            }
        }
        if ($sql) {
            foreach ($sql as $qr) {
                Db::getInstance()->execute($qr);
            }
        }

        return true;
    }

    public function replaceShortCode(&$vars, $idLang = null)
    {
        if ($idLang == null)
            $idLang = Context::getContext()->language->id;
        $vars['{tracking}'] = isset($vars['{tracking}']) && trim($vars['{logo_img}']) !== '' ? $this->display('shortcode.tpl', ['shortcode' => 'tracking', 'tracking' => $vars['{tracking}']]) : '';
        $vars['{logo_img}'] = isset($vars['{logo_img}']) && trim($vars['{logo_img}']) !== '' ? $this->display('shortcode.tpl', ['shortcode' => 'logo_img', 'shop_logo' => $vars['{logo_img}']]) : '';

        if (isset($vars['{product_list}'])) {
            $ETS_RV_MAIL_RATE_NOW_TEXT = Configuration::get('ETS_RV_MAIL_RATE_NOW_TEXT', $idLang) ?: $this->l('Rate now', 'EtsRVTools');
            $vars['{product_list}'] = $this->display('mail-product-list.tpl', ['products' => $vars['{product_list}'], 'ETS_RV_MAIL_RATE_NOW_TEXT' => $ETS_RV_MAIL_RATE_NOW_TEXT]);
        }

        if (!empty($vars['{product}']))
            $vars['{product}'] = $this->display('product-info.tpl', $vars['{product}']);

        if (isset($vars['{product_name}']) && !empty($vars['{product_link}'])) {
            $vars['{product_name}'] = EtsRVTools::displayText(EtsRVTools::displayText($vars['{product_name}'], 'span'), 'a', ['href' => $vars['{product_link}']]);
        }

        if (!empty($vars['{unsubscribe}'])) {
            $ETS_RV_MAIL_UNSUBSCRIBE_TEXT = Configuration::get('ETS_RV_MAIL_UNSUBSCRIBE_TEXT', $idLang) ?: $this->l('Unsubscribe', 'EtsRVTools');
            $vars['{unsubscribe}'] = EtsRVTools::displayText($ETS_RV_MAIL_UNSUBSCRIBE_TEXT, 'a', ['href' => $vars['{unsubscribe}']]);
        }
        if (isset($vars['{content}']) && $vars['{content}'])
            $vars['{content}'] = Tools::nl2br($vars['{content}']);
    }

    public function checkUploadError($error_code, $file_name)
    {
        switch ($error_code) {
            case 1:
                return sprintf($this->l('File "%1s" uploaded exceeds %2s', 'EtsRVTools'), $file_name, ini_get('upload_max_filesize'));
            case 2:
                return sprintf($this->l('The uploaded file exceeds %s', 'EtsRVTools'), ini_get('post_max_size'));
            case 3:
                return sprintf($this->l('Uploaded file "%s" was only partially uploaded', 'EtsRVTools'), $file_name);
            case 6:
                return $this->l('Missing temporary folder', 'EtsRVTools');
            case 7:
                return sprintf($this->l('Failed to write file "%s" to disk', 'EtsRVTools'), $file_name);
            case 8:
                return sprintf($this->l('A PHP extension stopped the file "%s" to upload', 'EtsRVTools'), $file_name);
        }
        return false;
    }

    public function processUploadImage($field, $folder, &$errors, $required = false, $label = null, $destinationWidth = null, $destinationHeight = null, &$error_post_maxsize = false)
    {
        if (count($errors) > 0)
            return [];

        $file_dest = _PS_IMG_DIR_ . 'ets_reviews/' . ($folder !== '' ? $folder : 'a') . '/';
        if (!is_dir($file_dest))
            $errors[] = sprintf($this->l('The directory "%s" does not exist.', 'EtsRVTools'), $file_dest);

        $post_content_size = EtsRVTools::getServerVars('CONTENT_LENGTH');
        if (($post_max_size = EtsRVTools::getPostMaxSizeBytes()) && ($post_content_size > $post_max_size)) {
            $errors[] = sprintf($this->l('The uploaded file(s) exceeds the post_max_size directive in php.ini (%s > %s)', 'EtsRVTools'), EtsRVTools::formatBytes($post_content_size), EtsRVTools::formatBytes($post_max_size));
            $error_post_maxsize = true;
        } elseif (!@is_writable($file_dest) && !empty($_FILES[$field]['name'])) {
            $errors[] = sprintf($this->l('The directory "%s" is not writable.', 'EtsRVTools'), $file_dest);
        } elseif (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
            if ($uploadError = $this->checkUploadError($_FILES[$field]['error'], $_FILES[$field]['name'])) {
                $errors[] = $uploadError;
            } elseif ($_FILES[$field]['size'] > $post_max_size) {
                $errors[] = sprintf($this->l('File is too large. Maximum size allowed: %sMb', 'EtsRVTools'), EtsRVTools::formatBytes($post_max_size));
            } elseif ($_FILES[$field]['size'] > Ets_reviews::DEFAULT_MAX_SIZE) {
                $errors[] = sprintf($this->l('File is too big. Current size is %1s, maximum size is %2s.', 'EtsRVTools'), $_FILES[$field]['size'], Ets_reviews::DEFAULT_MAX_SIZE);
            } elseif (isset($_FILES[$field]['name'])) {
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$field]['name'], '.'), 1));
                if (!in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                    $errors[] = sprintf($this->l('File "%s" type is not allowed', 'EtsRVTools'), $_FILES[$field]['name']);
                }
            }
        }
        if ($required && (isset($_FILES[$field]['name']) || empty($_FILES[$field]['name']))) {
            $errors[] = $label . ' ' . $this->l('is required', 'EtsRVTools');
        }
        if (!$errors && !empty($_FILES[$field]['name'])) {
            $salt = Tools::strtolower(Tools::passwdGen(20));
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES[$field]['name'], '.'), 1));
            $image = $salt . '.' . $type;
            $file_name = $file_dest . $image;

            if (@file_exists($file_name)) {
                $errors[] = $this->l('File name already exists. Try to rename the file and upload again', 'EtsRVTools');
            } else {
                $image_size = @getimagesize($_FILES[$field]['tmp_name']);
                if (isset($_FILES[$field]) && !empty($_FILES[$field]['tmp_name']) && !empty($image_size) && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                    if (!($temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !@move_uploaded_file($_FILES[$field]['tmp_name'], $temp_name)) {
                        $errors[] = $this->l('An error occurred while uploading the image.', 'EtsRVTools');
                    } elseif (!@ImageManager::resize($temp_name, $file_name, $destinationWidth, $destinationHeight, $type))
                        $errors[] = sprintf($this->l('An error occurred while copying this image: %s', 'EtsRVTools'), Tools::stripslashes($image));
                }
                if (isset($temp_name) && file_exists($temp_name))
                    @unlink($temp_name);
            }
            if (!$errors) {
                return [
                    $image,
                    $file_dest,
                    $file_name
                ];
            }
        }

        return [];
    }

    public static function ajaxSearchCustomer($q)
    {
        $query = $q && Validate::isCleanHtml($q) ? $q : false;
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }
        $searches = explode(' ', $query);
        $searches = array_unique($searches);
        foreach ($searches as $search) {
            if (!empty($search) && $results = Customer::searchByName($search, 50)) {
                foreach ($results as $result) {
                    $customer = [];
                    if ($result['active']) {
                        $customer = [
                            $result['id_customer'],
                            $result['firstname'],
                            $result['lastname'],
                            $result['email'],
                        ];
                    }
                    echo implode('|', $customer) . "\r\n";
                }
            }
        }
        die;
    }

    public static function getVariables($template)
    {
        if (empty($template))
            return [];
        switch ($template) {
            case 'person_like':
            case 'person_dislike':
                return [
                    '{shop_name}',
                    '{person_name}',
                    '{from_person_name}',
                    '{object}',
                    '{product_name}',
                    '{content}',
                    '{unsubscribe}',
                ];
            case 'person_commented':
            case 'person_replied':
                return [
                    '{shop_name}',
                    '{person_name}',
                    '{from_person_name}',
                    '{object}',
                    '{product_name}',
                    '{object_content}',
                    '{content}',
                    '{unsubscribe}',
                ];
            case 'person_answer':
                return [
                    '{shop_name}',
                    '{person_name}',
                    '{from_person_name}',
                    '{object}',
                    '{product_name}',
                    '{question_content}',
                    '{content}',
                    '{unsubscribe}',
                ];
            case 'toadmin_awaiting':
                return [
                    '{shop_name}',
                    '{admin_name}',
                    '{customer_name}',
                    '{object}',
                    '{content}',
                    '{product}',
                    '{unsubscribe}',
                ];
            case 'tocustomer_awaiting':
            case 'tocustomer_approved':
                return [
                    '{shop_name}',
                    '{customer_name}',
                    '{object}',
                    '{product_name}',
                    '{content}',
                    '{unsubscribe}',
                ];
            case 'tocustomer_get_voucher':
                return [
                    '{shop_name}',
                    '{customer_name}',
                    '{voucher_code}',
                    '{voucher_value}',
                    '{available_date}',
                    '{unsubscribe}',
                ];
            case 'tocustomer_rating_invitation':
                return [
                    '{shop_name}',
                    '{customer_name}',
                    '{product_list}',
                    '{unsubscribe}',
                ];
            case 'tocustomer_rating_invitation_getvoucher':
                return [
                    '{shop_name}',
                    '{customer_name}',
                    '{product_list}',
                    '{voucher_value}',
                    '{unsubscribe}',
                ];
            default:
                return [];
        }
    }

    public static function getShortCodesInSubject($template)
    {
        if (!$template || !Validate::isCleanHtml($template)) {
            return [];
        }
        switch ($template) {
            case 'person_like':
            case 'person_dislike':
            case 'person_commented':
            case 'person_replied':
                return [
                    '{from_person_name}',
                    '{object}',
                ];
            case 'person_answer':
                return [
                    '{from_person_name}',
                ];
            case 'toadmin_awaiting':
                return [
                    '{customer_name}',
                    '{object}',
                ];
            case 'tocustomer_awaiting':
            case 'tocustomer_approved':
                return [
                    '{object}',
                ];
            case 'tocustomer_get_voucher':
                return [
                    '{shop_name}',
                ];
            case 'tocustomer_rating_invitation':
            case 'tocustomer_rating_invitation_getvoucher':
                return [
                    '{product_name}',
                ];
            default:
                return [];
        }
    }

    public static function encrypt($key)
    {
        $key = md5($key);
        return Tools::substr($key, 5, 5)
            . Tools::substr($key, 3, 3)
            . Tools::substr($key, 4, 4)
            . Tools::substr($key, 20, 3)
            . Tools::substr($key, 15, 2)
            . Tools::substr($key, 23, 3)
            . Tools::substr($key, 29, 2);
    }

    public static function isEmailListSeparatedByComma($emails)
    {
        $emails = explode(',', $emails);
        if ($emails) {
            foreach ($emails as $email) {
                if (!Validate::isEmail(trim($email)))
                    return false;
            }
        }

        return true;
    }

    public static function displayText($content, $tag, $attr_datas = array())
    {
        $text = '<' . $tag;
        if ($attr_datas) {
            foreach ($attr_datas as $key => $value) {
                if ($value === null)
                    $text .= ' ' . $key;
                else
                    $text .= ' ' . $key . '="' . $value . '"';
            }
        }
        if ($tag == 'img' || $tag == 'br' || $tag == 'path' || $tag == 'input')
            $text .= ' />';
        else
            $text .= ' style="text-decoration:none;">';
        if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
            $text .= $content;
        if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
            $text .= '<' . '/' . $tag . '>';
        return $text;
    }

    public static function checkEnableOtherShop($id_module)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module`=' . (int)$id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
        return Db::getInstance()->executeS($sql);
    }

    public static function activeTab($module_name)
    {
        if (property_exists('Tab', 'enabled'))
            return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab` SET `enabled`=1 WHERE `module`=\'' . pSQL($module_name) . '\'');
    }

    public static function executeSQL($sql_file)
    {
        if (!file_exists(dirname(__FILE__) . '/../sql/' . $sql_file)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/../sql/' . $sql_file)) {
            return false;
        }
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
        if ($sql) {
            foreach ($sql as $query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function htmlOpenTag($tag, $attr_datas = array())
    {
        $text = '<' . $tag . ' ';
        if ($attr_datas) {
            foreach ($attr_datas as $key => $value)
                $text .= $key . '="' . $value . '" ';
        }
        return $text . '>';
    }

    public static function htmlCloseTag($tag)
    {
        return '<' . '/' . $tag . '>';
    }

    static $_icons = [];

    public static function pushIcon($name, $svg)
    {
        if (!$name || !$svg) {
            return;
        }
        if (empty(self::$_icons) && @file_exists(($filename = dirname(__FILE__) . '/../icon.json'))) {
            $icons = json_decode(Tools::file_get_contents(dirname(__FILE__) . '/../icon.json'), true);
            self::$_icons = $icons;
        }
        if (empty(self::$_icons[$name])) {
            self::$_icons[$name] = $svg;
        }
        @file_put_contents(dirname(__FILE__) . '/../icon.json', json_encode(self::$_icons));
    }

    public static function getIcon($name = null)
    {
        if (empty(self::$_icons) && @file_exists(($filename = dirname(__FILE__) . '/../icon.json'))) {
            $icons = json_decode(Tools::file_get_contents(dirname(__FILE__) . '/../icon.json'), true);
            self::$_icons = $icons;
        } else
            $icons = self::$_icons;
        if ($name !== null) {
            return !empty($icons[$name]) ? $icons[$name] : null;
        }
        return $icons;
    }

    public static function getMetaIdByPageName($page_name)
    {
        if (!$page_name || !Validate::isCatalogName($page_name)) {
            return false;
        }
        $query = '
            SELECT `id_meta` 
            FROM `' . _DB_PREFIX_ . 'meta`
            WHERE `page`=\'' . pSQL($page_name) . '\'
        ';
        return Db::getInstance()->getValue($query);
    }
}