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
        ];
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
