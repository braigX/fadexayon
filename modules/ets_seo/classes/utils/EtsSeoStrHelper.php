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
 **/
if (!defined('_PS_VERSION_')) {
    exit;
}
if (!class_exists('EtsSeoStrHelper')) {
    /**
     * Class EtsSeoStrHelper
     */
    class EtsSeoStrHelper
    {
        /**
         * @param string $haystack
         * @param string $needle
         *
         * @return bool
         */
        public static function contains(string $haystack, string $needle)
        {
            return '' === $needle || false !== strpos($haystack, $needle);
        }

        /**
         * @param string $haystack
         * @param string $needle
         *
         * @return bool
         */
        public static function startsWith(string $haystack, string $needle)
        {
            return 0 === strncmp($haystack, $needle, \strlen($needle));
        }

        /**
         * @param string $haystack
         * @param string $needle
         *
         * @return bool
         */
        public static function endsWith(string $haystack, string $needle)
        {
            if ('' === $needle || $needle === $haystack) {
                return true;
            }

            if ('' === $haystack) {
                return false;
            }

            $needleLength = \strlen($needle);

            return $needleLength <= \strlen($haystack) && 0 === substr_compare($haystack, $needle, -$needleLength);
        }

        /**
         * Return the remainder of a string after the first occurrence of a given value.
         *
         * @param string $subject
         * @param string $search
         *
         * @return string
         */
        public static function after($subject, $search)
        {
            return '' === $search ? $subject : array_reverse(explode($search, $subject, 2))[0];
        }

        /**
         * Return the remainder of a string after the last occurrence of a given value.
         *
         * @param string $subject
         * @param string $search
         *
         * @return string
         */
        public static function afterLast($subject, $search)
        {
            if ('' === $search) {
                return $subject;
            }

            $position = strrpos($subject, (string) $search);

            if (false === $position) {
                return $subject;
            }

            return substr($subject, $position + strlen($search));
        }

        /**
         * Get the portion of a string before the first occurrence of a given value.
         *
         * @param string $subject
         * @param string $search
         *
         * @return string
         */
        public static function before($subject, $search)
        {
            if ('' === $search) {
                return $subject;
            }

            $result = strstr($subject, (string) $search, true);

            return false === $result ? $subject : $result;
        }

        /**
         * Get the portion of a string before the last occurrence of a given value.
         *
         * @param string $subject
         * @param string $search
         *
         * @return string
         */
        public static function beforeLast($subject, $search)
        {
            if ('' === $search) {
                return $subject;
            }

            $pos = mb_strrpos($subject, $search);

            if (false === $pos) {
                return $subject;
            }

            return static::substr($subject, 0, $pos);
        }

        /**
         * Returns the portion of the string specified by the start and length parameters.
         *
         * @param string $string
         * @param int $start
         * @param int|null $length
         *
         * @return string
         */
        public static function substr($string, $start, $length = null)
        {
            return mb_substr($string, $start, $length, 'UTF-8');
        }

        /**
         * Get the portion of a string between two given values.
         *
         * @param string $subject
         * @param string $from
         * @param string $to
         *
         * @return string
         */
        public static function between($subject, $from, $to)
        {
            if ('' === $from || '' === $to) {
                return $subject;
            }

            return static::beforeLast(static::after($subject, $from), $to);
        }
        public static function displayText($content, $tag, $attr_datas = [])
        {
            $text = '<' . $tag . ' ';
            if ($attr_datas) {
                foreach ($attr_datas as $key => $value) {
                    $text .= $key . '="' . $value . '" ';
                }
            }
            if ('img' == $tag || 'br' == $tag || 'path' == $tag || 'input' == $tag) {
                $text .= ' />';
            } else {
                $text .= '>';
            }
            if ('img' != $tag && 'input' != $tag && 'br' != $tag && !is_null($content)) {
                $text .= $content;
            }
            if ('img' != $tag && 'path' != $tag && 'input' != $tag && 'br' != $tag) {
                $text .= '</' . $tag . '>';
            }
            return $text;
        }
    }
}
