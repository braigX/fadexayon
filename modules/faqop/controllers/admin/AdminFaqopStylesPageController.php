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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicStylesController.php';

class AdminFaqopStylesPageController extends AdminFaqopBasicStylesController
{
    public $tagsPageDescription;

    public function initContent()
    {
        $this->content .= $this->module->displayNav('page');
        parent::initContent();
    }

    public function renderBlockNav($active_url = 'styles')
    {
        return $this->module->helper->renderPageNavTabs($active_url, $this->id_list);
    }

    public function __construct()
    {
        parent::__construct();

        $this->tagsPageDescription = [
            ['id' => 'article', 'name' => 'article'],
            ['id' => 'aside', 'name' => 'aside'],
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'footer', 'name' => 'footer'],
            ['id' => 'header', 'name' => 'header'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'section', 'name' => 'section'],
        ];
    }

    public function setBlockFields($block)
    {
        parent::setBlockFields($block);
        $block->description_tag = Tools::getValue('description_tag');
        $block->description_class = $this->module->helper->explodeImplode(Tools::getValue('description_class'));
    }

    public function getFormTagClasses($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields = parent::getFormTagClasses($fields);

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'description_tag',
            'label' => $this->l('Tag for page description'),
            'options' => [
                'query' => $this->tagsPageDescription,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for page description'),
            'name' => 'description_class',
            'class' => 'fixed-width-3xl',
            'lang' => false,
            'desc' => $this->classNameDescription,
        ];

        return $fields;
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields = parent::getAddBlockFieldsValues($block, $fields);
        $descriptionTagDefault = 'div';
        if ($block->description_tag) {
            $descriptionTagDefault = $block->description_tag;
        }
        if (Tools::isSubmit('description_tag')) {
            $descriptionTagDefault = Tools::getValue('description_tag');
        }

        $fields['description_tag'] = $descriptionTagDefault;
        $fields['description_class'] = Tools::getValue('description_class', $block->description_class);

        return $fields;
    }

    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            $errors = $this->validateClass('description', $errors);
        }

        return parent::postValidationBlock($errors);
    }
}
