<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_2_1_0($object)
{
	$id_tab   = (int) Tab::getIdFromClassName( 'AdminCrazyTemplates' );
	$id_parent = (int) Tab::getIdFromClassName( 'AdminCrazyEditor' );


	if ( ! $id_tab ) {
		$tab             = new Tab();
		$tab->active     = 1;
		$tab->class_name = 'AdminCrazyTemplates';
		$tab->name       = array();
		foreach ( Language::getLanguages( true ) as $lang ) {
			$tab->name[ $lang['id_lang'] ] = 'Saved Templates';
		}
		$tab->id_parent = $id_parent;
		$tab->module    = $object->name;

		$tab->add();
	}
	return true;
}