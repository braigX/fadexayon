<?php
require_once(dirname(__FILE__) . '/../../classes/CrazyLibrary.php');
require_once _PS_MODULE_DIR_ . 'crazyelements/PrestaHelper.php';

use CrazyElements\PrestaHelper;

class AdminCrazyTemplatesController extends AdminController
{
    public $folder_name = '';

    public function __construct()
    {
        $this->table = 'crazy_library';  // give the table name which we have create,
        $this->className = 'CrazyLibrary'; // class name of our object model
        $this->lang = false;
        $this->deleted = false;
        $this->bootstrap = true;
        $this->module = 'crazyelements';
        parent::__construct();
        // now we will create the table to show
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->fields_list = array(
            'id_crazy_library' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 440,
                'type' => 'text',
                'lang' => false,
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'type' => array(
                'title' => $this->l('Type'),
                'width' => 440,
                'type' => 'text',
                'lang' => false,
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'source' => array(
                'title' => $this->l('Source'),
                'width' => 440,
                'type' => 'text',
                'lang' => false,
                'orderby' => false,
                'filter' => false,
                'search' => false
            ),
            'export_link' => array(
                'title' => $this->l('Export Url'),
                'width' => 200,
                'type' => 'link',
                'lang' => false,
                'icon' => 'process-icon-export',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'export_url'
            )
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        parent::__construct();
        $this->actions = array( 'edit','delete');
    }

    public static function export_url($url){
        return '<a href="'.$url.'"><i class="icon-download"></i> Export Now</a>';
    }

    public function renderForm()
    {

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Edit Template'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => false,
                    'required' => true,
                    'desc' => $this->l('Enter Your Title')
                ),
                array(
					'type'     => 'textarea',
					'callback' => 'gohere',
					'label'    => $this->l( 'Content' ),
					'name'     => 'template_content',
					'lang'     => false,
				),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ),
        );

        return parent::renderForm();
    }


    public function renderList()
    {
        $ce_licence = PrestaHelper::get_option('ce_licence', 'false');
        if ($ce_licence == "false") {
            $active_link = PrestaHelper::get_setting_page_url();
            return '<style>.need_to_active {font-size: 20px;color: #495157 !important;font-weight: bold;}.need_to_active_a {font-size: 12px;font-weight: bold;}</style><div class="panel col-lg-12"> <div class="panel-heading"> Need To Active Licence.<span class="badge"></span></div><div class="col-md-12"><div class="need_to_active">Need To Active Licence.</div><a class="need_to_active_a" href="' . $active_link . '">Click For Active.</a></div></div>';
        }
        $htmlfinal = parent::renderList();
        return $htmlfinal;
    }

    public function initContent()
    {
        PrestaHelper::get_lience_expired_date();
        $a = "<div></div>" . parent::initContent();
        return $a;
    }

    public function postProcess()
    {
        // die(__FILE__ . ' : ' . __LINE__);
        parent::postProcess(true);
    }
}
