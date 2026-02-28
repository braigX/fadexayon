<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';

class GeodisDbTranslation
{
    protected $translations_back_en = array(
        array('geodis..*.*.adminmenu.shipmentgridprint',
            'en',
            'Print'
        ),
        array('geodis.Admin.ShipmentController.index.success.shipmentSend',
            'en',
            'Your shipment has been successfully transmitted.'
        ),
        array('geodis.Admin.ShipmentController.index.success.shipmentSubmit',
            'en',
            'Your shipment has been saved.'
        ),
        array('geodis.Admin.ShipmentController.index.error.cannotValidate',
            'en',
            'The transmission of your shipment could not be done.'
        ),
        array('geodis.Admin.Shipment.index.button.back',
            'en',
            'Return'
        ),
       array('geodis.Admin.Shipment.index.warning.noWlAccepted',
            'en',
            'Warning: the service you have chosen does not allow wines and spirits.'
        ),
        array('geodis.Admin.shipment.index.warning.wsDisabled',
            'en',
            'The declaration of wines and spirits is deactivated on this service.'
        ),
        array('geodis.Admin.shipment.index.warning.noCarrierAvailable',
            'en',
            'No service is set for this type of transport. Please set at least one service in the
            "Back-office Configuration" tab.'
        ),
        array('geodis.Admin.shipment.index.warning.noGroupCarrierAvailable',
            'en',
            'This type of transport is not set up. Please make the settings in the
            "Back-office Configuration" tab.'
        ),
        array('geodis.Admin.Shipment.index.createTitle',
            'en',
            'Create a shipment for the order %s '
        ),
        array('geodis.Admin.Shipment.index.removalDate',
        'en',
        'Date of departure'
        ),
        array('geodis.Admin.Shipment.index.carrier',
            'en',
            'Carrier'
        ),
        array('geodis.Admin.Shipment.index.packageReference',
            'en',
            'Parcel reference : #'
        ),
        array('geodis.Admin.Shipment.index.placeholder.height',
            'en',
            'Height (cm)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.width',
            'en',
            'Width (cm)'
        ),
        array('geodis.Admin.Removal.index.prestationAccount',
            'en',
            'Service/Account'
        ),
        array('geodis.Admin.index.warning.noCarrierAvailable',
            'en',
            'No service is registered for the selected type of transport. Go to the "Back-Office
            Configuration" tab to set up the service.'
        ),
        array('geodis.Admin.index.warning.noGroupCarrierAvailable',
            'en',
            'No type of transport is  registered. Go to the "Back-Office Configuration" tab to
            configure transport types.'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.statusLabel',
            'en',
            'Status'
        ),
        array('geodis.Admin.index.warning.wsDisabled',
            'en',
            'The registration of wines and spirits is not allowed on this service. Please
            contact your GEODIS sales representative.'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.departureDate',
            'en',
            'Date of departure'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.isLabelPrinted',
            'en',
            'Printed labels'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.isTransmitted',
            'en',
            'transmitted'
        ),
        array('geodis.Admin.GeneralConfiguration.index.purgeDelay.label',
            'en',
            'Log retention time (in days)'
        ),
        array('geodis.Admin.GeneralConfiguration.index.purgeDelay.desc',
            'en',
            'In order to limit the space used by the module logs, we propose to automatically
            purge the logs.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.carrierLogoWidth.label',
            'en',
            'Size of the logos displayed on your forehead'
        ),
        array('geodis.Admin.GeneralConfiguration.index.carrierLogoWidth.desc',
            'en',
            'This setting is necessary when you want to change the logos of the Front part,
            in order to resize your images optimally.'
        ),
        array('geodis.Admin.Shipment.index.wl.cancel.label',
            'en',
            'Cancel'
        ),
        array('geodis.*.*.menu.order',
            'en',
            'My orders'
        ),
        array('geodis.*.*.menu.removal',
            'en',
            'My Collections'
        ),
        array('geodis.*.*.menu.log',
            'en',
            'Application Logs'
        ),
        array('geodis.Admin.GeneralConfiguration.index.ignoreOrderStates.label',
            'en',
            'Statuses of the orders for which the modification of the states is no longer possible'
        ),
        array('geodis.Admin.GeneralConfiguration.index.ignoreOrderStates.desc',
            'en',
            'Choose the statuses for which the change of the order state can no longer be made
            following transmission to the carrier.'
        ),
        array('geodis.Admin.Index.mainTitle',
            'en',
            'Integration of GEODIS delivery services into my online store.'
        ),
        array('geodis.Admin.Index.mainHead',
            'en',
            'The GEODIS module allows you to integrate easily our delivery services on your
            online store. Depending on the destination (France or Europe), you can offer to
            your customers different delivery services : with appointment or without appointment,
            standard or express.<br>How does it work?'
        ),
        array('geodis.Admin.Index.subtitle1',
            'en',
            '1. Synchronize your GEODIS customer account with your Prestashop environment'
        ),
        array('geodis.Admin.Index.subcontent1',
            'en',
            '<ul><li>If you already have a customer account, log in to your GEODIS space to
            retrieve your Prestashop login credentials from the tab<b>My Information>My Parameters>
            API Keys</b>.</li><li>Go directly to<b> My Account</b>tab of your GEODIS module
            to fill in your credentials.</li><li> Start synchronization.</li><li>If you are
            not a customer, contact our sales team to obtain a customer account.<a href="mailto:dpm@geodis.com">
            Contact us.</a></ li></ul>'
        ),
        array('geodis.Admin.Index.subtitle2',
            'en',
            '2. Set up your delivery offer'
        ),
        array('geodis.Admin.Index.subcontent2',
            'en',
            '<ul><li>Once your account is synchronized, go to the <b>Back-Office Configuration</b>
            tab to set up the delivery services you want to offer to your customers.</li>
            <li>GEODIS provides the description of each of the services</li>< li>You can also
            choose to customize your carriers in the tab<b>Front-Office Configuration</b>.</li></ul>'
        ),
        array('geodis.Admin.Index.subtitle3',
            'en',
            '3. Manage your shipments to France and Europe'
        ),
        array('geodis.Admin.ConfigurationBack.OrderStateSelect.non',
            'en',
            'No state selected'
        ),
        array('geodis.Admin.ConfigurationBack.OrderStateSelect.none',
            'en',
            'No state selected'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.enabledFreeShipping.label',
            'en',
            'Enable Free Shipping'
        ),
        array('geodis.Admin.Index.subcontent3',
            'en',
            '<ul><li>You receive and process the orders placed from your online store in
            your back office <b>My Orders</b> tab of your module.</li><li>Print your labels
            and your driver\'s manifest.</li><li>Validate your orders in order to transmit
            them to your local GEODIS agency.</li><li>The shipping statuses automatically
            updates in your back office.</li><li> You can also make collection requests
            from the <b>My Collections<b>tab.</li></ul>'
        ),
        array('geodis.Admin.Shipment.index.submit.label',
            'en',
            'Save'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.enabledFreeShipping.desc',
            'en',
            'If you wish to set up free shipping from a certain amount, please check the box
            below and fill out the reference amount.'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.freeShipping.label',
            'en',
            'Free shipping, from'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.label',
            'en',
            'Include additional charges'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.desc',
            'en',
            'If you have additional charges (handling fees for example), by checking this box,
            they will also be free.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidNumberOfDays',
            'en',
            'The transport time must be an integer.'
        ),
        array('geodis.Admin.ShipmentController.index.warning.resetedFiscalCode',
            'en',
            'Tax codes that are not DAA-type have been reset.'
        ),
        array('geodis.Admin.ShipmentController.index.groupCarrier',
            'en',
            'Type of transport'
        ),
        array('geodis.Admin.shipment.index.warning.noLabelsAvailable',
            'en',
            'We did not manage to generate the label corresponding to your entry. 
            Please contact your GEODIS support.'
        ),
        array('geodis.Admin.Shipment.submit.error.log.%1$s.%2$s',
            'en',
            'The following error was encountered during transmission : %1$s %2$s.'
        ),
        array('geodis.Admin.Shipment.index.wineAndLiquor',
            'en',
            'Wine and spirits'
        ),
        array('geodis.Admin.Shipment.index.wl.submit',
            'en',
            'Save'
        ),
        array('geodis.Admin.Shipment.index.delivery.print',
            'en',
            'Print labels'
        ),
        array('geodis.Admin.Shipment.index.error.fillRemovalDate',
            'en',
            'Please fill in the date of departure.'
        ),
        array('geodis.Admin.ConfigurationBack.tab.general',
            'en',
            'General configuration'
        ),
        array('geodis.Admin.ConfigurationBack.tab.standard',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.Admin.ConfigurationBack.tab.express',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.Admin.ConfigurationBack.tab.ondemand',
            'en',
            'Delivery on appointment'
        ),
        array('geodis.Admin.ShipmentController.index.carrier',
            'en',
            'Carrier'
        ),
        array('geodis.Admin.Shipment.submit.error',
            'en',
            'An error was encountered during transmission. Please consult the "My logs"
            tab for more information.'
        ),
        array('geodis.Admin.Shipment.submit.success',
            'en',
            'Transmission completed successfully.'
        ),
        array('geodis.Admin.ConfigurationFront.tab.ondemand',
            'en',
            'Delivery on appointment'
        ),
        array('geodis.Admin.ConfigurationFront.tab.relay',
            'en',
            'Delivery in a pick-up point'
        ),
        array('geodis.Admin.ConfigurationFront.legend.general',
            'en',
            'General configuration'
        ),
        array('geodis.*.*.carrier.default.name.ondemand',
            'en',
            'Delivery on appointment'
        ),
        array('geodis.*.*.carrier.default.delay.ondemand',
            'en',
            'Preparation time'
        ),
        array('geodis.*.*.carrier.default.name.standard',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.*.*.carrier.default.delay.standard',
            'en',
            'Preparation time'
        ),
        array('geodis.Admin.LogGrid.massDelete.success.%s',
            'en',
            '%s line (s) successfully deleted .'
        ),
        array('geodis.Admin.ConfigurationBack.tab.relay',
            'en',
            'Delivery at a point of withdrawal'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.fieldset.prestation',
            'en',
            'Service'
        ),
        array('geodis.Admin.ShipmentController.index.updateShippingLabel',
            'en',
            'Edit the shipment'
        ),
        array('geodis.Admin.ConfigurationFront.tab.general',
            'en',
            'General configuration'
        ),
        array('geodis.Admin.ConfigurationFront.tab.standard',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.Admin.ConfigurationFront.tab.express',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.Admin.Removal.index.error.invalidBoxFormat',
            'en',
            'The format of the number of packages is not valid.'
        ),
        array('geodis.Admin.Removal.index.error.invalidPalletFormat',
            'en',
            'The format of the number of palettes is invalid.'
        ),
        array('geodis.Admin.Removal.index.error.invalidWeightFormat',
            'en',
            'The weight format is not valid.'
        ),
        array('geodis.Admin.Removal.index.error.invalidVolumeFormat',
            'en',
            'The volume format is invalid.'
        ),
        array('geodis.Admin.removal.index.error.notWlPrestation',
            'en',
            'Wines and spirits are not allowed with this service.'
        ),
        array('geodis.Admin.Shipment.index.error.update',
            'en',
            'An error was encountered during the status update.'
        ),
        array('geodis.Admin.Information.index.tab.synchonisation',
            'en',
            'Synchronize my information'
        ),
        array('geodis.Admin.LogGrid.massDelete.error.noItemSelected',
            'en',
            'No lines selected for deletion.'
        ),
        array('geodis.Admin.Removal.index.button.print',
            'en',
            'Print'
        ),
        array('geodis.Admin.salesShipment.view.trackingNumber',
            'en',
            'Tracking number'
        ),
        array('geodis.Admin.salesShipment.view.trackingUrl',
            'en',
            'Tracking URL'
        ),
        array('geodis.Admin.Information.index.tab.account',
            'en',
            'My account'
        ),
        array('geodis.Admin.Information.index.tab.shipmentPrestation',
            'en',
            'My delivery services'
        ),
        array('geodis.Admin.GeneralConfiguration.index.tab.removalPrestation',
            'en',
            'My collection services'
        ),
        array('geodis.Admin.Address.index.site.label',
            'en',
            'My site information'
        ),
        array('geodis.Admin.Removal.index.tab.history',
            'en',
            'History of collection requests'
        ),
        array('geodis.Admin.salesShipment.view.title.%s',
            'en',
            '%1$s shipping information.'
        ),
        array('geodis.Admin.ServiceSynchronize.shipment.statusLabel.waitingTransmission',
            'en',
            'To be handed over to the carrier'
        ),
        array('geodis.Admin.salesShipment.view.trackingLink',
            'en',
            'Click here'
        ),
        array('geodis.Admin.salesShipment.view.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.salesShipment.view.noInformationsAvailable',
            'en',
            'No information available at the moment.'
        ),
        array('geodis.Admin.salesShipment.view.noWs',
            'en',
            'The WebService is not accessible for transmission of the shipment.'
        ),
        array('geodis.Admin.salesShipment.view.incident.%s',
            'en',
            'An error was encountered on your #%1$s shipment. Please check the status of
            this shipment for more information.'
        ),
        array('geodis.Admin.Shipment.index.volume.label',
            'en',
            'Volume'
        ),
        array('geodis.Admin.shipment.index.error.Prestation',
            'en',
            'The characteristics of your shipment are not compatible with the selected service. 
            Please choose a suitable service or get closer to your GEODIS sales representative.'
        ),
        array('geodis.Admin.ServiceSynchronize.shipment.statusLabel.transmitted',
            'en',
            'Waiting for the picking-up'
        ),
        array('geodis.Admin.GeneralConfiguration.process.error.generic.%1$s.%2$s',
            'en',
            'The %1$s field does not conform to the %2$s rule.'
        ),
        array('geodis.*.*.menu.module',
            'en',
            'Presentation'
        ),
        array('geodis.*.*.menu.information',
            'en',
            'My Account'
        ),
        array('geodis.*.*.menu.back',
            'en',
            'Back-Office Configuration'
        ),
        array('geodis.*.*.menu.front',
            'en',
            'Front-Office Configuration'
        ),
        array('geodis.*.*.menu.address',
            'en',
            'Collection Addresses'
        ),
        array('geodis.*.*.adminMenu.shipments',
            'en',
            'My Shipments'
        ),
        array('geodis.*.*.adminMenu.log',
            'en',
            'Logs'
        ),
        array('geodis.*.*.adminMenu.removal',
            'en',
            'Collection Requests'
        ),
        array('geodis.Admin.GeneralConfiguration.index.form.legend',
            'en',
            'General Configuration'
        ),
        array('geodis.Admin.GeneralConfiguration.index.active.label',
            'en',
            'Active'
        ),
        array('geodis.Admin.GeneralConfiguration.index.active.desc',
            'en',
            'Use GEODIS carriers'
        ),
        array('geodis.Admin.GeneralConfiguration.index.partialShippingState.label',
            'en',
            'State of the order when a partial shipment has been sent to the carrier.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.partialShippingState.desc',
            'en',
            'Choose the status to be displayed when your order has been partially
            transmitted to the carrier.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.completeShippingState.label',
            'en',
            'State of the order when a complete shipment has been sent to the carrier.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.completeShippingState.desc',
            'en',
            'Choose the status to be displayed when your order has been transmitted in
            totatlity to the carrier.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.submit.title',
            'en',
            'Save'
        ),
        array('geodis.Admin.GeneralConfiguration.index.departureDateDelay.label',
            'en',
            'Deadline for the departure date'
        ),
        array('geodis.Admin.GeneralConfiguration.index.departureDateDelay.desc',
            'en',
            'Time in days to estimate the date of departure.'
        ),
        array('geodis.Admin.GeneralConfiguration.post.message.success',
            'en',
            'Saved configuration.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.useWhiteLabel.label',
            'en',
            'Customize the carriers.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.useWhiteLabel.desc',
            'en',
            'Edit the description and/or the logo of GEODIS carriers.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.availableOrderStates.label',
            'en',
            'States of the orders for which the editing of the shipment is possible.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.availableOrderStates.desc',
            'en',
            'Choose the statuses allowing the creation of a shipment.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.api.login.label',
            'en',
            'Sign in to your GEODIS account'
        ),
        array('geodis.Admin.GeneralConfiguration.index.api.secret.label',
            'en',
            'Secret key of your GEODIS account to use the module (see the User Guide of the
            GEODIS module).'
        ),
        array('geodis.Admin.GeneralConfiguration.index.mapEnabled.label',
            'en',
            'Show GoogleMaps map'
        ),
        array('geodis.Admin.GeneralConfiguration.index.mapEnabled.desc',
            'en',
            'Option'
        ),
        array('geodis.Admin.GeneralConfiguration.index.loadGoogleMapJs.label',
            'en',
            'Load Google Map Script'
        ),
        array('geodis.Admin.GeneralConfiguration.index.loadGoogleMapJs.desc',
            'en',
            'If the script is already loaded, you must disable this option.'
        ),
        array('geodis.*.*.switch.enabled',
            'en',
            'Yes'
        ),
        array('geodis.*.*.switch.disabled',
            'en',
            'No'
        ),
        array('geodis.Admin.OrdersGrid.index.action.createShipment',
            'en',
            'Create the shipment'
        ),
        array('geodis.Admin.Shipment.index.title',
            'en',
            'List of orders'
        ),
        array('geodis.Admin.OrdersGrid.index.action.editShipment',
            'en',
            'See the shipment'
        ),
        array('geodis.Admin.OrdersGrid.index.action.viewOrders',
            'en',
            'Return to orders'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.title',
            'en',
            'Your shipments'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.incident',
            'en',
            'Incident'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.transmitted',
            'en',
            'Transmitted'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.show',
            'en',
            'Display'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.hide',
            'en',
            'Hide'
        ),
        array('geodis.Admin.OrdersGrid.index.headerTitle',
            'en',
            'Shipments'
        ),
        array('geodis.Admin.LogGrid.index.headerTitle',
            'en',
            'Journals'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.shipmentList',
            'en',
            'Your shipments'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.customerName',
            'en',
            'Customer'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.customerEmail',
            'en',
            'E-mail'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.shippingList',
            'en',
            'Shipment'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderStatus',
            'en',
            'Status'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderDate',
            'en',
            'Date of dispatch'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderTotal',
            'en',
            'Total of the shipment'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapApiKey.label',
            'en',
            'Google Map API Key'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapApiKey.desc',
            'en',
            'Create a new key here: https://cloud.google.com/maps-platform/'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapClient.label',
            'en',
            'Google Map Client'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapClient.desc',
            'en',
            'If you are not using an API key but a client-id, fill out the form and leave the
            Google Map API key blank.'
        ),
        array('geodis.GeodisServiceConfiguration.OrderStateSelect.none',
            'en',
            'None'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.carrier',
            'en',
            'Carrier'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.actions',
            'en',
            'Choice'
        ),
        array('geodis.Admin.AdminGeodisOrdersGrid.index.grid.link.print',
            'en',
            'Waiting for printing'
        ),
        array('geodis.Admin.AdminGeodisOrdersGrid.index.grid.link.transmit',
            'en',
            'Waiting for validation'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidIntWLField',
            'en',
            'The number must be an integer.'
        ),
        array('geodis.Admin.ShipmentController.index.error.incompatibleFicalCode',
            'en',
            'A DAA tax code is already selected for another item. You can not choose another
            tax code in the same shipment.'
        ),
        array('geodis.Admin.Shipment.index.wl.label',
            'en',
            'Save'
        ),
        array('geodis.Admin.ShipmentController.index.warning.daaUniq',
            'en',
            'By selecting the DAA tax code, all other tax codes will be reset.'
        ),
        array('geodis.Admin.ShipmentController.index.error.missingWLField',
            'en',
            'Please enter all required fields.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidOrder',
            'en',
            'Technical error: invalid command ID.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidShipment',
            'en',
            'Technical error: Invalid shipment ID.'
        ),
        array('geodis.Admin.ShipmentController.index.error.pastDate',
            'en',
            'Choose a later departure date.'
        ),
        array('geodis.Admin.ShipmentController.index.error.missingDate',
            'en',
            'Please enter a departure date.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidDate',
            'en',
            'Invalid departure date.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidWeight',
            'en',
            'The entered weight is not valid.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidVolume',
            'en',
            'The entered volume is not valid.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidHeight',
            'en',
            'The entered length is not valid.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidDepth',
            'en',
            'The entered width is not valid.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidWidth',
            'en',
            'The entered height is not valid.'
        ),
        array('geodis.Admin.ShipmentController.index.error.noProductsAvailable',
            'en',
            'The labels have been created, the shipment is no longer editable.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unavailableWSDate',
            'en',
            'The transmission of your shipment could not be done. Please retry later.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unvailableWSPrint',
            'en',
            'Labels can not be printed. Please retry later.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unvailableWSSendShipment',
            'en',
            'The transmission of your shipment could not be done. Please retry later.'
        ),
        array('geodis.Admin.ShipmentController.index.success.labelsprinted',
            'en',
            'The labels have been printed successfully. You can now send your shipment.'
        ),
        array('geodis.Admin.Shipment.index.placeholder.depth',
            'en',
            'Length (cm)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.weight',
            'en',
            'Weight (kg)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.volume',
            'en',
            'Volume (m3)'
        ),
        array('geodis.Admin.Shipment.index.quantity',
            'en',
            'Quantity'
        ),
        array('geodis.Admin.Shipment.index.reference',
            'en',
            'Product reference'
        ),
        array('geodis.Admin.Shipment.index.combinationReference',
            'en',
            'References of the shipment'
        ),
        array('geodis.Admin.Shipment.index.packageType.box',
            'en',
            'Parcel'
        ),
        array('geodis.Admin.Shipment.index.packageType.pallet',
            'en',
            'Palette'
        ),
        array('geodis.Admin.Shipment.index.name',
            'en',
            'Item name'
        ),
        array('geodis.Admin.Shipment.index.vs',
            'en',
            'Wine & Spirits'
        ),
        array('geodis.Admin.Shipment.index.customs',
            'en',
            'Tax Code'
        ),
        array('geodis.Admin.Shipment.index.addPackage',
            'en',
            'Add a new package'
        ),
        array('geodis.Admin.Shipment.index.cancel',
            'en',
            'Return'
        ),
        array('geodis.Admin.Shipment.index.submit',
            'en',
            'Save my shipment'
        ),
        array('geodis.Admin.Shipment.index.submitandnew',
            'en',
            'Save and create a new shipment'
        ),
        array('geodis.Admin.Shipment.index.print',
            'en',
            'Print labels'
        ),
        array('geodis.Admin.Shipment.index.send.label',
            'en',
            'Submit my shipment'
        ),
        array('geodis.Admin.Shipment.index.send.confirm',
            'en',
            'Are you sure ? By validating, you will not be able to edit the transmission
            sent to GEODIS.'
        ),
        array('geodis.Admin.Shipment.index.send.infobulle.print',
            'en',
            'Please print your labels before submitting your shipment.'
        ),
        array('geodis.Admin.Shipment.index.send.infobulle.submit',
            'en',
            'Please save your shipment before sending it.'
        ),
        array('geodis.Admin.Shipment.index.error.missingCarrier',
            'en',
            'Please select a carrier.'
        ),
        array('geodis.Admin.Shipment.index.error.missingDimensions',
            'en',
            'Please enter all dimensions of the shipment.'
        ),
        array('geodis.Admin.Shipment.index.error.wrongValueDimensions',
            'en',
            'Please enter a number for the dimensions.'
        ),
        array('geodis.Admin.Shipment.index.error.noRowsSelected',
            'en',
            'No lines selected for this package.'
        ),
        array('geodis.Admin.Shipment.index.error.exceededQuantity',
            'en',
            'You have selected more items than the quantity available.'
        ),
        array('geodis.Admin.Shipment.index.error.taxCodeMissing',
            'en',
            'Please select a tax code.'
        ),
        array('geodis.Admin.Shipment.index.button.removePackage',
            'en',
            'Remove the package'
        ),
        array('geodis.Admin.Shipment.index.success',
            'en',
            'Shipment and packages created successfully.'
        ),
        array('geodis.Admin.Shipment.index.error',
            'en',
            'Error while creating the submission.'
        ),
        array('geodis.Admin.Shipment.index.error.wrongRemovalDate',
            'en',
            'Please select a valid departure date.'
        ),
        array('geodis.*.*.error.notSyncronized',
            'en',
            'Your account is not synced yet. Go to the "My Account" tab to start a synchronization.'
        ),
        array('geodis.*.*.carrier.defaultName',
            'en',
            'GEODIS carrier'
        ),
        array('geodis.Admin.ConfigurationBack.AjaxSave.success',
            'en',
            'Saved configuration.'
        ),
        array('geodis.Admin.ConfigurationBack.AjaxSave.notice.carrierDisabled.%s',
            'en',
            'The %s carrier was disassociated from the module and is now disabled.'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.account.label',
            'en',
            'Agency code / account'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.preparationDelay.label',
            'en',
            'Preparation time (days)'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.prestation.label',
            'en',
            'Service'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.label',
            'en',
            'Carrier'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.none',
            'en',
            'None'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.new',
            'en',
            'Create a new carrier'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.label',
            'en',
            'Price'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.active.label',
            'en',
            'Activate'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.priceImpact.label',
            'en',
            'Price of the service'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.remove',
            'en',
            'Remove'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.disable',
            'en',
            'Deactivate'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.addCarrier',
            'en',
            'Add a service'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.enable',
            'en',
            'Activate'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.save',
            'en',
            'Save this configuration'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.group.name.%s',
            'en',
            'Type of transport : %s'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.nonUniqCarrierReference.%s',
            'en',
            'The service associated with this agency/account code pair is selected twice.
            Please change one of the two settings %s.'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.price.%s',
            'en',
            '%s is not a valid price.'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.priceImpact.%s',
            'en',
            '%s is not a valid price.'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.depotage',
            'en',
            'Depalletizing'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.miseLieuUtil',
            'en',
            'Delivery in the consignee\'s room'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.livEtage',
            'en',
            'Delivery on the consignee\'s floor'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.depotage',
            'en',
            'Opening of the pallet, picking up the pallet and the outer packaging.'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.miseLieuUtil',
            'en',
            'Delivery in the room that I have indicated to the driver (floor, reserve, basement, ...).'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.livEtage',
            'en',
            'Delivery of items that can be handled by the driver alone. Presence of an
            elevator beyond 2 floors.'
        ),
        array('geodis.Admin.ShipmentController.index.error.removeShipmentForbidden',
            'en',
            'You do not have permission to delete a completed shipment.'
        ),
        array('geodis.Admin.ShipmentController.delete.success',
            'en',
            'Sending successfully deleted.'
        ),
        array('geodis.Admin.Shipment.delete.back',
            'en',
            'Return'
        ),
        array('geodis.Admin.ConfigurationFront.index.form.legend',
            'en',
            'Front-Office Configuration'
        ),
        array('geodis.Admin.ConfigurationFront.index.submit.title',
            'en',
            'Save'
        ),
        array('geodis.Admin.ConfigurationFront.index.useWhiteLabel.label',
            'en',
            'Customize carriers'
        ),
        array('geodis.Admin.ConfigurationFront.index.useWhiteLabel.desc',
            'en',
            'Edit the description and/or the logo of GEODIS carriers.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrier.title.%s',
            'en',
            'Carrier %s'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierName.label',
            'en',
            'Name of the transport type'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierName.desc',
            'en',
            'Name of the delivery mode displayed to the customers of the site.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierDescription.label',
            'en',
            'Description of the type of transport'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierDescription.desc',
            'en',
            'Description of the transport service for the customers of the site.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierActive.label',
            'en',
            'Active'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierActive.desc',
            'en',
            'Parameter to enable or disable this type of transport on the customer site.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierLogo.label',
            'en',
            'Logo'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierLogo.desc',
            'en',
            'Parameter to enable or disable the display of the logo on the customer site.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.success',
            'en',
            'Saved configuration.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.uploadMaxFileSize',
            'en',
            'The file is too large. Try a smaller image or change the upload_max_filesize
            configuration in your php.ini.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.maxFileSize',
            'en',
            'The file is too large. Try a smaller image or change the MAX_FILE_SIZE configuration.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.noTmpDir',
            'en',
            'The file could not be downloaded. The temporary folder is missing.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.nonWritableDir',
            'en',
            'The file could not be downloaded. The temporary folder is not writable.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.unknow',
            'en',
            'The file could not be downloaded.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.invalidExtension',
            'en',
            'The file extension is not valid. Only jpg, gif and png files are allowed.'
        ),
        array('geodis.*.*.carrier.default.name.rdv',
            'en',
            'Delivery on appointment'
        ),
        array('geodis.*.*.carrier.default.delay.rdv',
            'en',
            'I am delivered on the date that suits me.'
        ),
        array('geodis.*.*.carrier.default.name.classic',
            'en',
            'Delivery without an appointment'
        ),
        array('geodis.*.*.carrier.default.delay.classic',
            'en',
            'I get delivered as soon as possible.'
        ),
        array('geodis.*.*.carrier.default.name.relay',
            'en',
            'Delivery in a pick-up point'
        ),
        array('geodis.*.*.carrier.default.delay.relay',
            'en',
            'I am delivered in a pick-up point near to my home.'
        ),
        array('geodis.*.*.carrier.default.name.bulky',
            'en',
            'Delivery of large packages'
        ),
        array('geodis.*.*.carrier.default.delay.bulky',
            'en',
            'Delivery adapted to large packages.'
        ),
        array('geodis.*.*.carrier.default.name.express',
            'en',
            'Delivery of your urgent packages'
        ),
        array('geodis.*.*.carrier.default.delay.express',
            'en',
            'For all my urgent shipments, GEODIS puts at my disposal a dedicated delivery solutions.'
        ),
        array('geodis.Admin.Information.index.account.label',
            'en',
            'My Account'
        ),
        array('geodis.Admin.Information.index.explanation',
            'en',
            'This information comes from your customer card. If they are incorrect or
            if you wish to adjust your services, please contact your local agency.'
        ),
        array('geodis.Admin.Information.index.prestation.label',
            'en',
            'My services set up for the preparation of my shipments from one of my sites.'
        ),
        array('geodis.Admin.Information.index.removalRequest.label',
            'en',
            'My services set up for my on-site collection requests.'
        ),
        array('geodis.Admin.LogGrid.index.grid.id',
            'en',
            'Login'
        ),
        array('geodis.Admin.LogGrid.index.grid.message',
            'en',
            'Message'
        ),
        array('geodis.Admin.LogGrid.index.grid.isError',
            'en',
            'Is in error'
        ),
        array('geodis.Admin.LogGrid.index.grid.logDate',
            'en',
            'Date'
        ),
        array('geodis.Admin.Information.index.society',
            'en',
            'Society'
        ),
        array('geodis.Admin.Information.index.address',
            'en',
            'Address'
        ),
        array('geodis.Admin.Information.index.accountNumber.%s',
            'en',
            'Account number : %s'
        ),
        array('geodis.Admin.Information.index.agency.%s',
            'en',
            'Agency : %s'
        ),
        array('geodis.Admin.Information.index.connection.label',
            'en',
            'Sign in to my GEODIS account'
        ),
        array('geodis.Admin.Information.index.connection.login.placeholder',
            'en',
            'Enter your GEODIS ID'
        ),
        array('geodis.Admin.Information.index.connection.token.placeholder',
            'en',
            'Enter your KEY API'
        ),
        array('geodis.Admin.Information.index.connection.button.synchronise',
            'en',
            'Synchronize'
        ),
        array('geodis.Admin.Information.index.connection.button.connect',
            'en',
            'Log in'
        ),
        array('geodis.Admin.Information.index.connection.login.success',
            'en',
            'Verified access'
        ),
        array('geodis.Admin.Information.index.connection.token.info',
            'en',
            'You will find your KEY API on your customer Space, in your account settings, API Key tab.'
        ),
        array('geodis.Admin.Information.index.connection.button.modify',
            'en',
            'Change your connection settings'
        ),
        array('geodis.Admin.Information.connection.error.default',
            'en',
            'Account not found'
        ),
        array('geodis.Admin.AdressConfiguration.index.isDefault',
            'en',
            'Is by default'
        ),
        array('geodis.Admin.AdressConfiguration.index.address1',
            'en',
            'Address 1'
        ),
        array('geodis.Admin.AdressConfiguration.index.address2',
            'en',
            'Address 2'
        ),
        array('geodis.Admin.AdressConfiguration.index.zipCode',
            'en',
            'Postal code'
        ),
        array('geodis.Admin.AdressConfiguration.index.city',
            'en',
            'Postal code'
        ),
        array('geodis.Admin.AddressConfiguration.index.error',
            'en',
            'Collection Site not found in the records of the database.'
        ),
        array('geodis.Admin.Address.index.defaultSite',
            'en',
            'Used by default'
        ),
        array('geodis.Admin.Address.index.setAsDefault',
            'en',
            'Use as default'
        ),
        array('geodis.Admin.Address.index.title.removalSites',
            'en',
            'My Collection sites'
        ),
        array('geodis.Admin.Shipment.index.fiscalCode.label',
            'en',
            'Tax Code'
        ),
        array('geodis.Admin.Shipment.index.nbCol.label',
            'en',
            'Number of packages'
        ),
        array('geodis.Admin.Shipment.index.nbCol.placeholder',
            'en',
            'Number of packages'
        ),
        array('geodis.Admin.Shipment.index.volumeCl.label',
            'en',
            'Capacity (cl)'
        ),
        array('geodis.Admin.Shipment.index.volumeL.label',
            'en',
            'Volume in duties suspended (l)'
        ),
        array('geodis.Admin.Shipment.index.volumeL.placeholder',
            'en',
            'Ex: 0'
        ),
        array('geodis.Admin.Shipment.index.fiscalCodeRef.label',
            'en',
            'Administrative reference code'
        ),
        array('geodis.Admin.Shipment.index.fiscalCodeRef.placeholder',
            'en',
            'Administrative reference code'
        ),
        array('geodis.Admin.Shipment.index.nMvt.label',
            'en',
            'Movement certificate number'
        ),
        array('geodis.Admin.Shipment.index.nMvt.placeholder',
            'en',
            'Movement certificate number'
        ),
        array('geodis.Admin.Shipment.index.shippingDuration.label',
            'en',
            'Transport time in days'
        ),
        array('geodis.Admin.Shipment.index.shippingDuration.placeholder',
            'en',
            'Transport time in days'
        ),
        array('geodis.Admin.Shipment.index.nEa.label',
            'en',
            'Consignee EA number'
        ),
        array('geodis.Admin.Shipment.index.nEa.placeholder',
            'en',
            'Consignee EA number'
        ),
        array('geodis.Admin.Removal.index.form.legend',
            'en',
            'My collection request'
        ),
        array('geodis.Admin.Removal.index.submit.title',
            'en',
            'save'
        ),
        array('geodis.Admin.index.carrier',
            'en',
            'Carrier'
        ),
        array('geodis.Admin.index.groupCarrier',
            'en',
            'Type of transport'
        ),
        array('geodis.Admin.Removal.index.removalAdresses',
            'en',
            'My collection address'
        ),
        array('geodis.Admin.Removal.index.account',
            'en',
            'Account'
        ),
        array('geodis.Admin.Removal.index.prestation',
            'en',
            'Service'
        ),
        array('geodis.Admin.Removal.index.numberOfBox',
            'en',
            'Number of packages'
        ),
        array('geodis.Admin.Removal.index.numberOfPallet',
            'en',
            'Number of pallets'
        ),
        array('geodis.Admin.Removal.index.weight',
            'en',
            'Weight (kg)'
        ),
        array('geodis.Admin.Removal.index.volume',
            'en',
            'Volume (m3)'
        ),
        array('geodis.Admin.Removal.index.observations',
            'en',
            'Observations'
        ),
        array('geodis.Admin.Removal.index.reglementedTransport',
            'en',
            'Transport of regulated materials'
        ),
        array('geodis.Admin.Removal.index.fiscalCode',
            'en',
            'Tax Code'
        ),
        array('geodis.Admin.Removal.index.legalVolume',
            'en',
            'Legal volume'
        ),
        array('geodis.Admin.Removal.index.totalVolume',
            'en',
            'Total volume (l)'
        ),
        array('geodis.Admin.Removal.index.noReglemented',
            'en',
            'I declare that my shipment does not contain hazardous materials or wines and spirits.'
        ),
        array('geodis.Admin.Removal.index.reglemented',
            'en',
            'I declare that my shipment contains wines and spirits.'
        ),
        array('geodis.Admin.Removal.index.removalDateWished',
            'en',
            'Requested date of collection'
        ),
        array('geodis.Admin.Removal.index.table.head.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.Removal.index.table.head.dateAdd',
            'en',
            'Requested the'
        ),
        array('geodis.Admin.Removal.index.table.head.removalDate',
            'en',
            'Date of collection'
        ),
        array('geodis.Admin.Removal.index.table.head.prestation',
            'en',
            'Service'
        ),
        array('geodis.Admin.Removal.index.table.head.removalSite',
            'en',
            'Collection site'
        ),
        array('geodis.Admin.Removal.index.table.head.volume',
            'en',
            'Volume'
        ),
        array('geodis.Admin.Removal.index.table.head.weight',
            'en',
            'Weight'
        ),
        array('geodis.Admin.Removal.index.table.head.nbPallet',
            'en',
            'Number of pallets'
        ),
        array('geodis.Admin.Removal.index.table.head.nbBox',
            'en',
            'Number of boxes'
        ),
        array('geodis.Admin.Removal.index.table.head.action',
            'en',
            'action'
        ),
        array('geodis.Admin.Removal.index.action.button.copy',
            'en',
            'action'
        ),
        array('geodis.Admin.Removal.index.alert.success',
            'en',
            'Registered collection request'
        ),
        array('geodis.Admin.Removal.index.table.row.sent',
            'en',
            'Sent'
        ),
        array('geodis.Admin.Removal.index.table.head.print',
            'en',
            'See the request'
        ),
        array('geodis.Admin.Removal.index.table.action.print',
            'en',
            'Impression'
        ),
        array('geodis.Admin.Removal.index.table.head.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.Removal.index.noRecord',
            'en',
            'No record found'
        ),
        array('geodis.Admin.Removal.index.error.invalidWeight',
            'en',
            'Invalid weight.'
        ),
        array('geodis.Admin.Removal.index.error.invalidVolume',
            'en',
            'Invalid volume.'
        ),
        array('geodis.Admin.Removal.index.error.invalidQuantity',
            'en',
            'You must specify the number of packages or the number of pallets.'
        ),
        array('geodis.Admin.Removal.index.error.invalidAccount',
            'en',
            'Select an account.'
        ),
        array('geodis.Admin.Removal.index.error.invalidPrestation',
            'en',
            'Select a service.'
        ),
        array('geodis.Admin.Removal.index.error.missingTotalVolume',
            'en',
            'Enter the total volume.'
        ),
        array('geodis.Admin.Removal.index.error.invalidTotalVolume',
            'en',
            'Invalid total volume.'
        ),
        array('geodis.Admin.Removal.index.error.pastDate.%s',
            'en',
            'Choose a collection date after the %s.'
        ),
        array('geodis.Admin.Removal.index.error.invalidDate',
            'en',
            'Select a valid collection date.'
        ),
        array('geodis.Admin.Removal.index.table.title',
            'en',
            'Collection requests for the next 30 days'
        ),
        array('geodis.Admin.Information.index.connection.lastSynchronizationDate.%s',
            'en',
            'Last sync date, the %1$s.'
        ),
        array('geodis.Admin.Information.sync.success',
            'en',
            'Synchronization completed.'
        ),
        array('geodis.Admin.ServiceSynchronize.error.alreadySynchronize',
            'en',
            'The information has already been synchronized recently. Please try again in a few hours'
        ),
        array('geodis.Admin.ServiceSynchronize.error.default',
            'en',
            'The synchronization with the WebService encountered problems. Please try again.'
        ),
        array('geodis.Admin.ServiceSynchronize.error.shipment.event.%s',
            'en',
            'An incident is reported on the shipment %1$s. See the Shipment History
            for more information.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.title.%s',
            'en',
            'Shipment #%1$s information'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.history.title',
            'en',
            'Shipment History'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.status.%s',
            'en',
            'Status :%1$s'
        ),
        array('geodis.Admin.ShipmentStatus.table.date',
            'en',
            'Date of dispatch'
        ),
        array('geodis.Admin.ShipmentStatus.table.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.ShipmentStatus.packages.title',
            'en',
            'Package contents'
        ),
        array('geodis.Admin.ShipmentStatus.package.referenceExpedition.%s',
            'en',
            'Package #%1$s'
        ),
        array('geodis.Admin.ShipmentStatus.package.statusLabel.%s',
            'en',
            'Package Status : %1$s'
        ),
        array('geodis.Admin.ShipmentStatus.table.productName',
            'en',
            'Product Name'
        ),
        array('geodis.Admin.ShipmentStatus.table.productReference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.ShipmentStatus.table.productQuantity',
            'en',
            'Quantity'
        ),
        array('geodis.Admin.GeneralConfiguration.index.layout.label',
            'en',
            'Customizing the layout'
        ),
        array('geodis.Admin.GeneralConfiguration.index.layout.desc',
            'en',
            'By default, you have 4 layouts available: layouts / layout-left-column.tpl,
            layouts / layout-right-column.tpl, layouts / layout-both-columns.tpl, layouts /
            layout-full-width.tpl .'
        ),
        array('geodis.Admin.Shipment.index.prestation',
            'en',
            'Service'
        ),
        array('geodis.Admin.Shipment.index.account',
            'en',
            'Account'
        ),
        array('geodis.Admin.Shipment.index.warning.daysOff',
            'en',
            'The date you have chosen is aday off. We have selected the next business
            day for your collection date.'
        ),
        array('geodis.Admin.Removal.index.warning.daysOff',
            'en',
            'The date you have chosen is not available. We have selected the next business
            day for your collection date.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.fiscalCode.label',
            'en',
            'Default tax code'
        ),
        array('geodis.Admin.GeneralConfiguration.index.fiscalCode.desc',
            'en',
            'Select the tax code that you want to use by default when creating a shipment.'
        ),
        array('geodis.Admin.Removal.index.timeSlot.daytime',
            'en',
            'All day'
        ),
        array('geodis.Admin.Removal.index.timeSlot.morning',
            'en',
            'Morning'
        ),
        array('geodis.Admin.Removal.index.timeSlot.afternoon',
            'en',
            'Afternoon'
        ),
        array('geodis.Admin.Removal.index.timeSlot',
            'en',
            'Time slot'
        ),
        array('geodis.Admin.Removal.index.optionNotAvailable',
            'en',
            'Options not available'
        ),
        array('geodis.Admin.ServiceSynchronize.error.shipment.status.update',
            'en',
            'The update of the shipment status encountered an error.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.title.sameOrder',
            'en',
            'Shipments of the same order.'
        ),
        array('geodis.Admin.ShipmentStatus.package.orderedQuantity.%s',
            'en',
            'Quantity : %1$s'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.trackingUrl',
            'en',
            'See on the carrier\'s website.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.trackingUrl.link.name',
            'en',
            'Click here!'
        ),
        array('geodis.Admin.ShipmentStatus.package.quickView',
            'en',
            'Quick overview'
        ),
        array('geodis.Admin.Shipment.index.printLabel',
            'en',
            'Printing labels'
        ),
        array('geodis.Admin.Shipment.index.printDelivery',
            'en',
            'Print driver\'s manifest'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.departureDate',
            'en',
            'Date of departure'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.isLabelPrinted',
            'en',
            'Print labels'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.statusLabel',
            'en',
            'Status'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.actions',
            'en',
            'actions'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.action.print',
            'en',
            'Print '
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.bulkAction.print',
            'en',
            'Print '
        ),
        array('geodis.Admin.shipmentsGridPrint.index.action.printLabel',
            'en',
            'Print '
        ),
        array('geodis.Admin.shipmentsGridPrint.index.headerTitle',
            'en',
            'Unprinted label (s)'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.actions',
            'en',
            'Actions'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.id',
            'en',
            'ID'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.groupCarrier',
            'en',
            'Type of transport'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.carrier',
            'en',
            'Service'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.receptNumber',
            'en',
            'Receipt number'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.order',
            'en',
            'Order'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.action.print',
            'en',
            'Print '
        ),
        array('geodis.Admin.shipmentsGrid.index.webservice.false',
            'en',
            'The GEODIS WebService encountered an error during its call. Please check
            the "Logs" tab for more information.'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.webservice.false',
            'en',
            'The GEODIS WebService encountered an error while calling for printing. 
            Please consult the "Logs" tab for more information.'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.reference',
            'en',
            'Reference'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.departureDate',
            'en',
            'Date of departure'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.iscomplete',
            'en',
            'Complete'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.statusLabel',
            'en',
            'Status'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.actions',
            'en',
            'Actions'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.isTransmitted',
            'en',
            'Transmitted'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.headerTitle',
            'en',
            'Non-transmitted shipment'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.id',
            'en',
            'Login'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.action.transmit',
            'en',
            'Transmit'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.groupCarrier',
            'en',
            'Type of transport'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.carrier',
            'en',
            'Service'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.status',
            'en',
            'Status'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.receptNumber',
            'en',
            'Receipt number'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.order',
            'en',
            'Order'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.confirm.transmit',
            'en',
            'Are you sure ? By validating, you will not be able to edit the transmission
            sent to GEODIS.'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.webservice.false',
            'en',
            'The GEODIS WebService encountered an error during its call. Please check the
            "Logs" tab for more information.'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.action.TransmisLabel',
            'en',
            'Submit my shipment'
        ),
        array('geodis.Admin.PackageLabel.print.error.cannotPrint',
            'en',
            'Error while printing the label (s).'
        ),
        array('geodis.Admin.DeleveryLabel.print.error.cannotPrint',
            'en',
            'Error while printing the label (s).'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.label',
            'en',
            'Print on a thermal printer.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.desc',
            'en',
            'The printing of the labels can be done in PDF.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.install',
            'en',
            'Please install the GEODIS printer module first.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.port.label',
            'en',
            'GEODIS printer module port'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.port.desc',
            'en',
            'GEODIS printer module connection port (3000 by default)'
        ),
        array('geodis.Admin.shipment.index.thermalPrinting.error',
            'en',
            'An error was encountered during printing. Please check your configuration.'
        ),
        array('geodis.Admin.shipment.index.module.error',
            'en',
            'The GEODIS printer module does not appear to be installed. Please install it
            by downloading it from your GEODIS space or contact your GEODIS support team.'
        ),
        array('geodis.front.popin.requiredEntry',
            'en',
            '(mandatory information)'
        ),
        array('geodis.Admin.shipment.index.error.checkPrestation',
            'en',
            'The selected service is incorrect.'
        )
    );
    protected $translations_front_en = array(
        array('geodis.front.popin.description.relay',
            'en',
            'I choose the pick-up point where I want to my order. <br/>GEODIS will send me an
            email and/or a text message as soon as the parcel will be available.'
        ),
        array('geodis.front.popin.option.description.liveEtage',
            'en',
            'Delivery of items that can be handled by the driver alone.<br/>Mandatory presence
            of an elevator beyond 2 floors.'
        ),
        array('geodis.front.popin.option.name.liveEtage',
            'en',
            'Delivery on the consignee\'s floor'
        ),
        array('geodis.front.popin.prestation.description.classic.europe.exp',
            'en',
            'I will be delivered at my home between 24h and 72h.'
        ),
        array('geodis.front.popin.prestation.description.classic.france.exp',
            'en',
            'I will be delivered at the latest the next day before 1:00 pm, Saturday morning included.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.tel',
            'en',
            'GEODIS will contact me by phone to arrange a delivery appointment.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.tel.exp',
            'en',
            'GEODIS will contact me by phone to arrange a delivery appointment.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.web',
            'en',
            'I choose my delivery date, from Monday to Friday, on the GEODIS web portal.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.web.exp',
            'en',
            'I choose my half-day for delivery, Saturday morning included, on the web portal of GEODIS.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.europe.exp',
            'en',
            'I will be delivered at my home between 24h and 72h.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.france.exp',
            'en',
            'Once my order is shipped, France Express will send me by email or text message
            the half-day scheduled for delivery.<br>85&#37; shipments entrusted to GEODIS are
            delivered the next day before 1:00 pm, the rest in the afternoon.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.tel',
            'en',
            'As soon as my shipment reaches the delivery agency, GEODIS will contact me by
            phone to arrange the best time to be delivered.'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.tel.exp',
            'en',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.tel.exp',
            'en',
            'As soon as my shipment reaches the delivery agency, GEODIS will contact me by phone
            to arrange the best time to be delivered.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.web',
            'en',
            'GEODIS will send me a notification by email or sms to plan my delivery.<br/>
            I can choose the delivery window that suits me.<br>-<span style="margin-left: 2em;">
            </span>Possible delivery from Monday to Friday over a period of 7 days<br>-
            <span style="margin-left: 2em;"></span>Possible evening delivery in certain cities<br>-
            <span style="margin-left: 2em;"></span>Possibility of picking-up the parcel at the agency.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.web.exp',
            'en',
            'GEODIS will send me by email or sms my half-day scheduled for delivery.
            <br>I can change it if it does not suit me.<br>-<span style="margin-left: 2em;">
            </span>Possible delivery from Monday to Saturday morning over a period of 7 days<br/>-
            <span style="margin-left: 2em;"></span>Possinle evening delivery in certain cities<br/>-
            <span style="margin-left: 2em;"></span>Option to remove the parcel at the agency.'
        ),
        array('geodis.front.popin.prestation.name.classic.europe.exp',
            'en',
            'EXPRESS DELIVERY'
        ),
        array('geodis.front.popin.prestation.name.classic.france.exp',
            'en',
            'EXPRESS DELIVERY'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.tel',
            'en',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.web',
            'en',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.web.exp',
            'en',
            'ON DEMAND PREMIUM'
        ),
        array('geodis.front.popin.contactDescription.tel',
            'en',
            'I fill in my phone number and/or my email.'
        ),
        array('geodis.front.popin.contactDescription.web',
            'en',
            'I fill in my phone number and/or my email.'
        ),
        array('geodis.front.popin.submit',
            'en',
            'Validate my choice'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.tel',
            'en',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.tel',
            'en',
            'GEODIS will contact me by phone to arrange a delivery appointment.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.tel',
            'en',
            'As soon as my shipment reaches the delivery agency, GEODIS will contact me by
            phone to arrange the best time to be delivered.'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.web',
            'en',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.web',
            'en',
            'I choose my delivery day, from Monday to Friday, on the GEODIS web portal.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.web',
            'en',
            'GEODIS will send me a notification by email or sms to plan my delivery.<br/>
            I can choose the delivery window that suits me.<br>-<span style="margin-left: 2em;">
            </span>Possible delivery from Monday to Friday over a period of 7 days<br>-
            <span style="margin-left: 2em;"></span>Possible evening delivery in certain cities<br>-
            <span style="margin-left: 2em;"></span>Possibility of picking-up the parcel at the agency.'
        ),
        array('geodis.front.popin.option.name.livEtage',
            'en',
            'Delivery on the consignee\'s floor'
        ),
        array('geodis.front.popin.option.name.depotage',
            'en',
            'Depalletizing'
        ),
        array('geodis.front.popin.option.name.miseLieuUtil',
            'en',
            'Delivery in the consignee\'s room'
        ),
        array('geodis.front.popin.option.none.label',
            'en',
            'No service'
        ),
        array('geodis.front.popin.floor.error',
            'en',
            'Please enter the floor number.'
        ),
        array('geodis.front.popin.contactInformations',
            'en',
            'Contact information :'
        ),
        array('geodis.front.popin.contactDescription',
            'en',
            'I fill in my phone number and/or my email.'
        ),
        array('geodis.front.popin.services.available',
            'en',
            'I choose an optional service :'
        ),
        array('geodis.front.popin.email.placeholder',
            'en',
            'E-mail'
        ),
        array('geodis.front.popin.email.error',
            'en',
            'Please enter a valid email address.'
        ),
        array('geodis.front.popin.telephone.placeholder',
            'en',
            'Phone'
        ),
        array('geodis.front.popin.telephone.error',
            'en',
            'Please enter a valid landline number.'
        ),
        array('geodis.front.popin.mobile.placeholder',
            'en',
            'Mobile phone'
        ),
        array('geodis.front.popin.mobile.error',
            'en',
            'Please enter a valid mobile number.'
        ),
        array('geodis.front.popin.floor.placeholder',
            'en',
            'Floor number'
        ),
        array('geodis.front.popin.digicode.placeholder',
            'en',
            'Door code'
        ),
        array('geodis.front.popin.option.description.depotage',
            'en',
            'Opening of the pallet, picking up the pallet and the outer packaging.'
        ),
        array('geodis.front.popin.option.description.livEtage',
            'en',
            'Delivery of items that can be handled by the driver alone.<br/>Mandatory
            presence of an elevator beyond 2 floors.'
        ),
        array('geodis.front.popin.option.description.miseLieuUtil',
            'en',
            'Delivery in the room that I have indicated to the driver (floor, reserve, basement, ...).'
        ),
        array('geodis.front.popin.title.%s',
            'en',
            '%1$s'
        ),
        array('geodis.front.popin.subtitle.%s',
            'en',
            '%1$s'
        ),
        array('geodis.front.popin.prestation.name.CALBEMES.MES.RDW',
            'en',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.description.CALBEMES.MES.RDW',
            'en',
            'I choose my delivery day, from Monday to Friday, on the GEODIS web portal.'
        ),
        array('geodis.front.popin.point.distanceKilometers.@',
            'en',
            '@ km'
        ),
        array('geodis.front.popin.point.distanceMeters.@',
            'en',
            '@ m'
        ),
        array('geodis.front.popin.address.button',
            'en',
            'Search'
        ),
        array('geodis.front.popin.point.choose',
            'en',
            'Choose'
        ),
        array('geodis.front.popin.point.selected',
            'en',
            'Selected'
        ),
        array('geodis.front.popin.point.displayTimetable',
            'en',
            'Show schedules'
        ),
        array('geodis.front.popin.point.timetable.title.monday',
            'en',
            'On Monday'
        ),
        array('geodis.front.popin.point.timetable.title.tuesday',
            'en',
            'Tuesday'
        ),
        array('geodis.front.popin.point.timetable.title.wednesday',
            'en',
            'Wednesday'
        ),
        array('geodis.front.popin.point.timetable.title.thursday',
            'en',
            'Thursday'
        ),
        array('geodis.front.popin.point.timetable.title.friday',
            'en',
            'Friday'
        ),
        array('geodis.front.popin.point.timetable.title.saturday',
            'en',
            'Saturday'
        ),
        array('geodis.front.popin.point.timetable.title.sunday',
            'en',
            'Sunday'
        ),
        array('geodis.front.popin.point.timeline.and',
            'en',
            'and'
        ),
        array('geodis.front.popin.point.timeline.closed',
            'en',
            'closed'
        ),
        array('geodis.front.popin.instruction.placeholder',
            'en',
            'Delivery instructions (optional)'
        ),
        array('geodis.front.popin.address.placeholder',
            'en',
            'Address or postal code'
        ),
        array('geodis.front.popin.action.displayMap',
            'en',
            'Show map'
        ),
        array('geodis.front.popin.action.displayList',
            'en',
            'View list'
        ),
        array('geodis.front.popin.prestation.name.classic.france',
            'en',
            'STANDARD DELIVERY'
        ),
        array('geodis.front.popin.prestation.name.classic.europe',
            'en',
            'STANDARD DELIVERY'
        ),
        array('geodis.front.popin.prestation.description.classic.france',
            'en',
            'I am delivered between 24 and 48h, from Monday to Friday.'
        ),
        array('geodis.front.popin.prestation.description.classic.europe',
            'en',
            'I am delivered to my home between 48h and 96h.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.france',
            'en',
            'Once my order is shipped, GEODIS will send me an email or sms to track the routing.
            <br> GEODIS deliveries are made within 24 to 48 hours from Monday to Friday.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.europe',
            'en',
            'I am delivered to my home between 48h and 96h.'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.exp',
            'en',
            'Appointment in France'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.exp',
            'en',
            'Appointment in France (description)'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.exp',
            'en',
            'Appointment in France (long description)'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.exp',
            'en',
            'Appointment in Europe'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.exp',
            'en',
            'Appointment in Europe (description)'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.exp',
            'en',
            'Appointment in Europe (long description)'
        ),
        array('geodis.*.*.cron.description',
            'en',
            'Use this url below in your CRON planner to recover status updates for your shipments'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.fixed.label',
            'en',
            'Fixed portion regardless of amount or weight'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.according.amount.weight.label',
            'en',
            'Defines according to the amount or weight per zone'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.parameters.label',
            'en',
            'You must configure the rates in the Prestashop carriers menu'
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels',
            'en',
            'Print Labels'
        ),
        array('geodis.Admin.OrdersGrid.index.action.sendShipments',
            'en',
            'Send Shipments'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.order.checked',
            'en',
            'At least one order must be checked'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.failed.to.find.all.orders',
            'en',
            'Failed to find all orders in database'
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.failed',
            'en',
            'The sending of one or more orders has not been created. Please create shipments of these orders to print labels :'
        ),
        array('geodis.Admin.OrdersGrid.index.action.sendShipments.failed',
            'en',
            'One or more shipments could not be transmitted because they were not created or the labels were not printed or have already been transmitted. Please check unsent shipments:'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.print.ws.answer',
            'en',
            'No response from the Geodis Web Service received'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.send.shipments.ws.answer',
            'en',
            'No response from the Geodis Web Service received'
        ),
        array('geodis.Admin.OrdersGrid.index.action.success.shipments',
            'en',
            'The following shipments have been transmitted successfully : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.form.label',
            'en',
            'Your labels are ready : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.form.download.btn.label',
            'en',
            'Download'
        ),
        array('geodis.Admin.OrdersGrid.index.action.downloadFile.error.data.empty',
            'en',
            'File name or content is empty'
        )
    );
    protected $translations_back_fr = array(
        array('geodis..*.*.adminmenu.shipmentgridprint',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.Removal.index.prestationAccount',
            'fr',
            'Prestation / Compte'
        ),
        array('geodis.Admin.index.warning.noCarrierAvailable',
            'fr',
            'Aucune prestation n\\est inscrit pour le type de transport sélectionné.
            Allez à la page "Configuration Back-Office" pour configurer des prestations.'
        ),
        array('geodis.Admin.index.warning.noGroupCarrierAvailable',
            'fr',
            'Aucun type de transport n\est enregistré. Allez sur la page "Configuration Back-Office"
            pour configurer des types de transport.'
        ),
        array('geodis.Admin.index.warning.wsDisabled',
            'fr',
            'La saisie de vins et spiritueux n\est pas autorisée sur cette prestation.
            Veuillez vous rapprocher de votre commercial GEODIS.'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.departureDate',
            'fr',
            'Date de départ'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.isLabelPrinted',
            'fr',
            'Etiquettes imprimées'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.isTransmitted',
            'fr',
            'Transmis'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.reference',
            'fr',
            'Référence'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.statusLabel',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.GeneralConfiguration.index.purgeDelay.label',
            'fr',
            'Délai de conservation des logs (en jours)'
        ),
        array('geodis.Admin.GeneralConfiguration.index.purgeDelay.desc',
            'fr',
            'Afin de limiter l\'espace utilisé par les logs du module, nous vous proposons de
            purger automatiquement les logs.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.carrierLogoWidth.label',
            'fr',
            'Taille des logos s\'affichant sur votre front'
        ),
        array('geodis.Admin.GeneralConfiguration.index.carrierLogoWidth.desc',
            'fr',
            'Ce paramètre est nécessaire lorsque vous souhaitez changer les logos de la
            partie Front, afin de redimensionner vos images autimatiquement.'
        ),
        array('geodis.Admin.Shipment.index.wl.cancel.label',
            'fr',
            'Annuler'
        ),
        array('geodis.*.*.menu.order',
            'fr',
            'Mes commandes'
        ),
        array('geodis.*.*.menu.removal',
            'fr',
            'Mes enlèvements'
        ),
        array('geodis.*.*.menu.log',
            'fr',
            'Logs applicatives'
        ),
        array('geodis.Admin.GeneralConfiguration.index.ignoreOrderStates.label',
            'fr',
            'Statuts des commandes pour lesquelles la modification des états n\est plus possible'
        ),
        array('geodis.Admin.GeneralConfiguration.index.ignoreOrderStates.desc',
            'fr',
            'Choisissez les statuts pour lesquels la modification de l\'état de la commande ne
            plus être effectuée suite à la transmission au transporteur.'
        ),
        array('geodis.Admin.Index.mainTitle',
            'fr',
            'Intégrer les services de livraison GEODIS sur ma boutique en ligne.'
        ),
        array('geodis.Admin.Index.mainHead',
            'fr',
            'GEODIS met à votre disposition un module vous permettant d\'intégrer en quelques
            clics nos services de livraisons sur votre site e-marchand. Selon les destinations
            (en France ou en Europe), vous pouvez proposer à vos clients des modes de livraison
            sur rendez-vous ou sans rendez-vous, en standard ou en express. <br>Comment ça marche ?'
        ),
        array('geodis.Admin.Index.subtitle1',
            'fr',
            '1. Synchronisez votre compte client GEODIS à votre environnement Prestashop'
        ),
        array('geodis.Admin.Index.subcontent1',
            'fr',
            '<ul><li>Si vous êtes déjà client, connectez-vous à votre espace client GEODIS
            pour récupérer vos identifiants de connexion à Prestashop dans l’onglet
            <b>Mes informations>Mes Paramètres>Clés API</b>.</li><li>Rendez vous directement
            dans l’onglet <b>Mon compte</b> de votre module GEODIS pour renseigner vos identifiants.
            </li><li>Lancez la synchronisation.</li><li>Si vous n\'êtes  pas client, prenez contact
            avec notre équipe commerciale pour obtenir un compte client. <a href="#">Nous contacter.
            </a></li></ul> '
        ),
        array('geodis.Admin.Index.subtitle2',
            'fr',
            '2. Configurez votre offre de livraison'
        ),
        array('geodis.Admin.Index.subcontent2',
            'fr',
            '<ul><li>Dès lors que votre compte est synchronisé, rendez-vous dans l’onglet
            <b>Configuration Back-Office</b> pour paramétrer les services de livraisons que
            vous souhaitez proposer à vos clients</li><li>GEODIS met à votre disposition les
            descriptions de chacun des services</li><li>Vous pouvez également choisir de
            personnaliser vos transporteurs dans l\'onglet <b>Configuration Front-Office</b>.
            </li></ul>'
        ),
        array('geodis.Admin.Index.subtitle3',
            'fr',
            '3. Gérez vos envois vers la France et l’Europe'
        ),
        array('geodis.Admin.Index.subcontent3',
            'fr',
            '<ul><li>Vous recevez et traitez dans votre back-office les commandes passées
            depuis votre site e-marchand dans l\'onglet <b>Commandes</b> de votre module.
            </li><li>Imprimez vos étiquettes et vos bordereaux de remise au conducteur</li>
            <li>Validez vos commandes afin qu\'elles soient automatiquement transmises à
            l\'agence GEODIS.</li><li>Le suivi de vos envois remonte directement dans votre
            back-office grâce aux statuts d\'expédition qui s\'actualisent automatiquement</li>
            <li>Vous pouvez également faire des demandes d’enlèvement depuis l’onglet
            <b>Enlèvements<b></li></ul>'
        ),
        array('geodis.Admin.ConfigurationBack.OrderStateSelect.non',
            'fr',
            'Aucun état sélectionné'
        ),
        array('geodis.Admin.ConfigurationBack.OrderStateSelect.none',
            'fr',
            'Aucun état sélectionné'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.enabledFreeShipping.label',
            'fr',
            'Activer la gestion des frais de port offerts'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.enabledFreeShipping.desc',
            'fr',
            'Si vous souhaitez offrir les frais de port à partir d\'un montant de commande,
            veuillez activer la coche ci-dessous et renseigner le montant de référence.'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.freeShipping.label',
            'fr',
            'Frais de port offerts, à partir de'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.label',
            'fr',
            'Inclure les frais additionnels'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.includeAdditionnalShippingCost.desc',
            'fr',
            'Si vous avez des frais additionnels, en
            cochant cette case, ils seront également gratuits.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidNumberOfDays',
            'fr',
            'La durée de transport doit être un entier.'
        ),
        array('geodis.Admin.ShipmentController.index.warning.resetedFiscalCode',
            'fr',
            'Les codes fiscaux qui ne sont pas de type DAA, ont été réinitialisés.'
        ),
        array('geodis.Admin.ShipmentController.index.groupCarrier',
            'fr',
            'Type de transport'
        ),
        array('geodis.Admin.shipment.index.warning.noLabelsAvailable',
            'fr',
            'Nous n\'avons pas pu générer l\'étiquette correspondant à votre saisie.
            Veuillez vous rapprocher de votre contact GEODIS.'
        ),
        array('geodis.Admin.Shipment.submit.error.log.%1$s.%2$s',
            'fr',
            'l\'erreur suivante a été rencontrée lors de la transmission : %1$s %2$s.'
        ),
        array('geodis.Admin.Shipment.index.wineAndLiquor',
            'fr',
            'Vins et spiritueux'
        ),
        array('geodis.Admin.Shipment.index.wl.submit',
            'fr',
            'Sauvegarder'
        ),
        array('geodis.Admin.Shipment.index.submit.label',
            'fr',
            'Sauvegarder'
        ),
        array('geodis.Admin.Shipment.index.delivery.print',
            'fr',
            'Imprimer les étiquettes'
        ),
        array('geodis.Admin.Shipment.index.error.fillRemovalDate',
            'fr',
            'Veuillez renseigner la date de départ.'
        ),
        array('geodis.Admin.ConfigurationBack.tab.general',
            'fr',
            'Configuration générale'
        ),
        array('geodis.Admin.ConfigurationBack.tab.standard',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.Admin.ConfigurationBack.tab.express',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.Admin.ConfigurationBack.tab.ondemand',
            'fr',
            'Livraison sur rendez-vous'
        ),
        array('geodis.Admin.ShipmentController.index.carrier',
            'fr',
            'Transporteur'
        ),
        array('geodis.Admin.Shipment.submit.error',
            'fr',
            'Une erreur a été rencontrée lors de la transmission. Veuillez consulter
            l\'onglet "Mes logs" pour plus d\'informations.'
        ),
        array('geodis.Admin.Shipment.submit.success',
            'fr',
            'Transmission réalisée avec succès.'
        ),
        array('geodis.Admin.ConfigurationFront.tab.ondemand',
            'fr',
            'Livraison sur rendez-vous'
        ),
        array('geodis.Admin.ConfigurationFront.tab.relay',
            'fr',
            'Livraison dans un point de retrait'
        ),
        array('geodis.Admin.ConfigurationFront.legend.general',
            'fr',
            'Configuration générale'
        ),
        array('geodis.*.*.carrier.default.name.ondemand',
            'fr',
            'Livraison sur rendez-vous'
        ),
        array('geodis.*.*.carrier.default.delay.ondemand',
            'fr',
            'Délai de préparation'
        ),
        array('geodis.*.*.carrier.default.name.standard',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.*.*.carrier.default.delay.standard',
            'fr',
            'Délai de préparation'
        ),
        array('geodis.Admin.LogGrid.massDelete.success.%s',
            'fr',
            '%s ligne(s) supprimées avec succès.'
        ),
        array('geodis.Admin.ConfigurationBack.tab.relay',
            'fr',
            'Livraison dans un point de retrait'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.fieldset.prestation',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.ShipmentController.index.updateShippingLabel',
            'fr',
            'Modifier l\'envoi'
        ),
        array('geodis.Admin.ConfigurationFront.tab.general',
            'fr',
            'Configuration générale'
        ),
        array('geodis.Admin.ConfigurationFront.tab.standard',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.Admin.ConfigurationFront.tab.express',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.Admin.Removal.index.error.invalidBoxFormat',
            'fr',
            'Le format du nombre de colis n\est pas valide.'
        ),
        array('geodis.Admin.Removal.index.error.invalidPalletFormat',
            'fr',
            'Le format du nombre de palettes n\est pas valide.'
        ),
        array('geodis.Admin.Removal.index.error.invalidWeightFormat',
            'fr',
            'Le format du poids n\est pas valide.'
        ),
        array('geodis.Admin.Removal.index.error.invalidVolumeFormat',
            'fr',
            'Le format du volume n\est pas valide.'
        ),
        array('geodis.Admin.removal.index.error.notWlPrestation',
            'fr',
            'Les vins et spiritueux ne sont pas autorisés sur votre prestation.'
        ),
        array('geodis.Admin.Shipment.index.error.update',
            'fr',
            'Une erreur a été rencontrée durant la mise à jour du statut.'
        ),
        array('geodis.Admin.Information.index.tab.synchonisation',
            'fr',
            'Synchroniser mes informations'
        ),
        array('geodis.Admin.LogGrid.massDelete.error.noItemSelected',
            'fr',
            'Aucune ligne sélectionnée pour la suppression.'
        ),
        array('geodis.Admin.Removal.index.button.print',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.salesShipment.view.trackingNumber',
            'fr',
            'Numéro de suivi'
        ),
        array('geodis.Admin.salesShipment.view.trackingUrl',
            'fr',
            'URL de suivi'
        ),
        array('geodis.Admin.Information.index.tab.account',
            'fr',
            'Mon compte'
        ),
        array('geodis.Admin.Information.index.tab.shipmentPrestation',
            'fr',
            'Mes prestations d\'envoi'
        ),
        array('geodis.Admin.GeneralConfiguration.index.tab.removalPrestation',
            'fr',
            'Mes prestations d\'enlèvement'
        ),
        array('geodis.Admin.Address.index.site.label',
            'fr',
            'Mes informations de sites'
        ),
        array('geodis.Admin.Removal.index.tab.history',
            'fr',
            'Historique des demandes d\'enlèvements'
        ),
        array('geodis.Admin.salesShipment.view.title.%s',
            'fr',
            '%1$s informations d\'envois.'
        ),
        array('geodis.Admin.ServiceSynchronize.shipment.statusLabel.waitingTransmission',
            'fr',
            'A remettre au transporteur'
        ),
        array('geodis.Admin.salesShipment.view.trackingLink',
            'fr',
            'Cliquez ici'
        ),
        array('geodis.Admin.salesShipment.view.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.salesShipment.view.noInformationsAvailable',
            'fr',
            'Aucune information disponible pour le moment.'
        ),
        array('geodis.Admin.salesShipment.view.noWs',
            'fr',
            'Le WebService n\est pas accessible pour la transmission de l\'envoi.'
        ),
        array('geodis.Admin.salesShipment.view.incident.%s',
            'fr',
            'Une erreur a été rencontrée sur votre envoi #%1$s. Veuillez vérifier le statut
            de cet envoi pour plus d\'informations.'
        ),
        array('geodis.Admin.Shipment.index.volume.label',
            'fr',
            'Volume'
        ),
        array('geodis.Admin.shipment.index.error.Prestation',
            'fr',
            'Les caractéristiques de votre envoi ne sont pas compatible avec la prestation
            sélectionnée. Veuillez choisir une prestation adaptée ou vous rapprochez de votre
            commercial GEODIS.'
        ),
        array('geodis.Admin.ServiceSynchronize.shipment.statusLabel.transmitted',
            'fr',
            'En attente de prise en charge'
        ),
        array('geodis.Admin.Shipment.index.editTitle',
            'en',
            'Edit the shipment %1$s of the order %2$s'
        ),
        array('geodis.Admin.GeneralConfiguration.process.error.generic.%1$s.%2$s',
            'fr',
            'Le champ %1$s n\est pas conforme à la règle %2$s.'
        ),
        array('geodis.*.*.menu.module',
            'fr',
            'Présentation'
        ),
        array('geodis.*.*.menu.information',
            'fr',
            'Mon Compte'
        ),
        array('geodis.*.*.menu.back',
            'fr',
            'Configuration Back-Office'
        ),
        array('geodis.*.*.menu.front',
            'fr',
            'Configuration Front-Office'
        ),
        array('geodis.*.*.menu.address',
            'fr',
            'Adresses d\'enlèvement'
        ),
        array('geodis.*.*.adminMenu.shipments',
            'fr',
            'Mes Envois'
        ),
        array('geodis.*.*.adminMenu.log',
            'fr',
            'Logs'
        ),
        array('geodis.*.*.adminMenu.removal',
            'fr',
            'Demandes d\'enlèvement'
        ),
        array('geodis.Admin.GeneralConfiguration.index.form.legend',
            'fr',
            'Configuration générale'
        ),
        array('geodis.Admin.GeneralConfiguration.index.active.label',
            'fr',
            'Actif'
        ),
        array('geodis.Admin.GeneralConfiguration.index.active.desc',
            'fr',
            'Utiliser les transporteurs GEODIS'
        ),
        array('geodis.Admin.GeneralConfiguration.index.partialShippingState.label',
            'fr',
            'Etat de la commande lorsqu\'un envoi partiel a été transmis au transporteur.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.partialShippingState.desc',
            'fr',
            'Choisissez le statut à afficher lorsque votre commande a été transmise
            partiellement au transporteur.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.completeShippingState.label',
            'fr',
            'Etat de la commande lorsqu\'un envoi complet a été transmis au transporteur.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.completeShippingState.desc',
            'fr',
            'Choisissez le statut à afficher lorsque votre commande a été transmise
            en totatlité au transporteur.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.submit.title',
            'fr',
            'Sauvegarder'
        ),
        array('geodis.Admin.GeneralConfiguration.index.departureDateDelay.label',
            'fr',
            'Délai pour la date de départ'
        ),
        array('geodis.Admin.GeneralConfiguration.index.departureDateDelay.desc',
            'fr',
            'Délai en jours pour estimer la date de départ.'
        ),
        array('geodis.Admin.GeneralConfiguration.post.message.success',
            'fr',
            'Configuration sauvegardée.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.useWhiteLabel.label',
            'fr',
            'Personnaliser les transporteurs.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.useWhiteLabel.desc',
            'fr',
            'Modifiez la description et/ou le logo des transporteurs GEODIS.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.availableOrderStates.label',
            'fr',
            'Etats des commandes pour lesquelles la saisie d\'un envoi est possible.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.availableOrderStates.desc',
            'fr',
            'Choisissez les statuts permettant la création d\'un envoi.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.api.login.label',
            'fr',
            'Connexion à votre compte GEODIS'
        ),
        array('geodis.Admin.GeneralConfiguration.index.api.secret.label',
            'fr',
            'Clé secrète de votre compte GEODIS pour utiliser le module
            (cf. Guide du module GEODIS).'
        ),
        array('geodis.Admin.GeneralConfiguration.index.mapEnabled.label',
            'fr',
            'Afficher la carte GoogleMaps'
        ),
        array('geodis.Admin.GeneralConfiguration.index.mapEnabled.desc',
            'fr',
            'Active l\'affichage de la carte google à vos clients,
            sur le choix de livraison en retrait (nécessite un compte google API
            pour son bon fonctionnement)'
        ),
        array('geodis.Admin.GeneralConfiguration.index.loadGoogleMapJs.label',
            'fr',
            'Charger Google Map Script'
        ),
        array('geodis.Admin.GeneralConfiguration.index.loadGoogleMapJs.desc',
            'fr',
            'Si le script est déjà chargé, vous devez désactiver cette option.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapApiKey.label',
            'fr',
            'Clé de l\'API Google Map'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapApiKey.desc',
            'fr',
            'Créez une nouvelle clé ici : https://cloud.google.com/maps-platform/ .'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapClient.label',
            'fr',
            'Google Map Client'
        ),
        array('geodis.Admin.GeneralConfiguration.index.googleMapClient.desc',
            'fr',
            'Si vous n\'utilisez pas une clé d\'API mais un client-id, remplissez le formulaire et
            laissez la clé d\'API Google Map vide.'
        ),
        array('geodis.GeodisServiceConfiguration.OrderStateSelect.none',
            'fr',
            'Aucun'
        ),
        array('geodis.*.*.switch.enabled',
            'fr',
            'Oui'
        ),
        array('geodis.*.*.switch.disabled',
            'fr',
            'Non'
        ),
        array('geodis.Admin.OrdersGrid.index.action.createShipment',
            'fr',
            'Créer l\'envoi'
        ),
        array('geodis.Admin.Shipment.index.title',
            'fr',
            'Liste des commandes'
        ),
        array('geodis.Admin.OrdersGrid.index.action.editShipment',
            'fr',
            'Voir l\'envoi'
        ),
        array('geodis.Admin.OrdersGrid.index.action.viewOrders',
            'fr',
            'Retourner aux commandes'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.title',
            'fr',
            'Vos envois'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.reference',
            'fr',
            'Référence'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.incident',
            'fr',
            'Incident'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.transmitted',
            'fr',
            'Transmis'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.show',
            'fr',
            'Afficher'
        ),
        array('geodis.Admin.OrdersGrid.index.shipments.hide',
            'fr',
            'Masquer'
        ),
        array('geodis.Admin.OrdersGrid.index.headerTitle',
            'fr',
            'Envois'
        ),
        array('geodis.Admin.LogGrid.index.headerTitle',
            'fr',
            'Les journaux'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.shipmentList',
            'fr',
            'Vos envois'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.reference',
            'fr',
            'Référence'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.customerName',
            'fr',
            'Client'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.customerEmail',
            'fr',
            'Email'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.shippingList',
            'fr',
            'Envoi'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderStatus',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderDate',
            'fr',
            'Date de l\'envoi'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.orderTotal',
            'fr',
            'Total de l\'envoi'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.carrier',
            'fr',
            'Transporteur'
        ),
        array('geodis.Admin.OrdersGrid.index.grid.actions',
            'fr',
            'Choix'
        ),
        array('geodis.Admin.AdminGeodisOrdersGrid.index.grid.link.print',
            'fr',
            'En attente d\'impression'
        ),
        array('geodis.Admin.AdminGeodisOrdersGrid.index.grid.link.transmit',
            'fr',
            'En attente de validation'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidIntWLField',
            'fr',
            'Le nombre doit être un entier.'
        ),
        array('geodis.Admin.ShipmentController.index.error.incompatibleFicalCode',
            'fr',
            'Un régime fiscal DAA est déjà sélectionné pour un autre article. Vous ne pouvez pas
            choisir un autre régime fiscal dans le même envoi.'
        ),
        array('geodis.Admin.Shipment.index.wl.label',
            'fr',
            'Sauvegarder'
        ),
        array('geodis.Admin.ShipmentController.index.warning.daaUniq',
            'fr',
            'En sélectionnant le régime fiscal DAA, tous les autres régimes fiscaux seront réinitialisés.'
        ),
        array('geodis.Admin.ShipmentController.index.error.missingWLField',
            'fr',
            'Merci de saisir tous les champs obligatoires.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidOrder',
            'fr',
            'Erreur technique : identifiant de commande non valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidShipment',
            'fr',
            'Erreur technique : identifiant d\'envoi non valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.pastDate',
            'fr',
            'Choisissez une date de départ ultérieure.'
        ),
        array('geodis.Admin.ShipmentController.index.error.missingDate',
            'fr',
            'Merci de saisir une date de départ.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidDate',
            'fr',
            'Date de départ invalide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidWeight',
            'fr',
            'Le poids renseigné n\'est pas valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidVolume',
            'fr',
            'Le volume renseigné n\'est pas valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidHeight',
            'fr',
            'La longueur renseignée n\'est pas valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidDepth',
            'fr',
            'La largeur renseignée n\'est pas valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.invalidWidth',
            'fr',
            'La hauteur renseignée n\'est pas valide.'
        ),
        array('geodis.Admin.ShipmentController.index.error.noProductsAvailable',
            'fr',
            'Les étiquettes ont été créées, l\'envoi n\est plus modifiable.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unavailableWSDate',
            'fr',
            'La transmission de votre envoi n\'a pas pu être effectuée. Veuillez réessayer ultérieurement.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unvailableWSPrint',
            'fr',
            'Les étiquettes ne peuvent pas être imprimées. Veuillez réessayer ultérieurement.'
        ),
        array('geodis.Admin.ShipmentController.index.error.unvailableWSSendShipment',
            'fr',
            'La transmission de votre envoi n\'a pas pu être effectuée. Veuillez réessayer ultérieurement.'
        ),
        array('geodis.Admin.ShipmentController.index.success.labelsprinted',
            'fr',
            'Les étiquettes ont été imprimées avec succès. Vous pouvez maintenant transmettre votre envoi.'
        ),
        array('geodis.Admin.ShipmentController.index.success.shipmentSend',
            'fr',
            'Votre envoi a été transmis avec succès.'
        ),
        array('geodis.Admin.ShipmentController.index.success.shipmentSubmit',
            'fr',
            'Votre envoi a bien été sauvegardé.'
        ),
        array('geodis.Admin.ShipmentController.index.error.cannotValidate',
            'fr',
            'La transmission de votre envoi n\'a pas pu être effectuée.'
        ),
        array('geodis.Admin.Shipment.index.button.back',
            'fr',
            'Retour'
        ),
        array('geodis.Admin.Shipment.index.warning.noWlAccepted',
            'fr',
            'Attention : la prestation que vous avez choisie n\'autorise pas les vins et spiritueux.'
        ),
        array('geodis.Admin.shipment.index.warning.wsDisabled',
            'fr',
            'La saisie de vins et spiritueux est désactivée sur cette prestation.'
        ),
        array('geodis.Admin.shipment.index.warning.noCarrierAvailable',
            'fr',
            'Aucune prestation n\est paramétrée pour ce type de transport. Veuillez en paramétrer
            au moins une prestation dans l\'onglet "Configuration Back-office".'
        ),
        array('geodis.Admin.shipment.index.warning.noGroupCarrierAvailable',
            'fr',
            'Ce type de transport n\est pas paramétré. Veuillez réaliser le paramétrage dans
            l\'onglet "Configuration Back-office".'
        ),
        array('geodis.Admin.Shipment.index.createTitle',
            'fr',
            'Créer un envoi pour la commande %s'
        ),
        array('geodis.Admin.Shipment.index.editTitle',
            'fr',
            'Modifier l\'envoi %1$s de la commande %2$s'
        ),
        array('geodis.Admin.Shipment.index.removalDate',
            'fr',
            'Date de départ'
        ),
        array('geodis.Admin.Shipment.index.carrier',
            'fr',
            'Transporteur'
        ),
        array('geodis.Admin.Shipment.index.packageReference',
            'fr',
            'Référence du colis : #'
        ),
        array('geodis.Admin.Shipment.index.placeholder.height',
            'fr',
            'Hauteur (cm)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.width',
            'fr',
            'Largeur (cm)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.depth',
            'fr',
            'Longueur (cm)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.weight',
            'fr',
            'Poids (kg)'
        ),
        array('geodis.Admin.Shipment.index.placeholder.volume',
            'fr',
            'Volume (m3)'
        ),
        array('geodis.Admin.Shipment.index.quantity',
            'fr',
            'Quantité'
        ),
        array('geodis.Admin.Shipment.index.reference',
            'fr',
            'Référence du produit'
        ),
        array('geodis.Admin.Shipment.index.combinationReference',
            'fr',
            'Références de l\'envoi'
        ),
        array('geodis.Admin.Shipment.index.packageType.box',
            'fr',
            'Colis'
        ),
        array('geodis.Admin.Shipment.index.packageType.pallet',
            'fr',
            'Palette'
        ),
        array('geodis.Admin.Shipment.index.name',
            'fr',
            'Nom de l\'article'
        ),
        array('geodis.Admin.Shipment.index.vs',
            'fr',
            'Vins & Spiritueux'
        ),
        array('geodis.Admin.Shipment.index.customs',
            'fr',
            'Code des taxes'
        ),
        array('geodis.Admin.Shipment.index.addPackage',
            'fr',
            'Ajouter un nouveau colis'
        ),
        array('geodis.Admin.Shipment.index.cancel',
            'fr',
            'Retour'
        ),
        array('geodis.Admin.Shipment.index.submit',
            'fr',
            'Sauvegarder mon envoi'
        ),
        array('geodis.Admin.Shipment.index.submitandnew',
            'fr',
            'Sauvegarder et créer un nouvel envoi'
        ),
        array('geodis.Admin.Shipment.index.print',
            'fr',
            'Imprimer les étiquettes'
        ),
        array('geodis.Admin.Shipment.index.send.label',
            'fr',
            'Transmettre mon envoi'
        ),
        array('geodis.Admin.Shipment.index.send.confirm',
            'fr',
            'Etes-vous sûr ? En validant, vous ne pourrez plus modifier l\'envoi transmis à GEODIS.'
        ),
        array('geodis.Admin.Shipment.index.send.infobulle.print',
            'fr',
            'Veuillez imprimer vos étiquettes avant de transmettre votre envoi.'
        ),
        array('geodis.Admin.Shipment.index.send.infobulle.submit',
            'fr',
            'Veuillez sauvegarder votre envoi avant de le transmettre.'
        ),
        array('geodis.Admin.Shipment.index.error.missingCarrier',
            'fr',
            'Veuillez sélectionner un transporteur.'
        ),
        array('geodis.Admin.Shipment.index.error.missingDimensions',
            'fr',
            'Veuillez saisir toutes les dimensions de l\'envoi.'
        ),
        array('geodis.Admin.Shipment.index.error.wrongValueDimensions',
            'fr',
            'Veuillez saisir une valeur numérique pour les dimensions.'
        ),
        array('geodis.Admin.Shipment.index.error.noRowsSelected',
            'fr',
            'Aucune ligne sélectionnée pour ce colis.'
        ),
        array('geodis.Admin.Shipment.index.error.exceededQuantity',
            'fr',
            'Vous avez sélectionné plus d\'articles que la quantité disponible.'
        ),
        array('geodis.Admin.Shipment.index.error.taxCodeMissing',
            'fr',
            'Veuillez sélectionner un code de taxe.'
        ),
        array('geodis.Admin.Shipment.index.button.removePackage',
            'fr',
            'Retirer le colis'
        ),
        array('geodis.Admin.Shipment.index.success',
            'fr',
            'Envoi et colis créés avec succès.'
        ),
        array('geodis.Admin.Shipment.index.error',
            'fr',
            'Erreur lors de la création de l\'envoi.'
        ),
        array('geodis.Admin.Shipment.index.error.wrongRemovalDate',
            'fr',
            'Veuillez sélectionner une date de départ valide.'
        ),
        array('geodis.*.*.error.notSyncronized',
            'fr',
            'Votre compte n\'est pas encore synchronisé. Rendez vous dans l\'onglet "Mon Compte"
            pour lancer une synchronisation.'
        ),
        array('geodis.*.*.carrier.defaultName',
            'fr',
            'Transporteur GEODIS'
        ),
        array('geodis.Admin.ConfigurationBack.AjaxSave.success',
            'fr',
            'Configuration sauvegardée.'
        ),
        array('geodis.Admin.ConfigurationBack.AjaxSave.notice.carrierDisabled.%s',
            'fr',
            'Le transporteur %s était dissocié du module et est maintenant désactivé.'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.account.label',
            'fr',
            'Code agence / compte'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.preparationDelay.label',
            'fr',
            'Délai de préparation (jours)'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.prestation.label',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.label',
            'fr',
            'Transporteur'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.none',
            'fr',
            'Aucun'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.carrier.new',
            'fr',
            'Créer un nouveau transporteur'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.label',
            'fr',
            'Prix'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.active.label',
            'fr',
            'Activer'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.priceImpact.label',
            'fr',
            'Prix du service'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.remove',
            'fr',
            'Retirer'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.disable',
            'fr',
            'Désactiver'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.addCarrier',
            'fr',
            'Ajouter une prestation'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.enable',
            'fr',
            'Activer'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.action.save',
            'fr',
            'Enregistrer cette configuration'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.group.name.%s',
            'fr',
            'Type de transport : %s'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.nonUniqCarrierReference.%s',
            'fr',
            'La prestation associée à ce couple code agence / compte est sélectionnée deux fois.
            Veuillez modifier l\'une des deux configuration %s.'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.price.%s',
            'fr',
            '%s n\est pas un prix valide.'
        ),
        array('geodis.Admin.ConfigurationBack.ajaxSave.error.priceImpact.%s',
            'fr',
            '%s n\est pas un prix valide.'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.depotage',
            'fr',
            'Dépotage'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.miseLieuUtil',
            'fr',
            'Mise en lieu d\'utilisation'
        ),
        array('geodis.Admin.ConfigurationBack.option.name.livEtage',
            'fr',
            'Livraison à l\'étage'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.depotage',
            'fr',
            'Ouverture de la palette, reprise de la palette et de l\'emballage extérieur.'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.miseLieuUtil',
            'fr',
            'Livraison dans la pièce que j\'aurai indiquée au conducteur (étage, réserve, sous-sol,...).'
        ),
        array('geodis.Admin.ConfigurationBack.option.description.livEtage',
            'fr',
            'Livraison d\'envois manipulables par le conducteur seul.<br>Présence obligatoire
            d\'un ascenseur au-delà de 2 étages.'
        ),
        array('geodis.Admin.ShipmentController.index.error.removeShipmentForbidden',
            'fr',
            'Vous n\'êtes pas autorisé à supprimer un envoi terminé.'
        ),
        array('geodis.Admin.ShipmentController.delete.success',
            'fr',
            'Envoi supprimé avec succès.'
        ),
        array('geodis.Admin.Shipment.delete.back',
            'fr',
            'Retour'
        ),
        array('geodis.Admin.ConfigurationFront.index.form.legend',
            'fr',
            'Configuration Front-Office'
        ),
        array('geodis.Admin.ConfigurationFront.index.submit.title',
            'fr',
            'Sauvegarder'
        ),
        array('geodis.Admin.ConfigurationFront.index.useWhiteLabel.label',
            'fr',
            'Personnaliser les transporteurs'
        ),
        array('geodis.Admin.ConfigurationFront.index.useWhiteLabel.desc',
            'fr',
            'Modifiez la description et/ou le logo des transporteurs GEODIS.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrier.title.%s',
            'fr',
            'Transporteur %s'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierName.label',
            'fr',
            'Nom du type de transport'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierName.desc',
            'fr',
            'Nom du mode livraison affiché aux clients du site.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierDescription.label',
            'fr',
            'Description du type de transport'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierDescription.desc',
            'fr',
            'Description de la prestation de transport pour les clients du site.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierActive.label',
            'fr',
            'Actif'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierActive.desc',
            'fr',
            'Paramètre permettant d\'activer ou de désactiver ce type de transport sur le site client.'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierLogo.label',
            'fr',
            'Logo'
        ),
        array('geodis.Admin.ConfigurationFront.index.carrierLogo.desc',
            'fr',
            'Paramètre permettant d\'activer ou de désactiver l\'affichage du logo sur le site client.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.success',
            'fr',
            'Configuration sauvegardée.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.uploadMaxFileSize',
            'fr',
            'Le fichier est trop volumineux. Essayez une image plus petite ou changez la
            configuration upload_max_filesize dans votre php.ini.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.maxFileSize',
            'fr',
            'Le fichier est trop volumineux. Essayez une image plus petite ou modifiez la
            configuration MAX_FILE_SIZE.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.noTmpDir',
            'fr',
            'Le fichier n\'a pas pu être téléchargé. Le dossier temporaire est manquant.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.nonWritableDir',
            'fr',
            'Le fichier n\'a pas pu être téléchargé. Le dossier temporaire n\est pas accessible
             en écriture.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.unknow',
            'fr',
            'Le fichier n\'a pas pu être téléchargé.'
        ),
        array('geodis.Admin.ConfigurationFront.submit.fileError.invalidExtension',
            'fr',
            'l\'extension de fichier n\est pas valide. Seuls les fichiers jpg, gif et png sont
            autorisés.'
        ),
        array('geodis.*.*.carrier.default.name.rdv',
            'fr',
            'Livraison sur rendez-vous'
        ),
        array('geodis.*.*.carrier.default.delay.rdv',
            'fr',
            'Je me fais livrer à la date qui me convient.'
        ),
        array('geodis.*.*.carrier.default.name.classic',
            'fr',
            'Livraison sans rendez-vous'
        ),
        array('geodis.*.*.carrier.default.delay.classic',
            'fr',
            'Je me fais livrer dès que possible.'
        ),
        array('geodis.*.*.carrier.default.name.relay',
            'fr',
            'Livraison dans un point de retrait'
        ),
        array('geodis.*.*.carrier.default.delay.relay',
            'fr',
            'Je me fais livrer dans un point de retrait à proximité de mon domicile.'
        ),
        array('geodis.*.*.carrier.default.name.bulky',
            'fr',
            'Livraison des colis volumineux'
        ),
        array('geodis.*.*.carrier.default.delay.bulky',
            'fr',
            'La livraison adaptée aux colis volumineux.'
        ),
        array('geodis.*.*.carrier.default.name.express',
            'fr',
            'Livraison de vos colis urgents'
        ),
        array('geodis.*.*.carrier.default.delay.express',
            'fr',
            'Pour tous mes envois urgents, GEODIS met à ma disposition des solutions de course dédiées.'
        ),
        array('geodis.Admin.Information.index.account.label',
            'fr',
            'Mon Compte'
        ),
        array('geodis.Admin.Information.index.explanation',
            'fr',
            'Ces informations sont issues de votre fiche client. Si des informations sont
            erronées ou que vous souhaitez ajuster des prestations, merci de contacter votre agence.'
        ),
        array('geodis.Admin.Information.index.prestation.label',
            'fr',
            'Mes prestations paramétrées pour la préparation de mes envois depuis l\'un de mes sites.'
        ),
        array('geodis.Admin.Information.index.removalRequest.label',
            'fr',
            'Mes prestations paramétrées pour mes demandes d\'enlèvement sur site.'
        ),
        array('geodis.Admin.LogGrid.index.grid.id',
            'fr',
            'Identifiant'
        ),
        array('geodis.Admin.LogGrid.index.grid.message',
            'fr',
            'Message'
        ),
        array('geodis.Admin.LogGrid.index.grid.isError',
            'fr',
            'Est en erreur'
        ),
        array('geodis.Admin.LogGrid.index.grid.logDate',
            'fr',
            'Date'
        ),
        array('geodis.Admin.Information.index.society',
            'fr',
            'Société'
        ),
        array('geodis.Admin.Information.index.address',
            'fr',
            'Adresse'
        ),
        array('geodis.Admin.Information.index.accountNumber.%s',
            'fr',
            'Numéro de compte : %s'
        ),
        array('geodis.Admin.Information.index.agency.%s',
            'fr',
            'Agence : %s'
        ),
        array('geodis.Admin.Information.index.connection.label',
            'fr',
            'Se connecter à mon compte GEODIS'
        ),
        array('geodis.Admin.Information.index.connection.login.placeholder',
            'fr',
            'Entrez votre identifiant GEODIS'
        ),
        array('geodis.Admin.Information.index.connection.token.placeholder',
            'fr',
            'Entrez votre API KEY'
        ),
        array('geodis.Admin.Information.index.connection.button.synchronise',
            'fr',
            'Synchroniser'
        ),
        array('geodis.Admin.Information.index.connection.button.connect',
            'fr',
            'S\'identifier'
        ),
        array('geodis.Admin.Information.index.connection.login.success',
            'fr',
            'Accès validé'
        ),
        array('geodis.Admin.Information.index.connection.token.info',
            'fr',
            'Vous trouverez votre API KEY sur Mon espace, dans les paramètres de votre compte,
            onglet API Key.'
        ),
        array('geodis.Admin.Information.index.connection.button.modify',
            'fr',
            'Changer vos paramètres de connexion'
        ),
        array('geodis.Admin.Information.connection.error.default',
            'fr',
            'Compte non trouvé'
        ),
        array('geodis.Admin.AdressConfiguration.index.isDefault',
            'fr',
            'Est par défaut'
        ),
        array('geodis.Admin.AdressConfiguration.index.address1',
            'fr',
            'Adresse 1'
        ),
        array('geodis.Admin.AdressConfiguration.index.address2',
            'fr',
            'Adresse 2'
        ),
        array('geodis.Admin.AdressConfiguration.index.zipCode',
            'fr',
            'Code postal'
        ),
        array('geodis.Admin.AdressConfiguration.index.city',
            'fr',
            'Code postal'
        ),
        array('geodis.Admin.AddressConfiguration.index.error',
            'fr',
            'Site introuvable dans les enregistrements de la base de données.'
        ),
        array('geodis.Admin.Address.index.defaultSite',
            'fr',
            'Utilisé par défaut'
        ),
        array('geodis.Admin.Address.index.setAsDefault',
            'fr',
            'Utiliser par défaut'
        ),
        array('geodis.Admin.Address.index.title.removalSites',
            'fr',
            'Mes sites d\'enlèvement'
        ),
        array('geodis.Admin.Shipment.index.fiscalCode.label',
            'fr',
            'Régime fiscal'
        ),
        array('geodis.Admin.Shipment.index.nbCol.label',
            'fr',
            'Nombre de cols'
        ),
        array('geodis.Admin.Shipment.index.nbCol.placeholder',
            'fr',
            'Nombre de cols'
        ),
        array('geodis.Admin.Shipment.index.volumeCl.label',
            'fr',
            'Capacité (cl)'
        ),
        array('geodis.Admin.Shipment.index.volumeL.label',
            'fr',
            'Volume en droits suspendus (l)'
        ),
        array('geodis.Admin.Shipment.index.volumeL.placeholder',
            'fr',
            'Ex: 0'
        ),
        array('geodis.Admin.Shipment.index.fiscalCodeRef.label',
            'fr',
            'Code de référence administratif'
        ),
        array('geodis.Admin.Shipment.index.fiscalCodeRef.placeholder',
            'fr',
            'Code de référence administratif'
        ),
        array('geodis.Admin.Shipment.index.nMvt.label',
            'fr',
            'Numéro du titre du mouvement'
        ),
        array('geodis.Admin.Shipment.index.nMvt.placeholder',
            'fr',
            'Numéro du titre du mouvement'
        ),
        array('geodis.Admin.Shipment.index.shippingDuration.label',
            'fr',
            'Temps de transport en jours'
        ),
        array('geodis.Admin.Shipment.index.shippingDuration.placeholder',
            'fr',
            'Temps de transport en jours'
        ),
        array('geodis.Admin.Shipment.index.nEa.label',
            'fr',
            'Numéro de destination EA'
        ),
        array('geodis.Admin.Shipment.index.nEa.placeholder',
            'fr',
            'Numéro de destination EA'
        ),
        array('geodis.Admin.Removal.index.form.legend',
            'fr',
            'Ma demande d\'enlèvement'
        ),
        array('geodis.Admin.Removal.index.submit.title',
            'fr',
            'sauvegarder'
        ),
        array('geodis.Admin.index.carrier',
            'fr',
            'Transporteur'
        ),
        array('geodis.Admin.index.groupCarrier',
            'fr',
            'Type de transport'
        ),
        array('geodis.Admin.Removal.index.removalAdresses',
            'fr',
            'Mon adresse d\'enlèvement'
        ),
        array('geodis.Admin.Removal.index.account',
            'fr',
            'Compte'
        ),
        array('geodis.Admin.Removal.index.prestation',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.Removal.index.numberOfBox',
            'fr',
            'Nombre de colis'
        ),
        array('geodis.Admin.Removal.index.numberOfPallet',
            'fr',
            'Nombre de palettes'
        ),
        array('geodis.Admin.Removal.index.weight',
            'fr',
            'Poids (kg)'
        ),
        array('geodis.Admin.Removal.index.volume',
            'fr',
            'Volume (m3)'
        ),
        array('geodis.Admin.Removal.index.observations',
            'fr',
            'Observations'
        ),
        array('geodis.Admin.Removal.index.reglementedTransport',
            'fr',
            'Transport de matières réglementées'
        ),
        array('geodis.Admin.Removal.index.fiscalCode',
            'fr',
            'Régime fiscal'
        ),
        array('geodis.Admin.Removal.index.legalVolume',
            'fr',
            'Volume légal'
        ),
        array('geodis.Admin.Removal.index.totalVolume',
            'fr',
            'Volume total (l)'
        ),
        array('geodis.Admin.Removal.index.noReglemented',
            'fr',
            'Je déclare que mon envoi ne contient pas de matières dangereuses, ni de vins et spiritueux.'
        ),
        array('geodis.Admin.Removal.index.reglemented',
            'fr',
            'Je déclare que mon envoi contient des vins et spiritueux.'
        ),
        array('geodis.Admin.Removal.index.removalDateWished',
            'fr',
            'Date d\'enlèvement souhaitée'
        ),
        array('geodis.Admin.Removal.index.table.head.reference',
            'fr',
            'Référence'
        ),
        array('geodis.Admin.Removal.index.table.head.dateAdd',
            'fr',
            'Demandé le'
        ),
        array('geodis.Admin.Removal.index.table.head.removalDate',
            'fr',
            'Date de l\'enlèvement'
        ),
        array('geodis.Admin.Removal.index.table.head.prestation',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.Removal.index.table.head.removalSite',
            'fr',
            'Site d\'enlèvement'
        ),
        array('geodis.Admin.Removal.index.table.head.volume',
            'fr',
            'Volume'
        ),
        array('geodis.Admin.Removal.index.table.head.weight',
            'fr',
            'Poids'
        ),
        array('geodis.Admin.Removal.index.table.head.nbPallet',
            'fr',
            'Nombre de palettes'
        ),
        array('geodis.Admin.Removal.index.table.head.nbBox',
            'fr',
            'Nombre de boites'
        ),
        array('geodis.Admin.Removal.index.table.head.action',
            'fr',
            'action'
        ),
        array('geodis.Admin.Removal.index.action.button.copy',
            'fr',
            'action'
        ),
        array('geodis.Admin.Removal.index.alert.success',
            'fr',
            'Demande d\'enlèvement enregistrée'
        ),
        array('geodis.Admin.Removal.index.table.row.sent',
            'fr',
            'Envoyé'
        ),
        array('geodis.Admin.Removal.index.table.head.print',
            'fr',
            'Voir la demande'
        ),
        array('geodis.Admin.Removal.index.table.action.print',
            'fr',
            'Impression'
        ),
        array('geodis.Admin.Removal.index.table.head.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.Removal.index.noRecord',
            'fr',
            'Aucun enregistrement trouvé'
        ),
        array('geodis.Admin.Removal.index.error.invalidWeight',
            'fr',
            'Poids invalide.'
        ),
        array('geodis.Admin.Removal.index.error.invalidVolume',
            'fr',
            'Volume invalide.'
        ),
        array('geodis.Admin.Removal.index.error.invalidQuantity',
            'fr',
            'Vous devez spécifier le nombre de colis ou le nombre de palettes.'
        ),
        array('geodis.Admin.Removal.index.error.invalidAccount',
            'fr',
            'Sélectionnez un compte.'
        ),
        array('geodis.Admin.Removal.index.error.invalidPrestation',
            'fr',
            'Sélectionnez une prestation / service.'
        ),
        array('geodis.Admin.Removal.index.error.missingTotalVolume',
            'fr',
            'Entrez le volume total.'
        ),
        array('geodis.Admin.Removal.index.error.invalidTotalVolume',
            'fr',
            'Volume total non valide.'
        ),
        array('geodis.Admin.Removal.index.error.pastDate.%s',
            'fr',
            'Choisissez une date d\'enlèvement après le %s.'
        ),
        array('geodis.Admin.Removal.index.error.invalidDate',
            'fr',
            'Sélectionnez une date d\'enlèvement valide.'
        ),
        array('geodis.Admin.Removal.index.table.title',
            'fr',
            'Demandes d\'enlèvement pour les 30 prochains jours'
        ),
        array('geodis.Admin.Information.index.connection.lastSynchronizationDate.%s',
            'fr',
            'Dernière date de synchronisation, le %1$s.'
        ),
        array('geodis.Admin.Information.sync.success',
            'fr',
            'Synchronisation terminée.'
        ),
        array('geodis.Admin.ServiceSynchronize.error.alreadySynchronize',
            'fr',
            'Les informations ont déjà été synchronisées récemment. Veuillez réessayer dans
            quelques heures'
        ),
        array('geodis.Admin.ServiceSynchronize.error.default',
            'fr',
            'La synchronisation avec le WebService a rencontré des problèmes. Merci de réessayer.'
        ),
        array('geodis.Admin.ServiceSynchronize.error.shipment.event.%s',
            'fr',
            'Un incident est signalé sur l\'envoi %1$s. Consultez l\'historique des envois
            pour plus d\'informations.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.title.%s',
            'fr',
            'Envoi #%1$s informations'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.history.title',
            'fr',
            'Historique des envois'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.status.%s',
            'fr',
            'Statut : %1$s'
        ),
        array('geodis.Admin.ShipmentStatus.table.date',
            'fr',
            'Date de l\'envoi'
        ),
        array('geodis.Admin.ShipmentStatus.table.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.ShipmentStatus.packages.title',
            'fr',
            'Contenu des colis'
        ),
        array('geodis.Admin.ShipmentStatus.package.referenceExpedition.%s',
            'fr',
            'Colis #%1$s'
        ),
        array('geodis.Admin.ShipmentStatus.package.statusLabel.%s',
            'fr',
            'Statut du colis : %1$s'
        ),
        array('geodis.Admin.ShipmentStatus.table.productName',
            'fr',
            'Nom du produit'
        ),
        array('geodis.Admin.ShipmentStatus.table.productReference',
            'fr',
            'Référence'
        ),
        array('geodis.Admin.ShipmentStatus.table.productQuantity',
            'fr',
            'Quantité'
        ),
        array('geodis.Admin.GeneralConfiguration.index.layout.label',
            'fr',
            'Personnalisation de la mise en page'
        ),
        array('geodis.Admin.GeneralConfiguration.index.layout.desc',
            'fr',
            'Par défaut, vous avez 4 mises en page disponibles: layouts / layout-left-column.tpl,
            layouts / layout-right-column.tpl, layouts / layout-both-columns.tpl, layouts /
            layout-full-width.tpl.'
        ),
        array('geodis.Admin.Shipment.index.prestation',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.Shipment.index.account',
            'fr',
            'Compte'
        ),
        array('geodis.Admin.Shipment.index.warning.daysOff',
            'fr',
            'La date que vous avez choisie est un jour ferié. Nous avons sélectionné le prochain
            jour ouvré pour votre date d\'enlèvement.'
        ),
        array('geodis.Admin.Removal.index.warning.daysOff',
            'fr',
            'La date que vous avez choisie n\est pas disponible. Nous avons sélectionné le prochain
            jour ouvré pour votre date d\'enlèvement.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.fiscalCode.label',
            'fr',
            'Régime fiscal par défaut'
        ),
        array('geodis.Admin.GeneralConfiguration.index.fiscalCode.desc',
            'fr',
            'Sélectionnez le régime fiscal que vous souhaitez utiliser par défaut lors de la
            création d\'un envoi.'
        ),
        array('geodis.Admin.Removal.index.timeSlot.daytime',
            'fr',
            'Toute la journée'
        ),
        array('geodis.Admin.Removal.index.timeSlot.morning',
            'fr',
            'Matin'
        ),
        array('geodis.Admin.Removal.index.timeSlot.afternoon',
            'fr',
            'Après-midi'
        ),
        array('geodis.Admin.Removal.index.timeSlot',
            'fr',
            'Créneau horaire'
        ),
        array('geodis.Admin.Removal.index.optionNotAvailable',
            'fr',
            'Options non disponibles'
        ),
        array('geodis.Admin.ServiceSynchronize.error.shipment.status.update',
            'fr',
            'La mise à jour du statut de l\'envoi rencontre une erreur.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.title.sameOrder',
            'fr',
            'Expéditions de la même commande.'
        ),
        array('geodis.Admin.ShipmentStatus.package.orderedQuantity.%s',
            'fr',
            'Quantité : %1$s'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.trackingUrl',
            'fr',
            'Voir sur le site Web du transporteur.'
        ),
        array('geodis.Admin.ShipmentStatus.shipment.trackingUrl.link.name',
            'fr',
            'Cliquez ici!'
        ),
        array('geodis.Admin.ShipmentStatus.package.quickView',
            'fr',
            'Rapide vue d\'ensemble'
        ),
        array('geodis.Admin.Shipment.index.printLabel',
            'fr',
            'Impression des étiquettes'
        ),
        array('geodis.Admin.Shipment.index.printDelivery',
            'fr',
            'Impression de bordereaux'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.reference',
            'fr',
            'Reference'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.departureDate',
            'fr',
            'Date de départ'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.isLabelPrinted',
            'fr',
            'Imprimer les étiquettes'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.statusLabel',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.actions',
            'fr',
            'actions'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.action.print',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.shipmentsGrid.index.grid.bulkAction.print',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.action.printLabel',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.headerTitle',
            'fr',
            'Etiquette(s) non imprimée(s)'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.actions',
            'fr',
            'Actions'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.id',
            'fr',
            'ID'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.groupCarrier',
            'fr',
            'Type de transport'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.carrier',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.receptNumber',
            'fr',
            'Numéro de récépissé'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.order',
            'fr',
            'Commande'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.grid.action.print',
            'fr',
            'Imprimer'
        ),
        array('geodis.Admin.shipmentsGrid.index.webservice.false',
            'fr',
            'Le WebService GEODIS a rencontré une erreur lors de son appel. Veuillez consulter
            l\'onglet "Logs" pour plus d\'informations.'
        ),
        array('geodis.Admin.shipmentsGridPrint.index.webservice.false',
            'fr',
            'Le WebService GEODIS a rencontré une erreur lors de son appel pour l\'impression.
            Veuillez consulter l\'onglet "Logs" pour plus d\'informations.'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.reference',
            'fr',
            'Reference'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.departureDate',
            'fr',
            'Date de départ'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.iscomplete',
            'fr',
            'Complet'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.statusLabel',
            'fr',
            'Status'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.actions',
            'fr',
            'Actions'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.isTransmitted',
            'fr',
            'Transmis'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.headerTitle',
            'fr',
            'Envoi non transmis'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.id',
            'fr',
            'Identifiant'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.action.transmit',
            'fr',
            'Transmettre'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.groupCarrier',
            'fr',
            'Type de transport'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.carrier',
            'fr',
            'Prestation'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.status',
            'fr',
            'Statut'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.receptNumber',
            'fr',
            'Numéro de récépissé'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.grid.order',
            'fr',
            'Commande'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.confirm.transmit',
            'fr',
            'Etes-vous sûr ? En validant'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.webservice.false',
            'fr',
            'Le WebService GEODIS a rencontré une erreur lors de son appel. Veuillez
            consulter l\'onglet "Logs" pour plus d\'informations.'
        ),
        array('geodis.Admin.shipmentsGridTransmit.index.action.TransmisLabel',
            'fr',
            'Transmettre mon envoi'
        ),
        array('geodis.Admin.PackageLabel.print.error.cannotPrint',
            'fr',
            'Erreur lors de l\'impression d\'étiquette(s).'
        ),
        array('geodis.Admin.DeleveryLabel.print.error.cannotPrint',
            'fr',
            'Erreur lors de l\'impression d\'étiquette(s).'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.label',
            'fr',
            'Imprimer sur une imprimante thermique.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.desc',
            'fr',
            'l\'impression des étiquettes peut être réalisée en PDF.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.install',
            'fr',
            'Veuillez installer le module d\'impression GEODIS au préalable.'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.port.label',
            'fr',
            'Port du module d\'impression GEODIS'
        ),
        array('geodis.Admin.GeneralConfiguration.index.thermalPrinting.port.desc',
            'fr',
            'Port de connexion du module d\'impression GEODIS (3000 par défaut)'
        ),
        array('geodis.Admin.shipment.index.thermalPrinting.error',
            'fr',
            'Une erreur a été rencontrée lors de l\'impression. Veuillez vérifier votre configuration.'
        ),
        array('geodis.Admin.shipment.index.module.error',
            'fr',
            'Le module d\'impression GEODIS ne semble pas être installé. Veuillez l\'installer en
            le téléchargeant depuis votre espace client GEODIS ou vous rapprochez de votre contact
            GEODIS.'
        ),
        array('geodis.Admin.shipment.index.error.checkPrestation',
            'fr',
            'La prestation sélectionnée est incorrecte.'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.fixed.label',
            'fr',
            'Part fixe quelque soit le montant ou le poids'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.according.amount.weight.label',
            'fr',
            'Définit selon le montant ou le poids par zone'
        ),
        array('geodis.Admin.ConfigurationBack.ajax.price.parameters.label',
            'fr',
            'Vous devez paramétrer les tarifs dans le menu Transporteurs de Prestashop'
        )
    );
    protected $translations_front_fr = array(
        array('geodis.front.popin.description.relay',
            'fr',
            'Je choisis le point de retrait où je viendrai retirer ma commande.<br/>GEODIS
            m\'enverra un email et/ou un sms dès que le colis sera disponible.'
        ),
        array('geodis.front.popin.option.description.liveEtage',
            'fr',
            'Livraison des envois traités par le chauffeur seul. Présence obligatoire
            d\'un ascenseur au-delà de 2 étages.'
        ),
        array('geodis.front.popin.option.name.liveEtage',
            'fr',
            'Livraison à l\'étage'
        ),
        array('geodis.front.popin.prestation.description.classic.europe.exp',
            'fr',
            'Je suis livré(e) à mon domicile entre 24h et 72h.'
        ),
        array('geodis.front.popin.prestation.description.classic.france.exp',
            'fr',
            'Je suis livré(e) au plus tard le lendemain avant 13h, samedi matin compris.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.tel',
            'fr',
            'GEODIS me contactera par téléphone pour convenir d\'un rendez-vous de livraison.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.tel.exp',
            'fr',
            'GEODIS me contactera par téléphone pour convenir d\'un rendez-vous de livraison.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.web',
            'fr',
            'Je choisis ma journée de livraison, du lundi au vendredi, sur le portail web de GEODIS.'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.web.exp',
            'fr',
            'Je choisis ma demi-journée de livraison, samedi matin compris, sur le
            portail web de GEODIS.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.europe.exp',
            'fr',
            'Je suis livré(e) à mon domicile entre 24h et 72h.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.france.exp',
            'fr',
            'Dès le départ de ma commande, GEODIS m\'enverra par email ou sms, la demi-journée
            prévue pour la livraison.<br>85&#37; des envois confiés à GEODIS sont livrés le
            lendemain avant 13h, le reste, dans l\'après-midi.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.tel',
            'fr',
            'Dès que mon envoi aura atteint l\'agence de livraison, GEODIS me contactera
            par téléphone pour convenir du meilleur moment pour me livrer.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.tel.exp',
            'fr',
            'Dès que mon envoi aura atteint l\'agence de livraison, GEODIS me contactera
            par téléphone pour convenir du meilleur moment pour me livrer.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.web',
            'fr',
            'GEODIS m\'enverra une notification par email ou sms pour planifier ma livraison.
            <br>Je peux choisir le créneau de livraison qui me convient.<br>-
            <span style="margin-left: 2em;"></span>Livraison possible du lundi au vendredi
            sur une période de 7 jours<br>-<span style="margin-left: 2em;"></span>
            Livraison en soirée proposée sur certaines agglomérations<br>-
            <span style="margin-left: 2em;"></span>Possibilité de retirer le colis à l\'agence.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.web.exp',
            'fr',
            'GEODIS m\'enverra par email ou sms ma demi-journée de livraison prévue.
            <br>Je peux la modifier si elle ne me convient pas.<br>-<span style="margin-left: 2em;">
            </span>Livraison possible du lundi au samedi matin sur une période de 7 jours<br/>-
            <span style="margin-left: 2em;"></span>Livraison en soirée proposée sur certaines
            agglomérations<br/>-<span style="margin-left: 2em;"></span>Possibilité de retirer
            le colis à l\'agence.'
        ),
        array('geodis.front.popin.prestation.name.classic.europe.exp',
            'fr',
            'LIVRAISON EXPRESS'
        ),
        array('geodis.front.popin.prestation.name.classic.france.exp',
            'fr',
            'LIVRAISON EXPRESS'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.tel',
            'fr',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.tel.exp',
            'fr',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.web',
            'fr',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.web.exp',
            'fr',
            'ON DEMAND PREMIUM'
        ),
        array('geodis.front.popin.contactDescription.tel',
            'fr',
            'Je renseigne mon numéro de téléphone et/ou mon email.'
        ),
        array('geodis.front.popin.contactDescription.web',
            'fr',
            'Je renseigne mon numéro de téléphone et/ou mon email.'
        ),
        array('geodis.front.popin.submit',
            'fr',
            'Valider mon choix'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.tel',
            'fr',
            'ON DEMAND LIVE'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.tel',
            'fr',
            'GEODIS me contactera par téléphone pour convenir d\'un rendez-vous de livraison.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.tel',
            'fr',
            'Dès que mon envoi aura atteint l\'agence de livraison, GEODIS me contactera par
            téléphone pour convenir du meilleur moment pour me livrer.'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.web',
            'fr',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.web',
            'fr',
            'Je choisis ma journée de livraison, du lundi au vendredi, sur le portail web de GEODIS.'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.web',
            'fr',
            'GEODIS m\'enverra une notification par email ou sms pour planifier ma livraison.
            <br>Je peux choisir le créneau de livraison qui me convient.<br>-
            <span style="margin-left: 2em;"></span>Livraison possible du lundi au vendredi
            sur une période de 7 jours<br>-<span style="margin-left: 2em;"></span>Livraison
            en soirée proposée sur certaines agglomérations<br>-<span style="margin-left: 2em;">
            </span>Possibilité de retirer le colis à l\'agence.'
        ),
        array('geodis.front.popin.option.name.livEtage',
            'fr',
            'Livraison à l\'étage'
        ),
        array('geodis.front.popin.option.name.depotage',
            'fr',
            'Dépotage'
        ),
        array('geodis.front.popin.option.name.miseLieuUtil',
            'fr',
            'Mise en lieu d\'utilisation'
        ),
        array('geodis.front.popin.option.none.label',
            'fr',
            'Aucun service'
        ),
        array('geodis.front.popin.floor.error',
            'fr',
            'Veuillez renseigner le numéro d\'étage.'
        ),
        array('geodis.front.popin.contactInformations',
            'fr',
            'Informations de contact :'
        ),
        array('geodis.front.popin.contactDescription',
            'fr',
            'Je renseigne mon numéro de téléphone et/ou mon email.'
        ),
        array('geodis.front.popin.services.available',
            'fr',
            'Je choisis un service complémentaire : '
        ),
        array('geodis.front.popin.email.placeholder',
            'fr',
            'Email'
        ),
        array('geodis.front.popin.email.error',
            'fr',
            'Veuillez renseigner une adresse mail valide.'
        ),
        array('geodis.front.popin.telephone.placeholder',
            'fr',
            'Téléphone Fixe'
        ),
        array('geodis.front.popin.telephone.error',
            'fr',
            'Veuillez renseigner un numéro de téléphone fixe valide.'
        ),
        array('geodis.front.popin.mobile.placeholder',
            'fr',
            'Téléphone portable'
        ),
        array('geodis.front.popin.mobile.error',
            'fr',
            'Veuillez renseigner un numéro de téléphone mobile valide.'
        ),
        array('geodis.front.popin.floor.placeholder',
            'fr',
            'Numéro d\'étage'
        ),
        array('geodis.front.popin.digicode.placeholder',
            'fr',
            'Code porte'
        ),
        array('geodis.front.popin.option.description.depotage',
            'fr',
            'Ouverture de la palette, reprise de la palette et de l\'emballage extérieur.'
        ),
        array('geodis.front.popin.option.description.livEtage',
            'fr',
            'Livraison d\'envois manipulables par le conducteur seul.<br/>Présence obligatoire
            d\'un ascenseur au-delà de 2 étages.'
        ),
        array('geodis.front.popin.option.description.miseLieuUtil',
            'fr',
            'Livraison dans la pièce que j\'aurai indiquée au conducteur (étage, réserve, sous-sol, …).'
        ),
        array('geodis.front.popin.title.%s',
            'fr',
            '%1$s'
        ),
        array('geodis.front.popin.subtitle.%s',
            'fr',
            '%1$s'
        ),
        array('geodis.front.popin.prestation.name.CALBEMES.MES.RDW',
            'fr',
            'ON DEMAND STANDARD'
        ),
        array('geodis.front.popin.prestation.description.CALBEMES.MES.RDW',
            'fr',
            'Je choisis ma journée de livraison, du lundi au vendredi, sur le portail web de GEODIS.'
        ),
        array('geodis.front.popin.point.distanceKilometers.@',
            'fr',
            '@ km'
        ),
        array('geodis.front.popin.point.distanceMeters.@',
            'fr',
            '@ m'
        ),
        array('geodis.front.popin.address.button',
            'fr',
            'Rechercher'
        ),
        array('geodis.front.popin.point.choose',
            'fr',
            'Choisir'
        ),
        array('geodis.front.popin.point.selected',
            'fr',
            'Choisi'
        ),
        array('geodis.front.popin.point.displayTimetable',
            'fr',
            'Afficher les horaires'
        ),
        array('geodis.front.popin.point.timetable.title.monday',
            'fr',
            'Lundi'
        ),
        array('geodis.front.popin.point.timetable.title.tuesday',
            'fr',
            'Mardi'
        ),
        array('geodis.front.popin.point.timetable.title.wednesday',
            'fr',
            'Mercredi'
        ),
        array('geodis.front.popin.point.timetable.title.thursday',
            'fr',
            'Jeudi'
        ),
        array('geodis.front.popin.point.timetable.title.friday',
            'fr',
            'Vendredi'
        ),
        array('geodis.front.popin.point.timetable.title.saturday',
            'fr',
            'Samedi'
        ),
        array('geodis.front.popin.point.timetable.title.sunday',
            'fr',
            'Dimanche'
        ),
        array('geodis.front.popin.point.timeline.and',
            'fr',
            'et'
        ),
        array('geodis.front.popin.point.timeline.closed',
            'fr',
            'fermé'
        ),
        array('geodis.front.popin.instruction.placeholder',
            'fr',
            'Instructions pour la livraison (facultatif)'
        ),
        array('geodis.front.popin.address.placeholder',
            'fr',
            'Adresse ou code postal'
        ),
        array('geodis.front.popin.action.displayMap',
            'fr',
            'Montrer la carte'
        ),
        array('geodis.front.popin.action.displayList',
            'fr',
            'Afficher la liste'
        ),
        array('geodis.front.popin.prestation.name.classic.france',
            'fr',
            'LIVRAISON STANDARD'
        ),
        array('geodis.front.popin.prestation.name.classic.europe',
            'fr',
            'LIVRAISON STANDARD'
        ),
        array('geodis.front.popin.prestation.description.classic.france',
            'fr',
            'Je suis livré(e) entre 24h et 48h, du lundi au vendredi.'
        ),
        array('geodis.front.popin.prestation.description.classic.europe',
            'fr',
            'Je suis livré(e) à mon domicile entre 48h et 96h.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.france',
            'fr',
            'Dès le départ de ma commande, GEODIS m\'enverra un email ou un sms pour
            suivre l\'acheminement.<br>Les livraisons GEODIS se font dans un délai de 24 à 48 h
            du lundi au vendredi.'
        ),
        array('geodis.front.popin.prestation.longDescription.classic.europe',
            'fr',
            'Je suis livré(e) à mon domicile entre 48h et 96h.'
        ),
        array('geodis.front.popin.prestation.name.rdv.france.exp',
            'fr',
            'Rendez-vous en france'
        ),
        array('geodis.front.popin.prestation.description.rdv.france.exp',
            'fr',
            'Rendez-vous en france (description)'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.france.exp',
            'fr',
            'Rendez-vous en france (description longue)'
        ),
        array('geodis.front.popin.prestation.name.rdv.europe.exp',
            'fr',
            'Nomination en Europe'
        ),
        array('geodis.front.popin.prestation.description.rdv.europe.exp',
            'fr',
            'Nomination en Europe (description)'
        ),
        array('geodis.front.popin.prestation.longDescription.rdv.europe.exp',
            'fr',
            'Nomination en Europe (description longue)'
        ),
        array('geodis.front.popin.requiredEntry',
            'fr',
            '(information obligatoire)'
        ),
        array('geodis.*.*.cron.description',
            'fr',
            'Renseignez le lien ci-dessous, dans votre planificateur CRON, ou l\'ordonnanceur que vous utilisez
            classiquement, afin de récupérer les mises à jour du statut de vos expéditions.'
        ),
        array('geodis.Admin.info.locality.exped.change',
            'fr',
            'La localité de l\'expéditeur a été corrigée par :'
        ),
        array('geodis.Admin.info.locality.desti.change',
            'fr',
            'La localité du destinataire a été corrigée par :'
        ),
        array('geodis.Admin.info.locality.exped.change',
            'en',
            'The location of the sender has been corrected by :'
        ),
        array('geodis.Admin.info.locality.desti.change',
            'en',
            'The recipient\'s location has been corrected by :'
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels',
            'fr',
            'Imprimer les étiquettes'
        ),
        array('geodis.Admin.OrdersGrid.index.action.sendShipments',
            'fr',
            'Transmettre les envois'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.order.checked',
            'fr',
            'Au moins une commande doit être cochée'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.failed.to.find.all.orders',
            'fr',
            'Impossible de trouver toutes les commandes dans la base de données'
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.failed',
            'fr',
            'L’envoi d’une ou plusieurs commandes n’a pas été créé. Veuillez créer les envois de ces commandes pour imprimer les étiquettes : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.sendShipments.failed',
            'fr',
            'Un ou plusieurs envois n’ont pas pu être transmis car n’ont pas été créés ou les étiquettes n’ont pas été imprimées ou ont déjà été transmis. Veuillez vérifier les envois non transmis : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.print.ws.answer',
            'fr',
            'Aucune réponse du Web Service GEODIS reçue'
        ),
        array('geodis.Admin.OrdersGrid.index.action.error.no.send.shipments.ws.answer',
            'fr',
            'Aucune réponse du Web Service GEODIS reçue'
        ),
        array('geodis.Admin.OrdersGrid.index.action.success.shipments',
            'fr',
            'Les envois suivant ont été transmis avec succès : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.form.label',
            'fr',
            'Vos étiquettes sont prêtes : '
        ),
        array('geodis.Admin.OrdersGrid.index.action.printLabels.form.download.btn.label',
            'fr',
            'Télécharger'
        ),
        array('geodis.Admin.OrdersGrid.index.action.downloadFile.error.data.empty',
            'fr',
            'Le nom du fichier ou son contenu est vide'
        )
    );

    public static function getInstance()
    {
        return new GeodisDbTranslation();
    }

    public function initBack()
    {
        foreach (Language::getLanguages() as $lang) {
            $id_lang = $lang['id_lang'];
            if ($lang['iso_code'] == 'fr') {
                foreach ($this->getTranslationBackFr() as $line) {
                    list($key, $lang, $value) = $line;
                    GeodisTranslation::set($key, $id_lang, $value, true, false);
                }
                unset($key, $value, $line);
            } elseif ($lang['iso_code'] == 'en') {
                foreach ($this->getTranslationBackEn() as $line) {
                    list($key, $lang, $value) = $line;
                    GeodisTranslation::set($key, $id_lang, $value, true, false);
                }
                unset($key, $value, $line);
            }
        }
        unset($id_lang, $lang);
        return true;
    }

    public function initFront()
    {
        foreach (Language::getLanguages() as $lang) {
            $id_lang = $lang['id_lang'];
            if ($lang['iso_code'] == 'fr') {
                foreach ($this->getTranslationFrontFr() as $line) {
                    list($key, $lang, $value) = $line;
                    GeodisTranslation::set($key, $id_lang, $value, true, false);
                }
                unset($key, $value, $line);
            } elseif ($lang['iso_code'] == 'en') {
                foreach ($this->getTranslationFrontEn() as $line) {
                    list($key, $lang, $value) = $line;
                    GeodisTranslation::set($key, $id_lang, $value, true, false);
                }
                unset($key, $value, $line);
            }
        }
        unset($id_lang, $lang);
        return true;
    }

    protected function getTranslationBackFr()
    {
        foreach ($this->translations_back_fr as $translationBackFr) {
            yield $translationBackFr;
        }
    }

    protected function getTranslationBackEn()
    {
        foreach ($this->translations_back_en as $translationBackEn) {
            yield $translationBackEn;
        }
    }

    protected function getTranslationFrontFr()
    {
        foreach ($this->translations_front_fr as $translationFrontFr) {
            yield $translationFrontFr;
        }
    }

    protected function getTranslationFrontEn()
    {
        foreach ($this->translations_front_en as $translationFrontEn) {
            yield $translationFrontEn;
        }
    }
}
