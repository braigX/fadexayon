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

class AdminFaqopBlockGeneralController extends AdminFaqopBasicBlockController
{
    public function initContent()
    {
        $languages = Language::getLanguages(false);
        if (count($languages) > 1) {
            $this->content .= $this->module->mes->getMultiLanguageInfoMsg();
        }
        parent::initContent();
    }

    public function postProcessBlock()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            if ($this->postValidationBlock() && $this->module->helper->checkIfSubmitClicked()) {
                $this->postProcessSubmitBlock();
            }
        }

        return $this->output;
    }

    protected function postValidationBlock($errors = [])
    {
        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks title */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->l('The block title is too long.');
                }
            }
        }

        return parent::postValidationBlock($errors);
    }

    protected function postProcessSubmitBlock()
    {
        /* Here we update block */
        return parent::postProcessSubmitBlock();
    }

    public function setBlockFields($block)
    {
        $block->id_shop = (int) $this->context->shop->id;
        $block->show_title = (int) Tools::getValue('show_title');
        $block->show_markup = (int) Tools::getValue('show_markup');

        /* Sets each langue fields */
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $block->title[$language['id_lang']] = Tools::getValue('title_' . $language['id_lang']);
        }
    }

    public function getFieldsForm()
    {
        $fields_form = [];
        $fields_form['form'] = $this->getFieldsFormFirst();

        $fields_form[1] = [
            'form' => $this->module->getSaveCancelButtons($this->back),
        ];

        return $fields_form;
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('General settings'),
            'icon' => 'icon-edit',
        ];

        $fields['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Show markup'),
            'name' => 'show_markup',
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'show_markup_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'show_markup_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ],
        ];

        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Frontend Title'),
            'name' => 'title',
            'lang' => true,
            'class' => 'title-text-field',
        ];
        $fields['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Show Frontend Title'),
            'name' => 'show_title',
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'show_title_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'show_title_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ],
        ];

        return $fields;
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields['show_title'] = Tools::getValue('show_title', $block->show_title);
        $fields['show_markup'] = Tools::getValue('show_markup', $block->show_markup);

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            if (isset($block->title[$lang['id_lang']])) {
                $fields['title'][$lang['id_lang']] = Tools::getValue(
                    'title_' . (int) $lang['id_lang'],
                    $block->title[$lang['id_lang']]
                );
            } else {
                $fields['title'][$lang['id_lang']] = '';
            }
        }

        return $fields;
    }
}
