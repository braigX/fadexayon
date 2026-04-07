<?php
/**
 * Builds and validates internal signed requests used by PrestaLoad tooling.
 */

class PrestaLoadInternalAuth
{
    public const HEADER_BETA_GENERATE = 'HTTP_X_PRESTALOAD_BETA_GENERATE';
    public const HEADER_BETA_TOKEN = 'HTTP_X_PRESTALOAD_BETA_TOKEN';

    public static function buildBetaGenerateToken()
    {
        if (!defined('_COOKIE_KEY_')) {
            return '';
        }

        return hash_hmac('sha256', 'prestaload-beta-cache-generate', _COOKIE_KEY_ . '|' . (defined('_DB_NAME_') ? _DB_NAME_ : ''));
    }

    public static function isBetaGenerateRequest()
    {
        return !empty($_SERVER[self::HEADER_BETA_GENERATE]);
    }

    public static function isAuthorizedBetaGenerateRequest(array $runtime = [])
    {
        if (!self::isBetaGenerateRequest()) {
            return false;
        }

        $provided = isset($_SERVER[self::HEADER_BETA_TOKEN]) ? (string) $_SERVER[self::HEADER_BETA_TOKEN] : '';
        if ($provided === '') {
            return false;
        }

        $expected = '';
        if (!empty($runtime['beta_generate_token'])) {
            $expected = (string) $runtime['beta_generate_token'];
        } else {
            $expected = self::buildBetaGenerateToken();
        }

        if ($expected === '') {
            return false;
        }

        return hash_equals($expected, $provided);
    }
}
