<?php
/**
 *
 * @author AN Eshop Group
 * @copyright  AN Eshop Group
 * @license    Private
 * @version  Release: $Revision$
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . '/googlemybusinessreviews/classes/Review.php';


/**
 * Class Googlemybusinessreviews
 */
class Googlemybusinessreviews extends Module
{
    const MODULE_NAME_CONFIG = "GOOGLE_MY_BUSINESS";
    const CONFIG_API_KEY = "GOOGLEPLACE_APIKEY";
    const CONFIG_PLACE_ID = "GOOGLEPLACE_PLACE_ID";
    const CONFIG_NB_DISPLAY_REVIEW = "CONFIG_NB_DISPLAY_REVIEW";
    const CONFIG_MINIMUM_SCORE = "GOOGLEPLACE_MINIMUM_SCORE";
    const CONFIG_HOOK_SIMPLE_FORMAT = "GOOGLEPLACE_HOOK_SIMPLE_FORMAT";
    const CONFIG_HOOK_SLIDER_FORMAT = "GOOGLEPLACE_HOOK_SLIDER_FORMAT";
    const CONFIG_REVIEW_RATING = "GOOGLEPLACE_REVIEW_RATING";
    const CONFIG_PLACE_NAME = "GOOGLEPLACE_PLACE_NAME";
    const CONFIG_PLACE_URL = "CONFIG_PLACE_URL";
    const CONFIG_DATE_MAJ = "GOOGLEPLACE_DATE_MAJ";
    const CONFIG_ORDER_BY = "GOOGLEPLACE_ORDER_BY";

    const ORDER_BY_DATE = "ORDER_BY_DATE";
    const ORDER_BY_RANDOM = "ORDER_BY_RANDOM";


    const HOOKS_SIMPLE_FORMAT = ['displayLeftColumn', 'displayReassurance', 'displayRightColumn'];
    const HOOKS_SLIDER_FORMAT = ['displayFooterBefore', 'displayFooter', 'displayHome'];


    protected $templateFileSimple;
    protected $templateFileSlider;

    /**
     * Googlemybusinessreviews constructor.
     */
    public function __construct()
    {
        $this->name = 'googlemybusinessreviews';
        $this->tab = 'market_place';
        $this->version = '1.3.0';
        $this->author = 'AN Eshop Group';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;
        $this->module_key = 'b13b3a496511418254132e4658ef6e81';

        parent::__construct();

        $this->displayName = $this->l('Google My Business Review');
        $this->description = $this->l('Connect your Google My Business page');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall ?');

        if (!Configuration::get(self::MODULE_NAME_CONFIG)) {
            $this->warning = $this->l('No name provided');
        }

        $this->templateFileSimple = 'module:googlemybusinessreviews/views/templates/hook/simple.tpl';
        $this->templateFileSlider = 'module:googlemybusinessreviews/views/templates/hook/slider.tpl';

        $this->bulk_actions = array();
    }

    /**
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->installSql() &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayReassurance') &&
            $this->registerHook('displayRightColumn') &&
            $this->registerHook('displayFooterBefore') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayHome');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallConfig()
            && $this->uninstallSql();
    }


    protected function uninstallConfig()
    {
        return Configuration::deleteByName(self::CONFIG_API_KEY)
            && Configuration::deleteByName(self::CONFIG_PLACE_ID)
            && Configuration::deleteByName(self::CONFIG_NB_DISPLAY_REVIEW)
            && Configuration::deleteByName(self::CONFIG_MINIMUM_SCORE)
            && Configuration::deleteByName(self::CONFIG_HOOK_SIMPLE_FORMAT)
            && Configuration::deleteByName(self::CONFIG_HOOK_SLIDER_FORMAT)
            && Configuration::deleteByName(self::CONFIG_REVIEW_RATING)
            && Configuration::deleteByName(self::CONFIG_PLACE_NAME)
            && Configuration::deleteByName(self::CONFIG_PLACE_URL)
            && Configuration::deleteByName(self::CONFIG_DATE_MAJ)
            && Configuration::deleteByName(self::CONFIG_ORDER_BY);
    }

    /**
     * @return bool
     */
    public function installSql()
    {
        $sqlTableReview = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `author`  varchar(255),
                `author_url` varchar(255),
                `language`  varchar(128),
                `profile_photo`  varchar(255),
                `rating` int(11) unsigned,
                `text` varchar(255),
                `time_description`  varchar(255),
                `time` int(11) unsigned,
                `date_add` datetime DEFAULT NULL,
                `date_upd` datetime DEFAULT NULL,
                `place_id`  varchar(255),
                PRIMARY KEY (`id`)
                );", _DB_PREFIX_ . Review::$definition['table']);

