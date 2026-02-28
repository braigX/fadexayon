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

if (!defined('_PS_VERSION_')) { exit; }

class Ets_reviewsUploadModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function postProcess()
    {
        if (Tools::getValue('ajax') && ($action = Tools::getValue('action')) && Validate::isCleanHtml($action)) {
            $action = 'ajaxProcess' . Tools::ucfirst($action);
            if (method_exists($this, $action))
                $this->{$action}();
            else
                die(json_encode(['errors' => $this->module->l('Access denied!', 'upload')]));
        }
    }

    static $avatar_field = 'avatar';

    public function ajaxProcessUploadProfileImage()
    {
        $pcCustomer = new EtsRVProductCommentCustomer($this->context->customer->id);
        $files = EtsRVTools::getInstance()->processUploadImage(self::$avatar_field, 'a', $this->errors, false, null, 150, 150);
        if (!$this->errors) {

            list($image, $file_dest, $file_name) = $files;
            $oldImage = $pcCustomer->id && $pcCustomer->avatar ? $file_dest . $pcCustomer->avatar : '';

            if (trim($image) !== '')
                $pcCustomer->avatar = $image;

            if (!$pcCustomer->save()) {
                if (trim($image) !== '' && !@file_exists($file_name))
                    @unlink($file_name);
                $this->errors[] = sprintf($this->module->l('An error occurred while adding this image: %s', 'upload'), Tools::stripslashes($image));
            } elseif (trim($image) !== '' && @file_exists($oldImage)) {
                @unlink($oldImage);
            }

            $this->module->clearCache('footer.tpl', $this->module->getCacheId('footer'));
        }
        $hasError = count($this->errors) ? 1 : 0;
        $this->module->ajaxRender(json_encode([
            'errors' => $hasError ? implode(Tools::nl2br(PHP_EOL), $this->errors) : false,
            'msg' => !$hasError ? $this->module->l('Upload image successfully', 'upload') : '',
        ]));
    }

    public function viewAccess()
    {
        if (!isset($this->context->customer->id) || !$this->context->customer->id) {
            $this->module->ajaxRender([
                'errors' => true,
                'msg' => $this->module->l('Customer does not exist.', 'upload'),
            ]);
        }
    }

    public function ajaxProcessDeleteProfileImage()
    {
        $pcCustomer = new EtsRVProductCommentCustomer($this->context->customer->id);
        $dest = _PS_IMG_DIR_ . $this->module->name . '/a/' . $pcCustomer->avatar;
        $pcCustomer->avatar = '';
        if ($pcCustomer->update()) {
            if (@file_exists($dest))
                @unlink($dest);
            $this->module->clearCache('footer.tpl', $this->module->getCacheId('footer'));
        } else
            $this->errors[] = $this->module->l('Cannot delete avatar.', 'upload');

        $hasError = count($this->errors) ? 1 : 0;
        $this->module->ajaxRender(json_encode([
            'errors' => $hasError ? implode(Tools::nl2br("\n"), $this->errors) : false,
            'msg' => !$hasError ? $this->module->l('Image deleted', 'upload') : '',
        ]));
    }
}
