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

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AdminGooglemybusinessreviewsController
 */
class AdminGooglemybusinessreviewsController extends ModuleAdminController
{

    const URL_API = "https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&language=%s&fields=" .
    "name,url,reviews,rating,formatted_phone_number";

    /** @var string */
    public $name = "AdminGooglemybusinessreviewsController";

    /** @var int */
    protected $nbReviewAdd;

    /**
     * AdminGooglemybusinessreviewsController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = Review::$definition['table'];
        $this->identifier = Review::$definition['primary'];
        $this->className = Review::class;
        $this->lang = true;
        $this->context = Context::getContext();

        parent::__construct();
    }

    public function initContent()
    {
        $locales = Language::getLanguages(true);
        foreach ($locales as $locale) {
            $this->importReviews($locale['iso_code']);
        }

        if (empty($this->errors)) {
            $this->informations[] = $this->trans(
                'Information successfully updated.',
                array(),
                'Modules.Googlemybusinessreviews.Admin'
            );
        }
        if (Tools::getValue('ajax')) {
            $this->json = true;

            $this->ajaxRender(json_encode(['success' => true]));
            die;
        }


        if (!empty($this->errors)) {
            $link = '&configure=googlemybusinessreviews&error=true';
            $this->addLogErrors($this->errors);
        } else {
            $link = '&configure=googlemybusinessreviews&success=true';
        }

        if ($this->nbReviewAdd > 0) {
            PrestaShopLogger::addLog(
                sprintf('GoogleMyBusinessReviews : %d reviews add', $this->nbReviewAdd),
                1
            );
            $link .= '&nb_reviews=' . $this->nbReviewAdd;
        }
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminModules') . $link);
    }

    /**
     * @param $locale
     * @throws PrestaShopException
     */
    protected function importReviews($locale)
    {
        $httpClient = $this->getHttpClient();
        $response = $httpClient->get($this->getUrl($locale));

        if ($response->getStatusCode() != 200) {
            return;
        }

        $result = json_decode($response->getBody()->getContents());
        if (isset($result->result)) {
            $this->saveReviews($result);
            $this->saveGlobalInformations($result);
        }

        if (isset($result->error_message)) {
            $this->errors[] = $result->error_message;
            if (Tools::getValue('ajax')) {
                $this->json = true;

                $this->ajaxRender(json_encode(['error' => true]));
                die;
            }
        }
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

    /**
     * @param $reviews
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
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

    /**
     * @param array $errors
     */
    protected function addLogErrors(array $errors)
    {
        foreach ($errors as $error) {
            $message = 'GoogleMyBusinessReviews - Error to synchronise reviews : ' . $error;
            PrestaShopLogger::addLog($message, 2);
        }
    }
}
