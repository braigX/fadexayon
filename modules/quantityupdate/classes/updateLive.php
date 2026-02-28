<?php

class updateLiveProductCatalog
{
  private $_context;
  private $_idShop;
  private $_idLang;


  public function __construct($idShop, $idLang)
  {
    include_once(dirname(__FILE__) . '/../../config/config.inc.php');
    include_once(dirname(__FILE__) . '/../../init.php');


    $this->_context = Context::getContext();
    $this->_idShop = $idShop;
    $this->_idLang = $idLang;



  }

  public function update($data)
  {
    $this->validValues($data);
    $this->updateData($data);
    return true;
  }

  public function updateData($data){

    foreach ($data['products'] as $value){
      $this->updateItem($value, $data);
    }

    return true;
  }


  public function updateItem($id, $data){

    $obj = new Product($id, false, $this->_idLang, $this->_idShop);
    $combinations = $obj->getWsCombinations();

    if($data['price_increment'] !== ""){
      $price_increment = (float)$data['price_increment'];
      $type = $data['type_price_increment'];
      $operations = $data['operations'];
      $options = $data['options_live_update'];

      $old_price = (float)$obj->price;

      if($type == 1){
        $pr = $old_price*$price_increment/100;
        if($operations == 1){
          $new_price = $old_price+$pr;
        }
        if($operations == 2){
          $new_price = $old_price-$pr;
        }
      }

      if($type == 2){
        if($operations == 1){
          $new_price = $old_price+$price_increment;
        }

        if($operations == 2){
          $new_price = $old_price-$price_increment;
        }
      }

      if($type == 3){
        if($operations == 3){
          $new_price = $old_price*$price_increment;
        }

        if($operations == 4){
          $new_price = $old_price/$price_increment;
        }
      }

      if($type == 4){
        $new_price = $price_increment;
      }

      if($new_price<0){
        $new_price = 0;
      }

      if($options == 1){
        $obj->price = $new_price;
      }

      if($options == 2){
        if($combinations){
          foreach ($combinations as $combination){
            $this->updateItemCombination($combination['id'], $data);
          }
        }
      }


      if($options == 3){
        $obj->price = $new_price;
        if($combinations){
          foreach ($combinations as $combination){
            $this->updateItemCombination($combination['id'], $data);
          }
        }
      }
    }

    if($data['quantity_increment'] !== ""){
      $quantity_increment = (int)$data['quantity_increment'];
      $quantity_operations = $data['quantity_operations'];
      $type_quantity = $data['type_quantity_increment'];
      if($combinations){
        foreach ($combinations as $combination){
          $this->updateItemQuantity($obj->id, $combination['id'], $quantity_increment, $quantity_operations, $type_quantity);
        }
      }
      else{
        $this->updateItemQuantity($obj->id, 0, $quantity_increment, $quantity_operations, $type_quantity);
      }
    }

    $obj->update();
  }

  public function updateItemQuantity($id, $id_combination, $quantity_increment, $quantity_operations, $type_quantity){

    $old_quantity = StockAvailable::getQuantityAvailableByProduct($id, $id_combination, $this->_idShop);

    if($type_quantity == 1){
      if($quantity_operations == 1){
        $quantity = $old_quantity+$quantity_increment;
      }
      if($quantity_operations == 2){
        $quantity = $old_quantity-$quantity_increment;
      }
    }

    if($type_quantity == 2){
      $quantity = $quantity_increment;
    }
    StockAvailable::setQuantity((int)$id, (int)$id_combination, (int)$quantity, $this->_idShop);
  }

  public function updateItemCombination($id, $data){

    $price_increment = $data['price_increment'];
    $type = $data['type_price_increment'];
    $operations = $data['operations'];

    $obj = new Combination($id, null, $this->_idShop);
    $old_price = $obj->price;

    if($type == 1){
      $pr = $old_price*$price_increment/100;
      if($operations == 1){
        $new_price = $old_price+$pr;
      }
      if($operations == 2){
        $new_price = $old_price-$pr;
      }
    }

    if($type == 2){
      if($operations == 1){
        $new_price = $old_price+$price_increment;
      }

      if($operations == 2){
        $new_price = $old_price-$price_increment;
      }
    }

    if($type == 3){
      if($operations == 3){
        $new_price = $old_price*$price_increment;
      }

      if($operations == 4){
        $new_price = $old_price/$price_increment;
      }
    }

    if($type == 4){
      $new_price = $price_increment;
    }

    $obj->price = $new_price;

    $obj->update();

  }

  public function validValues($data){

    if(!$data['products']){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please select products for update!'));
    }

    if($data['price_increment'] == "" && $data['quantity_increment'] == ""){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Please enter increment!'));
    }

    if($data['price_increment'] !== "" && !Validate::isFloat($data['price_increment'])){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Price increment is not valid!'));
    }

    if($data['type_price_increment'] == 1 && ($data['operations'] != 1 && $data['operations'] != 2)){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Error: use "Percentage" if you need: price+20% or price-25%'));
    }

    if($data['type_price_increment'] == 2 && ($data['operations'] != 1 && $data['operations'] != 2)){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Error: use "Amount" if you need: price+50$ or price-50$'));
    }

    if($data['type_price_increment'] == 3 && ($data['operations'] != 3 && $data['operations'] != 4)){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Error: use "Coefficient" if you need: price*2 or price/4'));
    }

    if($data['quantity_increment'] !== "" && !Validate::isInt($data['quantity_increment'])){
      throw new Exception(Module::getInstanceByName('quantityupdate')->l('Quantity increment is not valid!'));
    }

    return true;
  }

}
