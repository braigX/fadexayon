<?php

if (!defined('_PS_VERSION_'))
  exit;

function upgrade_module_3_1_3($object)
{
  return $object->upgradeTo313();
}