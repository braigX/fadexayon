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

require_once _PS_MODULE_DIR_ . 'faqop/controllers/admin/AdminFaqopBlockGeneralController.php';

class AdminFaqopPageController extends AdminFaqopBlockGeneralController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav('page');
        parent::initContent();
    }

    public function renderBlockNav($active_url = 'general')
    {
        return $this->module->helper->renderPageNavTabs($active_url, $this->id_list);
    }

    protected function postValidationBlock($errors = [])
    {
        $allow_iframe = (bool) Configuration::get('PS_ALLOW_HTML_IFRAME');

        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks title */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (!Validate::isCleanHtml(Tools::getValue('description_' . $language['id_lang']), $allow_iframe)) {
                    $errors[] = $this->l('Script or iframe tags are not allowed. To enable iframes, change 
                setting Preferences -> General -> Allow iframes on HTML fields');
                }

                if (Tools::strlen(Tools::getValue('description_' . $language['id_lang'])) > 60000) {
                    $errors[] = $this->l(
                        'Page description is too long.'
                    );
                }
            }
        }

        return parent::postValidationBlock($errors);
    }

    public function setBlockFields($block)
    {
        parent::setBlockFields($block);
        $block->show_description = (int) Tools::getValue('show_description');

        /* Sets each langue fields */
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $block->description[$language['id_lang']] = Tools::getValue('description_' . $language['id_lang']);
        }
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields = parent::getFieldsFormFirst($fields);

        $fields['form']['input'][] = [
            'type' => 'textarea',
            'label' => $this->l('Description'),
            'name' => 'description',
            'autoload_rte' => true,
            'lang' => true,
            'class' => 'description-text-field',
        ];
        $fields['form']['input'][] = [
            'type' => 'switch',
            'label' => $this->l('Show description'),
            'name' => 'show_description',
            'is_bool' => true,
            'values' => [
                [
                    'id' => 'show_description_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ],
                [
                    'id' => 'show_description_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ],
            ],
        ];

        return $fields;
    }

    public function getAddBlockFieldsValues($block, $fields = [])
    {
        $fields = parent::getAddBlockFieldsValues($block, $fields);

        $fields['show_description'] = Tools::getValue('show_description', $block->show_description);

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            if (isset($block->title[$lang['id_lang']])) {
                $fields['description'][$lang['id_lang']] = Tools::getValue(
                    'description_' . (int) $lang['id_lang'],
                    $block->description[$lang['id_lang']]
                );
            } else {
                $fields['description'][$lang['id_lang']] = '';
            }
        }

        return $fields;
    }
}
