- Module V5.3.3 compatible with PrestaShop V1.7.x.x & V8.x.x

### Install note:
- If you want to sell sample of virtual products also, add a new custom Hook in /controllers/front/GetFileController.php for downloading virtual product sample:
    * Just before line: *$mimeType = false;* Add this line below PS V1.7.8.8 and for PS V1.7.8.8 and above Add before this code - *$this->sendFile($file, $filename);*

          `HOOK::exec('actionSampleProductDownloadBefore', array($info, &$file, &$filename));
          if (!$filename) {
              ?>
              <script type="text/javascript">
                  alert("<?php echo $file ?>");
                  history.back();
              </script>
             <?php
              exit();
          }`

- If you want to use "Minimum quantity for sale" setting in product with sample products, add a new custom Hook in /classes/Cart.php::updateQty() for adding sample quantity less than product minimum quantity:
    * Replace the code -

            if ((int)$quantity < $minimal_quantity) {
                return -1;
            }

     With -

        if ((int)$quantity < $minimal_quantity) {
            $sampleProductHook = Hook::exec('actionSampleProductAddInCart', array('idProduct' => $id_product, 'idAttr' => $id_product_attribute));
            if (!$sampleProductHook) {
                return -1;
            }
        }

  - If you are using Prestashop V1.7.5.0 or above, then make the following change for adding sample less than minimal quantity:
    * Replace the code in `processChangeProductInCart()` in `/controllers/front/CartController.php` -

            if ($qty_to_check < $product->minimal_quantity) {
                $this->errors[] = $this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $product->minimal_quantity),
                    'Shop.Notifications.Error'
                );
                return;
            }


            AND :

            if ($qty_to_check < $combination->minimal_quantity) {
                $this->errors[] = $this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $combination->minimal_quantity),
                    'Shop.Notifications.Error'
                );

                return;
            }

      With -

        if ($qty_to_check < $product->minimal_quantity) {
            $sampleProductHook = Hook::exec('actionSampleProductAddInCart', array('idProduct' => $product->id, 'idAttr' => $this->id_product_attribute));
            if (!$sampleProductHook) {
                $this->errors[] = $this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $product->minimal_quantity),
                    'Shop.Notifications.Error'
                );
                return;
            }
        }

        AND

        if ($qty_to_check < $combination->minimal_quantity) {
            $sampleProductHook = Hook::exec('actionSampleProductAddInCart', array('idProduct' => $product->id, 'idAttr' => $this->id_product_attribute));
            if (!$sampleProductHook) {
                $this->errors[] = $this->trans(
                    'The minimum purchase order quantity for the product %product% is %quantity%.',
                    array('%product%' => $product->name, '%quantity%' => $combination->minimal_quantity),
                    'Shop.Notifications.Error'
                );

                return;
            }
        }
