<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class ProductController extends ProductControllerCore
{
    public function setProduct(ProductCore $product)
    {
        $this->product = $product;
    }

    public function initContentForRedisCache()
    {
        if (!$this->errors) {
            if (Pack::isPack((int) $this->product->id)
                && !Pack::isInStock((int) $this->product->id, $this->product->minimal_quantity, $this->context->cart)
            ) {
                $this->product->quantity = 0;
            }

            $this->product->description = $this->transformDescriptionWithImg($this->product->description);

            $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);
            $productPrice = 0;
            $productPriceWithoutReduction = 0;

            if (!$priceDisplay || $priceDisplay == 2) {
                $productPrice = $this->product->getPrice(true, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(false, null);
            } elseif ($priceDisplay == 1) {
                $productPrice = $this->product->getPrice(false, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(true, null);
            }

            $pictures = [];
            $text_fields = [];

            if ($this->product->customizable) {
                $files = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_FILE, true);

                foreach ($files as $file) {
                    $pictures['pictures_' . $this->product->id . '_' . $file['index']] = $file['value'];
                }

                $texts = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_TEXTFIELD, true);

                foreach ($texts as $text_field) {
                    $text_fields['textFields_' . $this->product->id . '_' . $text_field['index']] = str_replace('<br />', "\n", $text_field['value']);
                }
            }

            $this->context->smarty->assign([
                'pictures' => $pictures,
                'textFields' => $text_fields, ]);

            $this->product->customization_required = false;
            $customization_fields = $this->product->customizable ? $this->product->getCustomizationFields($this->context->language->id) : false;

            if (is_array($customization_fields)) {
                foreach ($customization_fields as &$customization_field) {
                    if ($customization_field['type'] == Product::CUSTOMIZE_FILE) {
                        $customization_field['key'] = 'pictures_' . $this->product->id . '_' . $customization_field['id_customization_field'];
                    } elseif ($customization_field['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                        $customization_field['key'] = 'textFields_' . $this->product->id . '_' . $customization_field['id_customization_field'];
                    }
                }
                unset($customization_field);
            }

            // Assign template vars related to the category + execute hooks related to the category
            $this->assignCategory();
            // Assign template vars related to the price and tax
            $this->assignPriceAndTax();

            // Assign attributes combinations to the template
            $this->assignAttributesCombinations();

            // Pack management
            $pack_items = Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : [];

            $assembler = new ProductAssembler($this->context);
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->getTranslator()
            );
            $presentationSettings = $this->getProductPresentationSettings();

            $presentedPackItems = [];

            foreach ($pack_items as $item) {
                $presentedPackItems[] = $presenter->present(
                    $this->getProductPresentationSettings(),
                    $assembler->assembleProduct($item),
                    $this->context->language
                );
            }

            $this->context->smarty->assign('packItems', $presentedPackItems);
            $this->context->smarty->assign('noPackPrice', $this->product->getNoPackPrice());
            $this->context->smarty->assign('displayPackPrice', ($pack_items && $productPrice < $this->product->getNoPackPrice()) ? true : false);
            $this->context->smarty->assign('priceDisplay', $priceDisplay);
            $this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));

            $accessories = $this->product->getAccessories($this->context->language->id);

            if (is_array($accessories)) {
                foreach ($accessories as &$accessory) {
                    $accessory = $presenter->present(
                        $presentationSettings,
                        Product::getProductProperties($this->context->language->id, $accessory, $this->context),
                        $this->context->language
                    );
                }
                unset($accessory);
            }

            if ($this->product->customizable) {
                $customization_datas = $this->context->cart->getProductCustomization($this->product->id, null, true);
            }

            $product_for_template = $this->getTemplateVarProduct();
            // var_dump($product_for_template['add_to_cart_url']);exit;

            // hooks maybe provide errors
            try {
                $filteredProduct = Hook::exec(
                    'filterProductContent',
                    ['object' => $product_for_template],
                    null,
                    false,
                    true,
                    false,
                    null,
                    true
                );
            } catch (Throwable $e) {
            }

            if (!empty($filteredProduct['object'])) {
                $product_for_template = $filteredProduct['object'];
            }

            $productManufacturer = new Manufacturer((int) $this->product->id_manufacturer, $this->context->language->id);

            $manufacturerImageUrl = $this->context->link->getManufacturerImageLink($productManufacturer->id);
            $undefinedImage = $this->context->link->getManufacturerImageLink(null);

            if ($manufacturerImageUrl === $undefinedImage) {
                $manufacturerImageUrl = null;
            }

            $productBrandUrl = $this->context->link->getManufacturerLink($productManufacturer->id);

            $this->context->smarty->assign([
                'priceDisplay' => $priceDisplay,
                'productPriceWithoutReduction' => $productPriceWithoutReduction,
                'customizationFields' => $customization_fields,
                'id_customization' => empty($customization_datas) ? null : $customization_datas[0]['id_customization'],
                'accessories' => $accessories,
                'product' => $product_for_template,
                'displayUnitPrice' => (!empty($this->product->unity) && $this->product->unit_price_ratio > 0.000000) ? true : false,
                'product_manufacturer' => $productManufacturer,
                'manufacturer_image_url' => $manufacturerImageUrl,
                'product_brand_url' => $productBrandUrl,
            ]);

            // Assign attribute groups to the template
            $this->assignAttributesGroups($product_for_template);
        }
    }
}
