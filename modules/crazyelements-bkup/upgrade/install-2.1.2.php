<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_2_1_2($object)
{
	$object->registerHook( 'displayAdminProductsExtra' );	
	$object->registerHook('displayBackOfficeHeader');
	return true;
}