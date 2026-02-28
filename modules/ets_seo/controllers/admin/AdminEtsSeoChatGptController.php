<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoChatGptController
 *
 * @property \Ets_Seo $module
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoChatGptController extends ModuleAdminController
{
    public $errors = [];
    /**
     * @var \EtsSeoChatGpt
     */
    private $chatGptClient;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'general' => [
                'title' => $this->module->l('ChatGPT Integration', 'AdminEtsSeoChatGptController'),
                'description' => '',
                'fields' => $seoDef->fields_config()['chat_gpt'],
                'icon' => '',
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminEtsSeoChatGptController'),
                ],
            ],
            'templates' => [
                'title' => $this->module->l('Prompt templates', 'AdminEtsSeoChatGptController'),
                'icon' => '',
                'fields' => [
                    'x' => [
                        'type' => 'custom_html',
                        'html' => $this->displayListTemplateChatGPT(),
                        'no_multishop_checkbox' => true,
                    ],
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features','AdminEtsSeoChatGptController');
        }
        if ((bool) Configuration::get('ETS_TRANS_ENABLE_CHATGPT')) {
            $this->warnings[] = $this->module->l('You enabled ChatGPT on "ETS Translate" module. To using ChatGPT on Seo Audit, please disable ChatGPT feature on "ETS Translate" module first.','AdminEtsSeoChatGptController');
        }
    }

    public function postProcess()
    {
        $this->_processAjaxRequest();
        if (Tools::getValue('ETS_SEO_CHAT_GPT_ENABLE')) {
            $this->fields_options['general']['fields']['ETS_SEO_CHAT_GPT_API_TOKEN']['required'] = true;
        }
        parent::postProcess();
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->module->_clearCache('*');
        }
    }

    public function initContent()
    {
        $this->getJsDefHelper()->addBo('chatGptAdminUrl', $this->context->link->getAdminLink('AdminEtsSeoChatGpt'));
        $availShortCodes = [
            'AdminProducts' => ['product_name', 'meta_title', 'meta_description', 'default_category', 'language', 'brand'],
            'AdminCategories' => ['category_name', 'meta_title', 'meta_description', 'language'],
            'AdminCmsContent' => ['page_name', 'meta_title', 'meta_description', 'language'],
        ];
        $helpBlock = [];
        foreach ($availShortCodes as $key => $codes) {
            if (!isset($helpBlock[$key])) {
                $helpBlock[$key] = $this->module->l('Available short code: ', 'AdminEtsSeoChatGptController');
            }
            $helpBlock[$key] .= implode(', ', array_map(static function ($v) {
                return sprintf('{%s}', $v);
            }, $codes));
        }
        $this->getJsDefHelper()->addBo('transMsg.gptTplContentHelp', $helpBlock);

        return parent::initContent();
    }

    private function _processAjaxRequest()
    {
        $chatBoxTplPath = 'chatgpt/chatbox.tpl';
        if (Tools::isSubmit('getChatBox')) {
            $currentPage = Tools::getValue('currentPage');
            if (!EtsSeoGptTemplate::validateDisplayPage($currentPage)) {
                $this->errors[] = $this->module->l('Invalid page param provided', 'AdminEtsSeoChatGptController');
            }
            if (!$this->errors) {
                try {
                    exit(json_encode(['html' => $this->getChatBox($currentPage), 'ok' => true]));
                } catch (PrestaShopException $e) {
                    $this->errors[] = $e->getMessage();
                } catch (SmartyException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
            exit(json_encode(['ok' => false, 'message' => $this->errors[0], 'hasErrors' => true, 'errors' => $this->errors]));
        }

        if (Tools::isSubmit('clearAllMessages')) {
            $this->module->_clearCache($chatBoxTplPath);
            exit(json_encode(['ok' => \EtsSeoGptMessage::deleteAllMessages()]));
        }

        if (Tools::isSubmit('sendMessage')) {
            $message = Tools::getValue('message');
            $parentId = null;
            if (!Validate::isCleanHtml($message, true)) {
                $this->errors[] = $this->module->l('Message is not valid', 'AdminEtsSeoChatGptController');
            }
            if (!$this->errors) {
                try {
                    $userMsg = new \EtsSeoGptMessage();
                    $userMsg->is_chatgpt = 0;
                    $userMsg->message = $message;
                    if (!$userMsg->add()) {
                        $this->errors[] = $this->module->l('An error occurred while saving the user message', 'AdminEtsSeoChatGptController');
                    }
                    $parentId = $userMsg->id;
                    if (!$this->errors) {
                        $result = $this->getChatGptClient()->chat($message);
                        $gptMsg = new \EtsSeoGptMessage();
                        $gptMsg->is_chatgpt = 1;
                        $gptMsg->message = $result;
                        $gptMsg->id_parent = $parentId;
                        if ($gptMsg->add()) {
                            $this->module->_clearCache($chatBoxTplPath);
                            exit(json_encode(['ok' => true, 'result' => nl2br($result), 'id' => $gptMsg->id, 'parentId' => $parentId]));
                        }
                    }
                    $this->errors[] = $this->module->l('An error occurred while saving the GPT response message', 'AdminEtsSeoChatGptController');
                } catch (EtsSeoChatGptException $e) {
                    $this->errors[] = $e->getMessage();
                } catch (PrestaShopException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
            $parentId && (new \EtsSeoGptMessage($parentId))->delete();
            exit(json_encode(['ok' => false, 'message' => $this->errors[0], 'hasErrors' => true, 'errors' => $this->errors]));
        }

        if (Tools::isSubmit('getEditFrm')) {
            $id = Tools::getValue('id');
            if ($id && $id > 0) {
                exit(json_encode([
                    'ok' => true,
                    'html' => $this->getChatGptTemplateEditForm($id),
                    'message' => $this->module->l('Delete successfully.', 'AdminEtsSeoChatGptController'),
                ]));
            }

            $this->errors[] = $this->module->l('Id template is required.', 'AdminEtsSeoChatGptController');
            exit(json_encode(['ok' => false, 'message' => $this->errors[0], 'hasErrors' => true, 'errors' => $this->errors]));
        }

        if (Tools::isSubmit('deleteGptTemplate')) {
            $id = Tools::getValue('id');
            if ($id && $id > 0) {
                try {
                    $obj = new \EtsSeoGptTemplate((int) $id);
                    $chatBoxCacheId = $this->module->_getCacheId(['chatBoxDisplayPage' => $obj->display_page]);
                    $this->module->_clearCache($chatBoxTplPath, $chatBoxCacheId);
                    exit(json_encode([
                        'ok' => $obj->delete(),
                        'totalRecords' => \EtsSeoGptTemplate::countAllTemplates(),
                    ]));
                } catch (PrestaShopException $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            $this->errors[] = $this->module->l('Id template is required.', 'AdminEtsSeoChatGptController');
            exit(json_encode(['ok' => false, 'message' => $this->errors[0], 'hasErrors' => true, 'errors' => $this->errors]));
        }
        if (Tools::isSubmit('saveTemplateGPT')) {
            $tplId = Tools::getValue('id_ets_seo_gpt_template');
            $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
            $labels = Tools::getValue('label');
            $contents = Tools::getValue('content');
            $displayPage = Tools::getValue('display_page');
            if (!$displayPage) {
                $this->errors[] = $this->module->l('Display page is required.', 'AdminEtsSeoChatGptController');
            }
            if ($displayPage && !\EtsSeoGptTemplate::validateDisplayPage($displayPage)) {
                $this->errors[] = $this->module->l('Display page is invalid.', 'AdminEtsSeoChatGptController');
            }
            if (!isset($labels[$idLangDefault]) || @!$labels[$idLangDefault]) {
                $this->errors[] = $this->module->l('Label is required.', 'AdminEtsSeoChatGptController');
            }
            if (!isset($contents[$idLangDefault]) || @!$contents[$idLangDefault]) {
                $this->errors[] = $this->module->l('Content is required.', 'AdminEtsSeoChatGptController');
            }
            if (!$this->errors) {
                if ($tplId) {
                    $obj = new \EtsSeoGptTemplate((int) $tplId);
                } else {
                    $obj = new \EtsSeoGptTemplate();
                }
                $obj->display_page = $displayPage;
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $labelByLang = $labels[$language['id_lang']];
                    $contentByLang = $contents[$language['id_lang']];
                    if ($labelByLang && !Validate::isCleanHtml($labelByLang)) {
                        $this->errors[] = sprintf($this->module->l('Label in %s is not valid', 'AdminEtsSeoChatGptController'), $language['iso_code']);
                    } else {
                        $obj->label[$language['id_lang']] = $labelByLang ?: $labels[$idLangDefault];
                    }
                    if ($contentByLang && !Validate::isCleanHtml($contentByLang)) {
                        $errors[] = sprintf($this->module->l('Content in %s is not valid', 'AdminEtsSeoChatGptController'), $language['iso_code']);
                    } else {
                        $obj->content[$language['id_lang']] = $contentByLang ?: $contents[$idLangDefault];
                    }
                }
                if (!$this->errors) {
                    $status = $obj->id ? $this->module->l('Updated successfully', 'AdminEtsSeoChatGptController') : $this->module->l('Added successfully', 'AdminEtsSeoChatGptController');
                    $rs = $obj->id ? $obj->update() : $obj->add();
                    if ($rs) {
                        $chatBoxCacheId = $this->module->_getCacheId(['chatBoxDisplayPage' => $displayPage]);
                        $tplPath = $this->module->getLocalPath() . 'views/templates/hook/chatgpt/template-add.tpl';
                        $editFrmCacheId = $this->module->_getCacheId(['EtsSeoGptTemplate' => ['EditFrm' => $obj->id]]);
                        $this->module->_clearCache($tplPath, $editFrmCacheId);
                        $this->module->_clearCache($chatBoxTplPath, $chatBoxCacheId);
                        $this->context->smarty->assign(['template' => get_object_vars($obj), 'transMsg' => $this->_chatGptTemplateTransMsg()]);
                        exit(json_encode([
                            'ok' => true,
                            'message' => $status,
                            'html' => $this->module->fetch($this->module->getLocalPath() . 'views/templates/hook/chatgpt/template-line.tpl'),
                            'totalRecords' => \EtsSeoGptTemplate::countAllTemplates(),
                        ]));
                    }

                    $this->errors[] = $this->module->l('An error occurred while saving the template', 'AdminEtsSeoChatGptController');
                }
            }
            exit(json_encode(['ok' => false, 'message' => $this->errors[0], 'hasErrors' => true, 'errors' => $this->errors]));
        }
    }

    /**
     * @param string|null $currentPage
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \SmartyException
     */
    private function getChatBox($currentPage = null)
    {
        $tplPath = 'chatgpt/chatbox.tpl';
        $tplCacheId = $this->module->_getCacheId(['chatBoxDisplayPage' => $currentPage ?: 'all']);
        if (!$this->module->isCached($tplPath, $tplCacheId)) {
            $languages = Language::getLanguages(false);
            $messages = array_reverse(\EtsSeoGptMessage::getMessages(false, false));
            $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
            $templates = \EtsSeoGptTemplate::getAllTemplates($currentPage);
            foreach ($templates as &$template) {
                $template = get_object_vars($template);
            }
            unset($template);
            $this->context->smarty->assign([
                'languages' => $languages,
                'defaultLang' => $defaultLang,
                'templates' => $templates,
                'messages' => $messages,
            ]);
        }

        return $this->module->display($this->module->getLocalPath(), $tplPath, $tplCacheId);
    }

    /**
     * @param int $id
     *
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \SmartyException
     */
    private function getChatGptTemplateEditForm($id)
    {
        $obj = new \EtsSeoGptTemplate((int) $id);
        $tplPath = $this->module->getLocalPath() . 'views/templates/hook/chatgpt/template-add.tpl';
        $cacheId = $this->module->_getCacheId(['EtsSeoGptTemplate' => ['EditFrm' => $obj->id]]);
        if (!$this->module->isCached($tplPath, $cacheId)) {
            $this->context->smarty->assign([
                'template' => get_object_vars($obj),
                'fields' => $this->_chatGptTemplateFieldListAdd(),
                'languages' => \Language::getLanguages(false),
                'transMsg' => $this->_chatGptTemplateTransMsg(),
            ]);
        }

        return $this->module->fetch($tplPath, $cacheId);
    }

    /**
     * @return string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \SmartyException
     */
    private function displayListTemplateChatGPT()
    {
        $totalRecords = \EtsSeoGptTemplate::countAllTemplates();
        $tplPath = $this->module->getLocalPath() . 'views/templates/hook/chatgpt/template-list.tpl';
        $cacheId = $this->module->_getCacheId(['templateChatGPT' => ['list' => $totalRecords]]);
        if (!$this->module->isCached($tplPath, $cacheId)) {
            $templates = \EtsSeoGptTemplate::getAllTemplates();
            $languages = \Language::getLanguages();
            $listData = [
                'title' => $this->module->l('Prompt templates', 'AdminEtsSeoChatGptController'),
                'languages' => $languages,
                'fields_list' => $this->_chatGptTemplateFieldListAdd(),
                'templates' => $templates,
                'totalRecords' => $totalRecords,
                'transMsg' => $this->_chatGptTemplateTransMsg(),
            ];
            $this->context->smarty->assign($listData);
        }

        return $this->module->fetch($tplPath, $cacheId);
    }

    /**
     * @return array[]
     */
    private function _chatGptTemplateFieldListAdd()
    {
        return [
            'display_page' => [
                'title' => $this->module->l('Display Page', 'AdminEtsSeoChatGptController'),
                'type' => 'select',
                'defaultValue' => 'AdminProducts',
                'options' => [
                    ['value' => 'AdminProducts', 'name' => $this->module->l('Product Page', 'AdminEtsSeoChatGptController')],
                    ['value' => 'AdminCategories', 'name' => $this->module->l('Category Page', 'AdminEtsSeoChatGptController')],
                    ['value' => 'AdminCmsContent', 'name' => $this->module->l('CMS Page', 'AdminEtsSeoChatGptController')],
                ],
            ],
            'label' => [
                'title' => $this->module->l('Label', 'AdminEtsSeoChatGptController'),
                'type' => 'text',
                'defaultValue' => '',
                'lang' => true,
            ],
            'content' => [
                'title' => $this->module->l('Content', 'AdminEtsSeoChatGptController'),
                'type' => 'textarea',
                'defaultValue' => '',
                'lang' => true,
            ],
        ];
    }

    /**
     * @return string[]
     */
    private function _chatGptTemplateTransMsg()
    {
        return [
            'label' => $this->module->l('Label', 'AdminEtsSeoChatGptController'),
            'content' => $this->module->l('Content', 'AdminEtsSeoChatGptController'),
            'action' => $this->module->l('Action', 'AdminEtsSeoChatGptController'),
            'addNew' => $this->module->l('Add new', 'AdminEtsSeoChatGptController'),
            'edit' => $this->module->l('Edit', 'AdminEtsSeoChatGptController'),
            'del' => $this->module->l('Delete', 'AdminEtsSeoChatGptController'),
            'delConfirm' => $this->module->l('Do you want to delete this template?', 'AdminEtsSeoChatGptController'),
            'close' => $this->module->l('Close', 'AdminEtsSeoChatGptController'),
            'save' => $this->module->l('Save', 'AdminEtsSeoChatGptController'),
            'cancel' => $this->module->l('Cancel', 'AdminEtsSeoChatGptController'),
        ];
    }

    /**
     * @return \EtsSeoChatGpt
     */
    public function getChatGptClient()
    {
        if (!$this->chatGptClient instanceof EtsSeoChatGpt) {
            $this->chatGptClient = EtsSeoChatGpt::getInstance();
        }

        return $this->chatGptClient;
    }

    /**
     * @return \EtsSeoJsDefHelper
     */
    private function getJsDefHelper()
    {
        return $this->module->getJsDefHelper();
    }
}
