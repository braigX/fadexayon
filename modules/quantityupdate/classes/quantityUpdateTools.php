<?php

class quantityUpdateTools{

  public static function jsonEncode($data, $options = 0, $depth = 512)
  {
    return json_encode($data, $options, $depth);
  }

  public static function jsonDecode($data, $assoc = true, $depth = 512, $options = 0)
  {
    if ( ($unserialized = Tools::unSerialize($data)) !== false ){
      return $unserialized;
    }

    return json_decode($data, $assoc, $depth, $options);
  }

}