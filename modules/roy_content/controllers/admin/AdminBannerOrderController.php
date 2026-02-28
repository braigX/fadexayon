<?php
class AdminBannerOrderController extends ModuleAdminController
{
	public function ajaxProcessUpdatePosition()
	{
		$items = Tools::getValue('item');
		$total = count($items);
		$success = true;
		for ($i = 1; $i <= $total; $i++)
			$success &= Db::getInstance()->update('roy_content', array('item_order' => $i), '`id_item` = '.preg_replace('/(item-)([0-9]+)/', '${2}', $items[$i - 1]));
		if (!$success)
			die(Tools::jsonEncode(array('error' => 'Update Fail')));
		die(Tools::jsonEncode(array('success' => 'Update Success !', 'error' => false)));
	}
}
