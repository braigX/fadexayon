<?php
/**
 ** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class EstimatedDeliveryAjaxCartModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $context = Context::getContext();
        if (Tools::isSubmit('ajax')
            && Tools::getValue('token') == Configuration::get('ED_AJAX_TOKEN')
            && Tools::getIsset('modalAction')) {
            $id_product = Tools::getValue('id_product');
            if (Tools::getIsset('id_product_attribute')) {
                $id_product_attribute = Tools::getValue('id_product_attribute');
            } else {
                $id_product_attribute = 0;
            }
            $ed = new EstimatedDelivery();
            $params = [
                'cart' => Context::getContext()->cart,
            ];
            $return = $ed->displayEDsOnAjaxCartModal($params);
            echo json_encode($return);
        }
        // Update selected calendar date
        if (Tools::getIsset('customDate')) {
            $selectedDate = Tools::getValue('selectedDate');
            if (isset($selectedDate) && !empty($selectedDate)) {
                //                print_r([$this->context->language->date_format_lite, $selectedDate]);
                $dateFormat = str_replace('/', '-', $this->context->language->date_format_lite);
                $dateTime = DateTime::createFromFormat($dateFormat, $selectedDate);
                if ($dateTime instanceof DateTime) {
                    // Successfully parsed, format it to the desired output format
                    $updatedSelectedDate = $dateTime->format('Y-m-d');
                    //                    echo $selectedDate . ' - ' . $updatedSelectedDate . '<br>';

                    // Assuming $context->cookie is an instance of Cookie class
                    $context->cookie->__set('ed_calendar_date', $updatedSelectedDate);
                    $context->cookie->write();

                    // Backup method just in case the cookie gets deleted before the order validation
                    EDTools::addPendingCalendarDate($this->context->cart->id, $updatedSelectedDate);

                    echo json_encode(
                        [
                            'date' => $updatedSelectedDate,
                            'formatted_date' => $dateTime->format($dateFormat),
                        ]
                    );
                } else {
                    // Failed to parse the date
                    echo json_encode(['status' => 'error', 'msg' => $this->l('Wrong Date Format')]);
                }
            }
        }
        exit;
    }
}
