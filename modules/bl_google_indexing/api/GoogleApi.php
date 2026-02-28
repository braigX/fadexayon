<?php
/**
 * 2010-2023 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2023 Bl Modules
 * @license
 */

class GoogleApi
{
    const ACTION_UPDATE = 'URL_UPDATED';

    protected $httpClient;

    public function init()
    {
        $productUrl = !empty($_POST['product_url']) ? $_POST['product_url'] : ''; //Sorry, we cant use here Tools::getValue
        $jsonApiKey = !empty($_POST['json_api_key']) ? $_POST['json_api_key'] : ''; //Sorry, we cant use here Tools::getValue

        $this->load($jsonApiKey);
        $this->response($this->send($productUrl));
    }

    protected function send($productUrl)
    {
        $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

        $content = '{
                "url": "'.$productUrl.'",
                "type": "'.self::ACTION_UPDATE.'"
            }';

        $response = $this->httpClient->post($endpoint, ['body' => $content,]);
        $body = json_decode($response->getBody()->getContents(), true);

        return ['phrase' => $response->getReasonPhrase(), 'message' => $body,];
    }

    public function get($url, $settings)
    {
        $this->load($settings);

        $response = $this->httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url='.urlencode($url));

        return ['phrase' => $response->getReasonPhrase(), 'message' => $response->getBody()->getContents(),];
    }

    protected function load($jsonApiKey)
    {
        include_once(dirname(__FILE__).'/../vendor/google_api/autoload.php');

        $client = new Google_Client();

        $client->setAuthConfig(json_decode($jsonApiKey, true));
        $client->addScope('https://www.googleapis.com/auth/indexing');

        $this->httpClient = $client->authorize();
    }

    protected function response($array)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($array);
        die;
    }
}

$googleApi = new GoogleApi();
$googleApi->init();
