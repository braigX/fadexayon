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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicItemController.php';

class AdminFaqopStylesItemController extends AdminFaqopBasicItemController
{
    public $classNameDescription;

    public function __construct()
    {
        parent::__construct();

        $this->classNameDescription = $this->l('Separate several class names with a space. 
        A class name can contain only small letters (a-z) 
        and digits (0-9), as well as the hyphen (-) and the underscore (_), and can start only with a small letter');
    }

    public function initContent()
    {
        $this->content .= $this->module->displayNav($this->module->helper->getItemsParent());
        $this->content .= $this->module->mes->getInfoMultishopAboutItem();
        parent::initContent();
    }

    protected function postValidationItem($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            $classes = [
                'i',
                'q',
                'a',
            ];

            foreach ($classes as $class) {
                $errors = $this->validateClass($class, $errors);
            }
        }

        return parent::postValidationItem($errors);
    }

    protected function validateClass($class_name, $errors)
    {
        if (Tools::strlen(Tools::getValue($class_name . '_class')) > 0) {
            if (Tools::strlen(Tools::getValue($class_name . '_class')) > 255) {
                $errors[] = $this->l('The ' . $class_name . ' class is too long.');
            }
            if (!$this->module->helper->isClassName(Tools::getValue($class_name . '_class'))) {
                $errors[] = $this->l('Invalid characters in ' . $class_name . ' class name');
            }
        }

        return $errors;
    }

    public function setItemFields($item)
    {
        $item->i_class = $this->module->helper->explodeImplode(Tools::getValue('i_class'));
        $item->q_class = $this->module->helper->explodeImplode(Tools::getValue('q_class'));
        $item->a_class = $this->module->helper->explodeImplode(Tools::getValue('a_class'));
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('CSS classes'),
            'icon' => 'icon-cogs',
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Individual CSS class for the whole item'),
            'name' => 'i_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Individual CSS class for question'),
            'name' => 'q_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Individual CSS class for answer'),
            'name' => 'a_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        return $fields;
    }

    public function getAddItemFieldsValues($item, $fields = [])
    {
        $fields['i_class'] = Tools::getValue('i_class', $item->i_class);
        $fields['q_class'] = Tools::getValue('q_class', $item->q_class);
        $fields['a_class'] = Tools::getValue('a_class', $item->a_class);

        return $fields;
    }

    public function renderItemNav($active_url = 'styles')
    {
        return parent::renderItemNav($active_url);
    }
}