        return Db::getInstance()->execute($sqlTableReview);
    }

    /**
     * Delete database
     *
     * @return bool
     */
    protected function uninstallSql()
    {
        $tableReview = _DB_PREFIX_ . Review::$definition['table'];
        $sql = sprintf("DROP TABLE IF EXISTS %s", $tableReview);

        return Db::getInstance()->execute($sql);
    }


    /**
     * @return string
     */
    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit' . $this->name)) {
            $save = true;
            $apiKey = Tools::getValue(self::CONFIG_API_KEY);
            if (!Validate::isString($apiKey)) {
                {
                    $save = false;
                    $message = $this->l('Configuration not save, api key is not valid string');
                    $output .= $this->displayError($message);
                }
            }
            if (empty($apiKey)) {
                $save = false;
                $message = $this->l('Configuration not save, api key must be completed');
                $output .= $this->displayError($message);
            }

            $placeId = Tools::getValue(self::CONFIG_PLACE_ID);
            if (!Validate::isString($placeId)) {
                $save = false;
                $message = $this->l('Configuration not save, place id is not valid string');
                $output .= $this->displayError($message);
            }
            if (empty($placeId)) {
                $save = false;
                $message = $this->l('Configuration not save, place ID must be completed');
                $output .= $this->displayError($message);
            }

            $nbDisplayReview = Tools::getValue(self::CONFIG_NB_DISPLAY_REVIEW);
            if (!Validate::isInt($nbDisplayReview)) {
                $save = false;
                $message = $this->l('Configuration not save, Number of reviews displayed is not valid number');
                $output .= $this->displayError($message);
            }

            $minScore = Tools::getValue(self::CONFIG_MINIMUM_SCORE);
            if (!Validate::isInt($minScore)) {
                $save = false;
                $message = $this->l('Configuration not save, Minimum score is not valid number');
                $output .= $this->displayError($message);
            }

            $orderBy = Tools::getValue(self::CONFIG_ORDER_BY);
            if (empty($orderBy)) {
                $orderBy = self::ORDER_BY_DATE;
            }

            if ($save) {
                Configuration::updateValue(self::CONFIG_API_KEY, $apiKey);
                Configuration::updateValue(self::CONFIG_PLACE_ID, $placeId);
                Configuration::updateValue(
                    self::CONFIG_NB_DISPLAY_REVIEW,
                    $nbDisplayReview
                );
                Configuration::updateValue(
                    self::CONFIG_MINIMUM_SCORE,
                    $minScore
                );
                $this->checkboxSave(self::CONFIG_HOOK_SIMPLE_FORMAT);
                $this->checkboxSave(self::CONFIG_HOOK_SLIDER_FORMAT);
                Configuration::updateValue(self::CONFIG_ORDER_BY, $orderBy);

                $message = $this->l('Configuration save');
                $output .= $this->displayConfirmation($message);
            }
        }

        $informations = null;
        if (Tools::getValue('success')) {
            $informations = $this->l('Information successfully updated.');
            if (Tools::getValue('nb_reviews')) {
                $informations .= $this->l(sprintf('%d reviews has added.', Tools::getValue('nb_reviews')));
            }
        }

        $errors = null;
        if (Tools::getValue('error')) {
            $errors = $this->l('Error to synchronise reviews - please contact administrator - view your logs');
        }

        $output .= $this->displayKpiBLock();
        if (!empty($informations)) {
            $output .= $this->displayConfirmation($informations);
        }
        if (!empty($errors)) {
            $output .= $this->displayError($errors);
        }

        return $output . $this->displaySelectPlace() . $this->displayDescriptionCron() . $this->displayForm();
    }


    /**
     * @return string
     */
    public function displayForm()
    {
        $fieldsForm = [];
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => 'Configuration',
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Api key'),
                    'name' => self::CONFIG_API_KEY,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Place ID'),
                    'name' => self::CONFIG_PLACE_ID,
                    'required' => true,
                    'desc' =>
                        '<a href="https://developers.google.com/maps/documentation/javascript/examples/'
                        . 'places-placeid-finder" target="_blank">'
                        . $this->l('Find my place ID')
                        . '</a>',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Number of reviews displayed'),
                    'name' => self::CONFIG_NB_DISPLAY_REVIEW,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Minimum score'),
                    'name' => self::CONFIG_MINIMUM_SCORE,
                    'required' => true,
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Display in simple format'),
                    'name' => self::CONFIG_HOOK_SIMPLE_FORMAT,
                    'values' => array(
                        'query' => $this->getHooksSimpleFormat(),
                        'id' => 'id_hook',
                        'name' => 'name',
                    ),
                ],
                [
                    'type' => 'checkbox',
                    'label' => $this->l('Display in slider format'),
                    'name' => self::CONFIG_HOOK_SLIDER_FORMAT,
                    'values' => array(
                        'query' => $this->getHooksSliderFormat(),
                        'id' => 'id_hook',
                        'name' => 'name',
                    ),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Sorting mode'),
                    'name' => self::CONFIG_ORDER_BY,
                    'values' => [
                        [
                            'id' => 'sorting_date',
                            'value' => self::ORDER_BY_DATE,
                            'label' => $this->l('From newest to oldest'),
                        ],
                        [
                            'id' => 'sorting_random',
                            'value' => self::ORDER_BY_RANDOM,
                            'label' => $this->l('Random'),
                        ]
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->submit_action = 'submit' . $this->name;
        $helper->fields_value[self::CONFIG_API_KEY] = Configuration::get(self::CONFIG_API_KEY);
        $helper->fields_value[self::CONFIG_PLACE_ID] = Configuration::get(self::CONFIG_PLACE_ID);
        $helper->fields_value[self::CONFIG_NB_DISPLAY_REVIEW] = Configuration::get(self::CONFIG_NB_DISPLAY_REVIEW);
        $helper->fields_value[self::CONFIG_MINIMUM_SCORE] = Configuration::get(self::CONFIG_MINIMUM_SCORE);
        $helper->fields_value[self::CONFIG_ORDER_BY] = Configuration::get(self::CONFIG_ORDER_BY);

        $this->checkCheckbox($helper, self::CONFIG_HOOK_SIMPLE_FORMAT);
        $this->checkCheckbox($helper, self::CONFIG_HOOK_SLIDER_FORMAT);

        return $helper->generateForm($fieldsForm);
    }

    protected function displayKpiBLock()
    {
        $kpis = array();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-home';
        $helper->color = 'color1';
        $helper->title = $this->l('Place');
        if (!empty(ConfigurationKPI::get(self::CONFIG_PLACE_NAME))) {
            $helper->value = ConfigurationKPI::get(self::CONFIG_PLACE_NAME);
        }
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-star';
        $helper->color = 'color4';
        $helper->title = $this->l('Rating');
        if (!empty(ConfigurationKPI::get(self::CONFIG_DATE_MAJ))) {
            $date = 'updated on ' . ConfigurationKPI::get(self::CONFIG_DATE_MAJ);
            $helper->subtitle = $this->l($date);
        }
        if (!empty(ConfigurationKPI::get(self::CONFIG_REVIEW_RATING))) {
            $helper->value = ConfigurationKPI::get(self::CONFIG_REVIEW_RATING);
        }

        $kpis[] = $helper->generate();

        if (Configuration::get(self::CONFIG_API_KEY)
            && Configuration::get(self::CONFIG_PLACE_ID)) {
            $helper = new HelperKpi();
            $helper->id = 'box-average-order';
            $helper->icon = 'process-icon-refresh';
            $helper->color = 'color2';
            $helper->title = $this->l('Click here to synchronise reviews');
            $helper->subtitle = $this->l('Synchronize');
            $helper->href = $this->context->link->getAdminLink('AdminGooglemybusinessreviews');
            $kpis[] = $helper->generate();
        }

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        $helper->refresh = false;


        return $helper->generate();
    }

    protected function displaySelectPlace()
    {
        if (!Configuration::get(self::CONFIG_API_KEY)) {
            return;
        }

        $helper = new HelperView();
        $helper->title = "Map place ID";
        $helper->tpl_vars = ['api_key' => Configuration::get(self::CONFIG_API_KEY)];
        $helper->base_folder = 'module:googlemybusinessreviews/views/templates/admin/';
        $helper->base_tpl = 'select_placeid.tpl';

        return $helper->generateView();
    }

    protected function displayDescriptionCron()
    {

        if (!Configuration::get(self::CONFIG_API_KEY)) {
            return;
        }
        $vars = array('link' => $this->context->link->getModuleLink('googlemybusinessreviews', 'ajax'));
        $helper = new HelperView();
        $helper->title = "Cron";
        $helper->tpl_vars = $vars;
        $helper->base_folder = 'module:googlemybusinessreviews/views/templates/admin/';
        $helper->base_tpl = 'description_cron.tpl';

        return $helper->generateView();
    }

    /**
     * @return array
     */
    protected function getHooksSimpleFormat()
    {
        $result = array();

        foreach (Hook::getHooks() as $hook) {
            if (in_array($hook['name'], self::HOOKS_SIMPLE_FORMAT)) {
                array_push($result, $hook);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getHooksSliderFormat()
    {
        $result = array();

        foreach (Hook::getHooks() as $hook) {
            if (in_array($hook['name'], self::HOOKS_SLIDER_FORMAT)) {
                array_push($result, $hook);
            }
        }

        return $result;
    }

    /**
     * @param HelperForm $helper
     * @param $configurationName
     */
    protected function checkCheckbox(HelperForm $helper, $configurationName)
    {
        if (Configuration::get($configurationName)) {
            $valuesDatabase = unserialize(Configuration::get($configurationName));
            if (!$valuesDatabase) {
                return;
            }
            foreach ($valuesDatabase as $value) {
                $helper->fields_value[$configurationName . '_' . $value] = "on";
            }
        }
    }

    /**
     * @param $configurationName
     */
    protected function checkboxSave($configurationName)
    {
        $valuesHook = $configurationName == self::CONFIG_HOOK_SIMPLE_FORMAT ?
            $this->getHooksSimpleFormat() :
            $this->getHooksSliderFormat();
        if (!$valuesHook) {
            return;
        }

        $save = [];
        foreach ($valuesHook as $hook) {
            $key = $configurationName . '_' . $hook['id_hook'];
            if (Tools::getValue($key)) {
                array_push($save, $hook['id_hook']);
            }
        }
        Configuration::updateValue($configurationName, serialize($save));
    }


    protected function configHookIsCheck($configurationName, $idHook)
    {
        $result = false;
        $valuesDatabase = unserialize(Configuration::get($configurationName));
        if (empty($valuesDatabase)) {
            return $result;
        }


        foreach ($valuesDatabase as $hookDatabase) {
            if ($hookDatabase == $idHook) {
                $result = true;
            }
        }
        return $result;
    }

    protected function getIdHook($configurationName, $hookName)
    {
        $idHook = null;
        $valuesHook = $configurationName == self::CONFIG_HOOK_SIMPLE_FORMAT ?
            $this->getHooksSimpleFormat() :
            $this->getHooksSliderFormat();
        if (!$valuesHook) {
            return $idHook;
        }

        foreach ($valuesHook as $hook) {
            if ($hook['name'] == $hookName) {
                $idHook = $hook['id_hook'];
            }
        }

        return $idHook;
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/lib/slick/slick.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/lib/slick/slick-theme.css', 'all');

        $this->context->controller->addCSS($this->_path . 'views/css/reviews.css', 'all');
        $this->context->controller->addJquery();
        $this->context->controller->addJS($this->_path . 'views/css/lib/slick/slick.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/slider.js', 'all');
    }

    public function hookDisplayLeftColumn($params)
    {
        $hookName = 'displayLeftColumn';
        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SIMPLE_FORMAT);
    }

    public function hookDisplayReassurance($params)
    {
        $hookName = 'displayReassurance';
        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SIMPLE_FORMAT);
    }

    public function hookDisplayRightColumn($params)
    {
        $hookName = 'displayRightColumn';
        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SIMPLE_FORMAT);
    }

    public function hookDisplayFooterBefore($params)
    {
        $hookName = 'displayFooterBefore';
        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SLIDER_FORMAT);
    }

    public function hookDisplayFooter($params)
    {
        $hookName = 'displayFooter';
        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SLIDER_FORMAT);
    }

    public function hookDisplayHome($params)
    {
        $hookName = 'displayHome';
        $classes = [];
        $classes[] = 'widget_full_width';

        return $this->handleDisplayHook($hookName, self::CONFIG_HOOK_SLIDER_FORMAT, $classes);
    }

    /**
     * @param $hookName
     * @param $configurationName
     * @param null $classes
     * @return bool|mixed|void
     */
    public function handleDisplayHook($hookName, $configurationName, array $classes = [])
    {
        $idHook = $this->getIdHook($configurationName, $hookName);

        if (!isset($idHook)) {
            return;
        }

        if (!$this->configHookIsCheck($configurationName, $idHook)) {
            return;
        }

        if ($configurationName == self::CONFIG_HOOK_SIMPLE_FORMAT) {
            return $this->displaySimpleFormat($classes);
        }

        return $this->displaySliderFormat($hookName, $classes);
    }


    protected function displaySimpleFormat(array $classes = [])
    {
        if (!$this->isCached($this->templateFileSimple, $this->getCacheId('googlemybusinessreviews_simple'))) {
            $variables = $this->getVariablesReviewsSimple();

            if (!empty($classes)) {
                $variables['classes'] = $classes;
            }

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFileSimple, $this->getCacheId('googlemybusinessreviews_simple'));
    }

    protected function displaySliderFormat($hookName = null, $classes = [])
    {
        $cacheName = 'googlemybusinessreviews_slider_' . $hookName;
        if (!$this->isCached($this->templateFileSlider, $this->getCacheId($cacheName))) {
            $variables = $this->getVariablesReviews();

            $variables['classes'] = $classes;

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFileSlider, $this->getCacheId($cacheName));
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    protected function getVariablesReviews()
    {
        $locale = Context::getContext()->language->iso_code;
        $nbReviews = (int)Configuration::get(self::CONFIG_NB_DISPLAY_REVIEW);
        $orderByMode = Configuration::get(self::CONFIG_ORDER_BY) == self::ORDER_BY_DATE ? 'date' : 'random';
        $reviews = Review::getAll($locale, $nbReviews, 0, $orderByMode);

        return
            array(
                'name' => Configuration::get(self::CONFIG_PLACE_NAME),
                'rating' => (float)Configuration::get(self::CONFIG_REVIEW_RATING),
                'place_url' => Configuration::get(self::CONFIG_PLACE_URL),
                'reviews' => $reviews,
                'nb_reviews' => Review::getNbReviews($locale)
            );
    }

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    protected function getVariablesReviewsSimple()
    {
        $locale = Context::getContext()->language->iso_code;

        return
            array(
                'name' => Configuration::get(self::CONFIG_PLACE_NAME),
                'rating' => (float)Configuration::get(self::CONFIG_REVIEW_RATING),
                'place_url' => Configuration::get(self::CONFIG_PLACE_URL),
                'nb_reviews' => Review::getNbReviews($locale)
            );
    }
}
