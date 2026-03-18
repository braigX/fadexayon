<?php
/**
 * Builds a lightweight request key that can be computed before Prestashop
 * boots. This intentionally relies only on raw server variables so it is safe
 * to use from the shop root index.php.
 *
 * V1 is deliberately conservative and is mainly intended for the homepage or
 * other very simple anonymous URLs.
 */

class PrestaLoadEarlyCacheKeyBuilder
{
    /**
     * Returns both the key and the parts used to compute it so debugging stays
     * understandable when early cache is enabled.
     */
    public static function buildContextFromServer()
    {
        $parts = [
            'scheme' => self::isSecureRequest() ? 'https' : 'http',
            'host' => isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '',
            'uri' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/',
        ];

        return [
            'parts' => $parts,
            'key' => 'early_' . sha1(json_encode($parts)),
            'request_factors' => self::buildRequestFactors(),
        ];
    }

    private static function buildRequestFactors()
    {
        $cookieHeader = isset($_SERVER['HTTP_COOKIE']) ? (string) $_SERVER['HTTP_COOKIE'] : '';
        $cookies = self::parseCookies($cookieHeader);
        $query = [];
        if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
            parse_str((string) $_SERVER['QUERY_STRING'], $query);
        }

        return [
            'path' => self::getRequestPath(),
            'query_keys' => array_values(array_keys($query)),
            'device' => self::detectDeviceFromUserAgent(),
            'language_cookie' => self::findFirstCookieValue($cookies, ['id_lang', 'isolang', 'lang']),
            'currency_cookie' => self::findFirstCookieValue($cookies, ['id_currency', 'currency']),
            'country_cookie' => self::findFirstCookieValue($cookies, ['id_country', 'country']),
            'shop_cookie' => self::findFirstCookieValue($cookies, ['id_shop', 'shop']),
            'has_prestashop_cookie' => self::hasCookiePrefix($cookies, 'PrestaShop-'),
            'has_php_session' => array_key_exists('PHPSESSID', $cookies),
            'has_cart_cookie' => self::hasCookieLike($cookies, ['id_cart', 'cart']),
            'has_customer_cookie' => self::hasCookieLike($cookies, ['customer', 'logged']),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '',
            'forwarded_proto' => isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? (string) $_SERVER['HTTP_X_FORWARDED_PROTO'] : '',
        ];
    }

    private static function getRequestPath()
    {
        $path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';

        if ($path === false || $path === '') {
            return '/';
        }

        return $path;
    }

    private static function parseCookies($cookieHeader)
    {
        $cookies = [];
        foreach (explode(';', (string) $cookieHeader) as $part) {
            $part = trim($part);
            if ($part === '' || strpos($part, '=') === false) {
                continue;
            }

            list($name, $value) = array_map('trim', explode('=', $part, 2));
            if ($name === '') {
                continue;
            }

            $cookies[$name] = $value;
        }

        return $cookies;
    }

    private static function hasCookiePrefix(array $cookies, $prefix)
    {
        foreach (array_keys($cookies) as $name) {
            if (strpos($name, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function hasCookieLike(array $cookies, array $needles)
    {
        foreach (array_keys($cookies) as $name) {
            foreach ($needles as $needle) {
                if (stripos($name, $needle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function findFirstCookieValue(array $cookies, array $names)
    {
        foreach ($names as $name) {
            if (array_key_exists($name, $cookies)) {
                return (string) $cookies[$name];
            }
        }

        return '';
    }

    private static function detectDeviceFromUserAgent()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower((string) $_SERVER['HTTP_USER_AGENT']) : '';
        if ($userAgent === '') {
            return 'unknown';
        }

        if (preg_match('/mobile|iphone|ipod|android.+mobile|windows phone/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/ipad|tablet|android/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    private static function isSecureRequest()
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }

        return false;
    }
}
