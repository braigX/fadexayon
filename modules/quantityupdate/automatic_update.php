<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.01.16
 * Time: 15:38
 */


include(dirname(__FILE__).'/../../config/config.inc.php');
//Context::getContext()->controller = 'AdminController';

  if ( !(int)Configuration::get('PS_SHOP_ENABLE') ){
    if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
      if( !Configuration::get('PS_MAINTENANCE_IP') ){
        Configuration::updateValue('PS_MAINTENANCE_IP', Tools::getRemoteAddr() );
      }
      else{
        Configuration::updateValue('PS_MAINTENANCE_IP', Configuration::get('PS_MAINTENANCE_IP') . ',' . Tools::getRemoteAddr());
      }
    }
  }

include(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/classes/update.php');
include_once(_PS_MODULE_DIR_.'quantityupdate/classes/quantityUpdateTools.php');

try{
  checkConfig();
  if (Tools::getValue('secure_key')) {

    $secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
    if (!empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
      if( !Tools::getValue('id_shop') ){
        throw new Exception('id_shop is Empty');
      }
      $config = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . Tools::getValue('settings'));
      $config = quantityUpdateTools::jsonDecode($config);

      if( $config['feed_source'] == 'file_url' ){
        copyFile($config);
      } else {
        copyFromFtp($config);
      }

      $export = new updateProductCatalog(array(
        'id_shop' => Tools::getValue('id_shop'),
        'id_lang' => false,
        'format_file' => $config['format'],
        'field_update' => $config,
        'field_for_update' => $config['product_identifier'],
        'in_store_not_in_file' => $config['in_store_not_in_file'],
        'in_store_and_in_file' => $config['in_store_and_in_file'],
        'zero_quantity_disable' => $config['zero_quantity_disable']
      ));

      if ($config['disable_hooks']) {
        define('PS_INSTALLATION_IN_PROGRESS', true);
      }

      $res = $export->update(Tools::getValue('limit',0));
      if( is_int($res) ){
        Tools::redirect(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/quantityupdate/automatic_update.php?settings='.Tools::getValue('settings').'&id_shop='.Tools::getValue('id_shop').'&secure_key='.Tools::getValue('secure_key').'&limit='.$res);
        die;
      }

      sendEmail(false, $res['error_logs'], $res['message'] );
      echo Module::getInstanceByName('quantityupdate')->l('Products Update Report sent on your email (if you set up it in settings)!','automatic_update');

      updateProductCatalog::clearIdsOfProductsForUpdate();
    } else{
      echo (Module::getInstanceByName('quantityupdate')->l('Secure key is wrong','automatic_update', 'automatic_update'));
      die;
    }
  } else{
    echo (Module::getInstanceByName('quantityupdate')->l('Secure key is wrong','automatic_update', 'automatic_update'));
    die;
  }
}
catch( Exception $e ){
  sendEmail($e->getMessage());

  echo '<strong>Error: </strong>' . $e->getMessage();
  updateProductCatalog::clearIdsOfProductsForUpdate();
}

  function copyFromFtp( $config )
  {
    if( !$config['ftp_server'] || !Validate::isUrl( $config['ftp_server'] ) ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please enter valid FTP Server!'));
    }

    if( !$config['ftp_user'] ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please enter valid FTP User Name!'));
    }

    if( !$config['ftp_password'] ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please enter valid FTP Password!'));
    }

    if( !$config['ftp_file_path'] ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please enter valid FTP File Path!'));
    }

    $conn_id = @ftp_connect($config['ftp_server']);
    if( !$conn_id ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Can not connect to your FTP Server!'));
    }

    $login_result = @ftp_login($conn_id, $config['ftp_user'], $config['ftp_password']);

    if( !$login_result ){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Can not Login to your FTP Server, please check access!'));
    }

    $format = explode('.',basename($config['ftp_file_path']));
    $format = end($format);

    if( $format != Tools::strtolower($config['format']) ){
      throw new Exception(sprintf(Module::getInstanceByName('quantityupdate')->l('File must be in %s format!'),Tools::strtoupper($config['format'])));
    }

    $dest = _PS_MODULE_DIR_ . 'quantityupdate/files/'.Tools::getValue('settings').'_import.'.$config['format'];

    if (!@ftp_get($conn_id, $dest, $config['ftp_file_path'], FTP_BINARY)) {
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Can not download file from FTP, please check file path!'));
    }

    $mime = mime_content_type($dest);

    if( strpos($mime, 'octet-stream') === false && strpos($mime, 'text') === false && strpos($mime, 'csv') === false && strpos($mime, 'officedocument') === false && strpos($mime, 'vnd.openxmlformats') === false ){
      unlink($dest);
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('File for import is not valid!'));
    }
  }

