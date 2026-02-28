<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';
class PostHelper extends BasicHelper
{
    protected $action;

    protected $errors;

    protected $stayController;

    protected $back;

    protected $conf;

    public function __construct($action, $errors, $stayController, $back, $module)
    {
        parent::__construct($module);
        $this->errors = $errors;
        $this->stayController = $stayController;
        $this->back = $back;

        if ($action) {
            $this->conf = 3;
        } else {
            $this->conf = 4;
        }
    }

    public function post()
    {
        $output = '';

        /* Display errors if needed */
        if (count($this->errors)) {
            $output .= $this->module->displayError(implode('<br />', $this->errors));
        } else {
            if (Tools::isSubmit('submitStay')) {
                // if updated and stay
                Tools::redirectAdmin($this->stayController . '&conf=' . $this->conf);
            } elseif (Tools::isSubmit('submitBlock')) {
                // if updated and leave
                Tools::redirectAdmin($this->back . '&conf=' . $this->conf);
            }
        }

        return $output;
    }
}
