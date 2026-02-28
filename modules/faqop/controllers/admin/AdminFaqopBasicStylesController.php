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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBasicBlockController.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';

class AdminFaqopBasicStylesController extends AdminFaqopBasicBlockController
{
    public $classNameDescription;

    public $tagsListBlock;

    public $tagsListTitle;

    public $tagsListContent;

    public $tagsListItem;

    public $tagsListQuestion;

    public $tagsListAnswer;

    public $accordionSettings;

    public function __construct()
    {
        parent::__construct();

        $this->classNameDescription = $this->l('Separate several class names with a space. 
        A class name can contain only small letters (a-z) 
        and digits (0-9), as well as the hyphen (-) and the underscore (_), and can start only with a small letter');

        $this->tagsListBlock = [
            ['id' => 'article', 'name' => 'article'],
            ['id' => 'aside', 'name' => 'aside'],
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'footer', 'name' => 'footer'],
            ['id' => 'header', 'name' => 'header'],
            ['id' => 'section', 'name' => 'section'],
        ];

        $this->tagsListTitle = [
            ['id' => 'h1', 'name' => 'h1'],
            ['id' => 'h2', 'name' => 'h2'],
            ['id' => 'h3', 'name' => 'h3'],
            ['id' => 'h4', 'name' => 'h4'],
            ['id' => 'h5', 'name' => 'h5'],
            ['id' => 'h6', 'name' => 'h6'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'div', 'name' => 'div'],
        ];

        $this->tagsListContent = [
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'dl', 'name' => 'dl'],
            ['id' => 'ol', 'name' => 'ol'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'section', 'name' => 'section'],
            ['id' => 'ul', 'name' => 'ul'],
        ];

