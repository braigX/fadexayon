<?php
/**
 *
 * @author AN Eshop Group
 * @copyright  AN Eshop Group
 * @license    Private
 * @version  Release: $Revision$
 */

require _PS_MODULE_DIR_ . '/googlemybusinessreviews/vendor/autoload.php';
require_once _PS_MODULE_DIR_ . '/googlemybusinessreviews/googlemybusinessreviews.php';

/**
 * Class GooglemybusinessreviewsAjaxModuleFrontController
 */
class GooglemybusinessreviewsAjaxModuleFrontController extends ModuleFrontController
{

    const URL_API = "https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&language=%s&fields=" .
    "name,url,reviews,rating,formatted_phone_number";

    /** @var string */
    public $name = "googlemybusinessreviewsAjax";

    public $template = 'module:googlemybusinessreviews/views/templates/front/ajax.tpl';

    /** @var int */
    protected $nbReviewAdd;

    public function initContent()
    {

        $locales = Language::getLanguages(true);
        foreach ($locales as $locale) {
            $httpClient = $this->getHttpClient();
            $response = $httpClient->get($this->getUrl($locale['iso_code']));
            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody()->getContents());
                if (isset($result->result)) {
                    $this->saveReviews($result);
                    $this->saveGlobalInformations($result);
                }
            }
        }

        $this->json = true;
        $this->status = 'success';
        $this->displayAjaxSucess();
        die;
    }

    public function displayAjaxSucess()
    {
        if ($this->json) {
            $this->context->smarty->assign(array(
                'json' => true,
                'status' => $this->status,
                'nb_reviews_add' => empty($this->nbReviewAdd) ? 0 : $this->nbReviewAdd,
            ));
        }
        $this->layout = '';
        $this->display_header = false;
        $this->display_header_javascript = false;
        $this->display_footer = false;

        return $this->display();
    }

    /**
     * @return bool|string
     */
    protected function getApiKey()
    {
        return Configuration::get(\Googlemybusinessreviews::CONFIG_API_KEY);
    }

    /**
     * @param string $locale
     * @return string
     */
    protected function getUrl($locale = 'en')
    {
        return sprintf(
            self::URL_API,
            Configuration::get(Googlemybusinessreviews::CONFIG_PLACE_ID),
            $locale
        );
    }


    protected function saveInDatabase($reviews)
    {
        $reviewMin = (int)Configuration::get(Googlemybusinessreviews::CONFIG_MINIMUM_SCORE);
        foreach ($reviews as $review) {
            if (!$this->existeInDatabase($review) && ($reviewMin <= $review->rating)) {
                $this->insertLine($review);
                $this->nbReviewAdd++;
            }
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function insertLine($data)
    {
        $review = new \Review();
        $review->author = $data->author_name;
        $review->author_url = $data->author_url;
        $review->language = isset($data->language) ? $data->language : "";
        $review->profile_photo = $data->profile_photo_url;
        $review->rating = $data->rating;
        $review->time_description = $data->relative_time_description;
        $review->text = $data->text;
        $review->time = (int)$data->time;
        $review->place_id = Configuration::get(Googlemybusinessreviews::CONFIG_PLACE_ID);

        return $review->add();
    }


    protected function existeInDatabase($review)
    {
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from(Review::$definition['table']);
        $sql->where('time = ' . $review->time);
        $sql->where(sprintf("language ='%s'", $review->language));

        return (bool)Db::getInstance()->getValue($sql);
    }

    /**
     * @param $result
     * @return int|null
     */
    protected function saveReviews($result)
    {
        if (!isset($result->result->reviews)) {
            return null;
        }
        $reviews = $result->result->reviews;
        $this->saveInDatabase($reviews);

        return count($reviews);
    }

    /**
     * @param $result
     * @throws \Exception
     */
    protected function saveGlobalInformations($result)
    {
        if (isset($result->result->rating)) {
            Configuration::updateValue(
                \Googlemybusinessreviews::CONFIG_REVIEW_RATING,
                (float)$result->result->rating
            );
        }
        if (isset($result->result->name)) {
            Configuration::updateValue(\Googlemybusinessreviews::CONFIG_PLACE_NAME, $result->result->name);
        }
        if (isset($result->result->url)) {
            Configuration::updateValue(\Googlemybusinessreviews::CONFIG_PLACE_URL, $result->result->url);
        }
        $date = new DateTime();
        Configuration::updateValue(\Googlemybusinessreviews::CONFIG_DATE_MAJ, $date->format('d F Y'));
    }

    /**
     * @return \GuzzleHttp\Client|\GuzzleHttp\ClientInterface
     */
    protected function getHttpClient()
    {
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey($this->getApiKey());

        return $client->authorize();
    }
}
