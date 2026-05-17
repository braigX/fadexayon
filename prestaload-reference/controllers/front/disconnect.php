<?php

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Front controller: POST /module/prestaload/disconnect
 *
 * Called by the Prestaload dashboard when the user disconnects an integration.
 * Validates the api_key_hash against what is stored locally, then clears the settings.
 */
class PrestaloadDisconnectModuleFrontController extends ModuleFrontController
{
    public function postProcess(): void
    {
        header('Content-Type: application/json');

        $body   = json_decode(file_get_contents('php://input'), true) ?: [];
        $apiKeyHash = $body['api_key_hash'] ?? '';

        $settings = json_decode(Configuration::get(Prestaload::CONFIG_KEY, '{}'), true) ?: [];
        $storedHash = empty($settings['api_key']) ? '' : hash('sha256', $settings['api_key']);

        if ($storedHash === '' || ! hash_equals($storedHash, $apiKeyHash)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }

        Configuration::deleteByName(Prestaload::CONFIG_KEY);

        echo json_encode(['disconnected' => true]);
        exit;
    }
}
