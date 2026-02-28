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

class AdminFaqopItemController extends AdminFaqopBasicItemController
{
    public function initContent()
    {
        $this->content .= $this->module->displayNav($this->module->helper->getItemsParent());

        $languages = Language::getLanguages(false);
        if (count($languages) > 1) {
            $this->content .= $this->module->mes->getMultiLanguageInfoMsg();
        }
        $this->content .= $this->module->mes->getInfoMultishopAboutItem();
        parent::initContent();
    }

    public function postProcessItem()
    {
        if (ConfigsFaq::DEMO_MODE && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->output .= $this->module->displayError($this->l('You cannot edit in demo mode'));
        } else {
            if ($this->postValidationItem()) {
                // we don't need shop when just delete and clone (faq items are for all shops
                if (Tools::isSubmit('delete')) {
                    $factory = new OpFaqModelFactory();
                    $item = $factory->createItem($this->module);
                    $item->deleteWithRedirect();
                }

                if (Tools::isSubmit('clone')) {
                    $factory = new OpFaqModelFactory();
                    $item = $factory->createItem($this->module);
                    $item->cloneWithRedirect();
                }

                if (Tools::isSubmit('remove')) {
                    if (!$this->module->helper->isCurrentShopChosen()) {
                        Tools::redirectAdmin($this->back . '&removeWrong=1');
                    } else {
                        $factory = new OpFaqModelFactory();
                        $item = $factory->createItem($this->module);
                        $item->removeWithRedirect();
                    }
                }

                if ($this->module->helper->checkIfSubmitClicked()) {
                    $this->postProcessSubmitItem();
                }
            }
        }

        return $this->output;
    }

    protected function postValidationItem($errors = [])
    {
        $errorsInList = false;

        $allow_iframe = (bool) Configuration::get('PS_ALLOW_HTML_IFRAME');

        if (Tools::isSubmit('delete') || Tools::isSubmit('clone') || Tools::isSubmit('remove')) {
            if (!$this->module->rep->itemExists($this->id_item)) {
                $errorsInList = true;
            }
        }

        /* Redirect with error if change status or delete without success */
        if ($errorsInList) {
            Tools::redirectAdmin($this->back . '&itemWrong=1');

            return false;
        }

        if ($this->module->helper->checkIfSubmitClicked()) {
            /* Checks title */
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                if (!Validate::isCleanHtml(Tools::getValue('question_' . $language['id_lang']), $allow_iframe)) {
                    $errors[] = $this->l('Script or iframe tags are not allowed. To enable iframes, change 
                setting Preferences -> General -> Allow iframes on HTML fields');
                }

                if (!Validate::isCleanHtml(Tools::getValue('answer_' . $language['id_lang']), $allow_iframe)) {
                    $errors[] = $this->l('Script or iframe tags are not allowed. To enable iframes, change 
                setting Preferences -> General -> Allow iframes on HTML fields');
                }

                if (Tools::strlen(Tools::getValue('question_' . $language['id_lang'])) > 60000) {
                    $errors[] = $this->l(
                        'The question is too long.'
                    );
                }
                if (Tools::strlen(Tools::getValue('answer_' . $language['id_lang'])) > 60000) {
                    $errors[] = $this->l('The answer is too long.');
                }
                if (Tools::strlen(Tools::getValue('title_' . $language['id_lang'])) > 255) {
                    $errors[] = $this->l('The item title is too long.');
                }
            }
        }

        return parent::postValidationItem($errors);
    }

    protected function postProcessSubmitItem()
    {
        /* Here we update Item */
        $data = parent::postProcessSubmitItem();

        /* Create new Item. Can create only in general Item settings. Can update from other forms too */

        if (!Tools::isSubmit('id_item') && Tools::isSubmit('create')) {
            $item = $data['item'];
            $errors = $data['errors'];

            if (!$item->add()) {
                $this->errors[] = $this->module->displayError($this->l(
                    'Could not create item.'
                ));
            }

            try {
                $itemController = $this->context->link->getAdminLink($this->controller_name) .
                    '&edit=1' .
                    '&id_item=' . (int) $item->id .
                    $this->module->helper->createAnticacheString();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }

            $postHelper = new PostHelper(
                1,
                $errors,
                $itemController,
                $this->back,
                $this->module
            );

            $this->output .= $postHelper->post();
        }
    }

    public function setItemFields($item)
    {
        /* Sets each langue fields */
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $question = strip_tags(Tools::getValue('question_' . $language['id_lang']));
            $title = Tools::getValue('title_' . $language['id_lang']);

            $item->question[$language['id_lang']] = Tools::getValue('question_' . $language['id_lang']);
            $item->answer[$language['id_lang']] = Tools::getValue('answer_' . $language['id_lang']);
            $item->title[$language['id_lang']] = $this->module->helper->setItemTitle($question, $title);
        }
    }

    public function getFieldsFormFirst($fields = ['form' => ['legend' => [], 'input' => []]])
    {
        $fields['form']['legend'] = [
            'title' => $this->l('General settings'),
            'icon' => 'icon-edit',
        ];
        $fields['form']['input'][] = [
            'type' => 'textarea',
            'label' => $this->l('Question'),
            'name' => 'question',
            'autoload_rte' => true,
            'lang' => true,
        ];
        $fields['form']['input'][] = [
            'type' => 'textarea',
            'label' => $this->l('Answer'),
            'name' => 'answer',
            'autoload_rte' => true,
            'lang' => true,
        ];
        $fields['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Title (optional)'),
            'name' => 'title',
            'autoload_rte' => true,
            'lang' => true,
            'desc' => $this->l('Max 55 symbols, more will be truncated. This title is only for 
                        admin area. If not stated, truncated question will be used.'),
        ];

        return $fields;
    }

    public function renderItemNav($active_url = 'general')
    {
        return parent::renderItemNav($active_url);
    }

    public function getAddItemFieldsValues($item, $fields = [])
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            if (isset($item->question[$lang['id_lang']])) {
                $fields['question'][$lang['id_lang']] = Tools::getValue(
                    'question_' . (int) $lang['id_lang'],
                    $item->question[$lang['id_lang']]
                );
            } else {
                $fields['question'][$lang['id_lang']] = '';
            }
            if (isset($item->answer[$lang['id_lang']])) {
                $fields['answer'][$lang['id_lang']] = Tools::getValue(
                    'answer_' . (int) $lang['id_lang'],
                    $item->answer[$lang['id_lang']]
                );
            } else {
                $fields['answer'][$lang['id_lang']] = '';
            }
            if (isset($item->title[$lang['id_lang']])) {
                $fields['title'][$lang['id_lang']] = Tools::getValue(
                    'title_' . (int) $lang['id_lang'],
                    $item->title[$lang['id_lang']]
                );
            } else {
                $fields['title'][$lang['id_lang']] = '';
            }
        }

        return $fields;
    }
}
