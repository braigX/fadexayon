<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_2_0_4($object)
{
	$tab             = new Tab();
	$tab->active     = 1;
	$tab->class_name = 'AdminCrazyTroubleshooting';
	$tab->name       = array();
	foreach ( Language::getLanguages( true ) as $lang ) {
		$tab->name[ $lang['id_lang'] ] = 'Crazyelements Troubleshooting';
	}
	$tab->id_parent = -1;
	$tab->module    = $object->name;
	$tab->add();
	return true;
}