        $this->tagsListItem = [
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'dl', 'name' => 'dl'],
            ['id' => 'li', 'name' => 'li'],
            ['id' => 'ol', 'name' => 'ol'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'section', 'name' => 'section'],
            ['id' => 'ul', 'name' => 'ul'],
        ];

        $this->tagsListQuestion = [
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'dt', 'name' => 'dt'],
            ['id' => 'h1', 'name' => 'h1'],
            ['id' => 'h2', 'name' => 'h2'],
            ['id' => 'h3', 'name' => 'h3'],
            ['id' => 'h4', 'name' => 'h4'],
            ['id' => 'h5', 'name' => 'h5'],
            ['id' => 'h6', 'name' => 'h6'],
            ['id' => 'li', 'name' => 'li'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'section', 'name' => 'section'],
        ];

        $this->tagsListAnswer = [
            ['id' => 'div', 'name' => 'div'],
            ['id' => 'dd', 'name' => 'dd'],
            ['id' => 'li', 'name' => 'li'],
            ['id' => 'p', 'name' => 'p'],
            ['id' => 'section', 'name' => 'section'],
        ];

        $this->accordionSettings = [
            ['id' => 0, 'name' => $this->l('None')],
            ['id' => 1, 'name' => $this->l('Simple')],
            ['id' => 2, 'name' => $this->l('Collapsable')],
        ];
    }

    public function getFieldsForm()
    {
        $fields_form = [];
        $fields_form['form'] = $this->getFieldsFormFirst();
        $fields_form[0] = $this->getFormTagClasses();
        $fields_form[1] = [
            'form' => $this->module->getSaveCancelButtons($this->back),
        ];

        return $fields_form;
    }

    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            $classes = [
                'block',
                'title',
                'content',
                'item',
                'question',
                'answer',
            ];

            foreach ($classes as $class) {
                $errors = $this->validateClass($class, $errors);
            }
        }

        return parent::postValidationBlock($errors);
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

    public function setBlockFields($block)
    {
        $block->block_tag = Tools::getValue('block_tag');
        $block->block_class = $this->module->helper->explodeImplode(Tools::getValue('block_class'));
        $block->title_tag = Tools::getValue('title_tag');
        $block->title_class = $this->module->helper->explodeImplode(Tools::getValue('title_class'));
        $block->content_tag = Tools::getValue('content_tag');
        $block->content_class = $this->module->helper->explodeImplode(Tools::getValue('content_class'));
        $block->item_tag = Tools::getValue('item_tag');
        $block->item_class = $this->module->helper->explodeImplode(Tools::getValue('item_class'));
        $block->question_tag = Tools::getValue('question_tag');
        $block->question_class = $this->module->helper->explodeImplode(Tools::getValue('question_class'));
        $block->answer_tag = Tools::getValue('answer_tag');
        $block->answer_class = $this->module->helper->explodeImplode(Tools::getValue('answer_class'));
        $block->accordion = Tools::getValue('accordion');
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('Display settings'),
            'icon' => 'icon-cogs',
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'accordion',
            'label' => $this->l('Accordion'),
            'options' => [
                'query' => $this->accordionSettings,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        return $fields;
    }

    public function getFormTagClasses($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('Tags and classes'),
            'icon' => 'icon-cogs',
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'block_tag',
            'label' => $this->l('Tag for the whole block'),
            'options' => [
                'query' => $this->tagsListBlock,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for the whole block'),
            'name' => 'block_class',
            'class' => 'fixed-width-3xl',
            'lang' => false,
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'title_tag',
            'label' => $this->l('Tag for title'),
            'options' => [
                'query' => $this->tagsListTitle,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for title'),
            'name' => 'title_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'content_tag',
            'label' => $this->l('Tag for all FAQ items wrapper'),
            'options' => [
                'query' => $this->tagsListContent,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for all FAQ items wrapper'),
            'name' => 'content_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'item_tag',
            'label' => $this->l('Tag for one FAQ item (Question+Answer) wrapper'),
            'options' => [
                'query' => $this->tagsListItem,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for one FAQ item (Question+Answer) wrapper'),
            'name' => 'item_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'question_tag',
            'label' => $this->l('Tag for question'),
            'options' => [
                'query' => $this->tagsListQuestion,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for question'),
            'name' => 'question_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        $fields['form']['input'][] = [
            'type' => 'select',
            'name' => 'answer_tag',
            'label' => $this->l('Tag for answer'),
            'options' => [
                'query' => $this->tagsListAnswer,
                'id' => 'id',
                'name' => 'name',
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('CSS class for answer'),
            'name' => 'answer_class',
            'lang' => false,
            'class' => 'fixed-width-3xl',
            'desc' => $this->classNameDescription,
        ];

        return $fields;
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $blockTagDefault = 'div';
        if ($block->block_tag) {
            $blockTagDefault = $block->block_tag;
        }
        if (Tools::isSubmit('block_tag')) {
            $blockTagDefault = Tools::getValue('block_tag');
        }

        $titleTagDefault = 'h2';
        if ($block->title_tag) {
            $titleTagDefault = $block->title_tag;
        }
        if (Tools::isSubmit('title_tag')) {
            $titleTagDefault = Tools::getValue('title_tag');
        }

        $contentTagDefault = 'div';
        if ($block->content_tag) {
            $contentTagDefault = $block->content_tag;
        }
        if (Tools::isSubmit('content_tag')) {
            $contentTagDefault = Tools::getValue('content_tag');
        }

        $itemTagDefault = 'div';
        if ($block->item_tag) {
            $itemTagDefault = $block->item_tag;
        }
        if (Tools::isSubmit('item_tag')) {
            $itemTagDefault = Tools::getValue('item_tag');
        }

        $questionTagDefault = 'div';
        if ($block->question_tag) {
            $questionTagDefault = $block->question_tag;
        }
        if (Tools::isSubmit('question_tag')) {
            $questionTagDefault = Tools::getValue('question_tag');
        }

        $answerTagDefault = 'div';
        if ($block->answer_tag) {
            $answerTagDefault = $block->answer_tag;
        }
        if (Tools::isSubmit('answer_tag')) {
            $answerTagDefault = Tools::getValue('answer_tag');
        }

        $fields['block_tag'] = $blockTagDefault;
        $fields['title_tag'] = $titleTagDefault;
        $fields['content_tag'] = $contentTagDefault;
        $fields['item_tag'] = $itemTagDefault;
        $fields['question_tag'] = $questionTagDefault;
        $fields['answer_tag'] = $answerTagDefault;
        $fields['block_class'] = Tools::getValue('block_class', $block->block_class);
        $fields['title_class'] = Tools::getValue('title_class', $block->title_class);
        $fields['content_class'] = Tools::getValue('content_class', $block->content_class);
        $fields['item_class'] = Tools::getValue('item_class', $block->item_class);
        $fields['question_class'] = Tools::getValue('question_class', $block->question_class);
        $fields['answer_class'] = Tools::getValue('answer_class', $block->answer_class);

        $fields['accordion'] = Tools::getValue('accordion', $block->accordion);

        return $fields;
    }
}
