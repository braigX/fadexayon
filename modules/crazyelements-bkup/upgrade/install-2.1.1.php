<?php
if (!defined('_PS_VERSION_')) {
	exit;
}

function upgrade_module_2_1_1($object)
{
	$id_tab   = (int) Tab::getIdFromClassName( 'AdminCrazySitebuilder' );
	$id_parent = (int) Tab::getIdFromClassName( 'AdminCrazyEditor' );


	if ( ! $id_tab ) {
		$tab             = new Tab();
		$tab->active     = 1;
		$tab->class_name = 'AdminCrazySitebuilder';
		$tab->name       = array();
		foreach ( Language::getLanguages( true ) as $lang ) {
			$tab->name[ $lang['id_lang'] ] = 'Site Builder';
		}
		$tab->id_parent = $id_parent;
		$tab->module    = $object->name;

		$tab->add();
	}

	$id_parent = Tab::getIdFromClassName('AdminCrazySitebuilder');
	$langs     = Language::getLanguages();
	$tabvalue  = array(
		array(
			'class_name' => 'AdminCrazyhbuilder',
			'id_parent'  => $id_parent,
			'module'     => 'crazyelements',
			'name'       => 'Header Builder',
			'active'     => 1,
		),
		array(
			'class_name' => 'AdminCrazyfbuilder',
			'id_parent'  => $id_parent,
			'module'     => 'crazyelements',
			'name'       => 'Footer Builder',
			'active'     => 1,
		),
		array(
			'class_name' => 'AdminCrazyfzfbuilder',
			'id_parent'  => $id_parent,
			'module'     => 'crazyelements',
			'name'       => '404 Builder',
			'active'     => 1,
		)
	);
	foreach ($tabvalue as $tab) {
		$newtab             = new Tab();
		$newtab->class_name = $tab['class_name'];
		$newtab->module     = $tab['module'];
		$newtab->id_parent  = $tab['id_parent'];
		foreach ($langs as $l) {
			$newtab->name[$l['id_lang']] = $tab['name'];
		}
		$newtab->add(true, false);
	}
	return true;
}