function copyFile( $config )
{
  $dest = _PS_MODULE_DIR_ . 'quantityupdate/files/'.Tools::getValue('settings').'_import.'.$config['format'];
  $remoteHeaders = @get_headers($config['file_url']);
  $checkFormatFile = false;

  foreach ( $remoteHeaders as $header ){
    if( strpos($header, 'csv') !== false || strpos($header, 'officedocument') !== false || strpos($header, 'octet-stream') !== false || strpos($header, 'text') !== false || strpos($header, 'vnd.openxmlformats') !== false ){
      $checkFormatFile = true;
      break;
    }
  }

  if( !$checkFormatFile ){
    throw new Exception(Module::getInstanceByName('quantityupdate')->l('File for import is not valid!'));
  }

  if( !@copy($config['file_url'], $dest) ){
    throw new Exception(Module::getInstanceByName('quantityupdate')->l('Can not copy file for import, please check module folder file permissions or contact us.'));
  }
}

function checkConfig()
{

  if( !Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . Tools::getValue('settings')) ){
    echo Module::getInstanceByName('quantityupdate')->l('Update Settings does not exists!', 'automatic_update');
    die;
  }
}

function sendEmail( $error = false, $link = false, $message = false )
{
  $config = Configuration::get('GOMAKOIL_QUANTITYUPDATE_' . Tools::getValue('settings'));
  $config = quantityUpdateTools::jsonDecode($config);
  $emails = $config['emails'];
  $emails = trim($emails);
  if( !$emails ){
    return false;
  }
  $emails = explode("\n", $emails);

  foreach ($emails as $users_email){
    $users_email = trim($users_email);
    $mailMessage = '';
    $mailMessage .= '<div style="width: 50%; min-width: 160px;margin: 0 auto;margin-top: 40px;margin-bottom: 40px;border: 1px solid #dadada;border-radius: 6px;    ">';
    $mailMessage .= '<div style="padding: 20px;border-bottom: 1px solid #dadada;font-size: 20px;text-align: center;
        border-radius: 6px 6px 0px 0px;
        background-image: -ms-linear-gradient(top, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);
        background-image: -moz-linear-gradient(top, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);
        background-image: -o-linear-gradient(top, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);
        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #FFFFFF), color-stop(20, #FFFFFF), color-stop(40, #FCFCFC), color-stop(60, #FAFAFA), color-stop(80, #FAFAFA), color-stop(100, #EDEDED));
        background-image: -webkit-linear-gradient(top, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);
        background-image: -webkit-linear-gradient(top, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);
        background-image: linear-gradient(to bottom, #FFFFFF 0%, #FFFFFF 20%, #FCFCFC 40%, #FAFAFA 60%, #FAFAFA 80%, #EDEDED 100%);">'.Module::getInstanceByName('quantityupdate')->l('Products Update Report', 'automatic_update').'</div><div style="padding: 30px;font-size: 14px;">';
    if( $error ){
      $mailMessage .= '<div style="margin-bottom: 10px;"><div style="margin: 2px 10px 2px 0px;color: red"><strong>'.Module::getInstanceByName('quantityupdate')->l('Error:', 'automatic_update'). '</strong> ' . $error . '</div><div style="clear: both;"></div></div>';
    }
    if( $message ){
      $mailMessage .= '<div style="margin-bottom: 10px;"><div style="float: left;width: 100px;margin: 2px 10px 2px 0px;"><strong>'.Module::getInstanceByName('quantityupdate')->l('Update date:', 'automatic_update').'</strong></div><div style="float: left;margin-top: 2px;"> ' . date('d/m/Y G:i:s') .'</div><div style="clear: both;"></div></div>';
      $mailMessage .= '<div style="margin-bottom: 10px;"><div style="float: left;width: 100px;margin: 2px 10px 2px 0px;"><strong>'.Module::getInstanceByName('quantityupdate')->l('Message:', 'automatic_update').'</strong></div><div style="float: left;margin-top: 2px;"> ' . $message .'</div><div style="clear: both;"></div></div>';
    }
    if( $link ){
      $mailMessage .= '<div style="margin-bottom: 10px;"><div style="float: left;width: 100px;margin: 2px 10px 2px 0px;"><strong>'.Module::getInstanceByName('quantityupdate')->l('Error Log:', 'automatic_update').'</strong></div><div style="float: left;margin-top: 2px;"> <a style="color: #00aff0;" href="' . $link . '">' . $link . '</a></div><div style="clear: both;"></div></div>';
    }
    $mailMessage .= '<div style="clear: both;display: block !important;"></div><div style="clear: both;display: block !important;"></div><div style="clear: both; display: block !important;">';
    $template_vars = array('{content}' => $mailMessage);
    $mail = Mail::Send(
      Configuration::get('PS_LANG_DEFAULT'),
      'notification',
      Module::getInstanceByName('quantityupdate')->l('Quantity And Price Update Report', 'automatic_update'),
      $template_vars,
      "$users_email",
      NULL,
      Tools::getValue('email') ? Tools::getValue('email') : NULL,
      Tools::getValue('fio') ? Tools::getValue('fio') : NULL,
      NULL,
      NULL,
      dirname(__FILE__).'/mails/');
    if( !$mail ){
      echo Module::getInstanceByName('quantityupdate')->l('Some error occurred please contact us!', 'automatic_update');
      die;
    }
  }
}
