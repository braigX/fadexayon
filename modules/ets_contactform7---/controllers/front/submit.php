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
 
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/form-tag.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/function.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/Contact.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/ContactMessage.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/contact-form.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/form-tags-manager.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/submission.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/mail.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/pipe.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/integration.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/recaptcha.php');
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/validation.php');
class Ets_contactform7SubmitModuleFrontController extends ModuleFrontController
{
    /**
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        if($id = (int)Tools::getValue('_etscf7_container_post'))
        {
            $item = etscf7_contact_form( $id );
           // $unit_tag = ($unit_tag = Tools::getValue('_etscf7_unit_tag')) && Validate::isCleanHtml($unit_tag)  ? $unit_tag:'';
          $unit_tag_raw = Tools::getValue('_etscf7_unit_tag');
$unit_tag = Validate::isCleanHtml($unit_tag_raw) ? htmlspecialchars($unit_tag_raw, ENT_QUOTES, 'UTF-8') : '';
			
			
			$item->unit_tag = $unit_tag;
            $result = $item->submit();
            $response = array(
        		'into' => $unit_tag,
        		'status' => $result['status'],
        		'message' => $result['message'],
        	);
            if ( 'validation_failed' == $result['status'] ) {
        		$invalid_fields = array();
        		foreach ( (array) $result['invalid_fields'] as $name => $field ) {
        			$invalid_fields[] = array(
        				'into' => 'span.wpcf7-form-control-wrap.'
        					. ets_sanitize_html_class( $name ),
        				'message' => $field['reason'],
        				'idref' => $field['idref'],
        			);
        		}
        		$response['invalidFields'] = $invalid_fields;
        	}
            die(
                json_encode($response)
            );
        }
    }
}