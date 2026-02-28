<?php
/**
 *   AmbJoliSearch Module : Search for prestashop
 *
 *   @author    Ambris Informatique
 *   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
 *   @license   Licensed under the EUPL-1.2-or-later
 *
 *   @module     Advanced search (AmbJoliSearch)
 *
 *   @file       AmbJolisearchModuleProxy.php
 *   Support by mail: support@ambris.com
 */
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    abstract class AmbJolisearchModuleProxy extends AmbJolisearchModule implements PrestaShop\PrestaShop\Core\Module\WidgetInterface
    {
    }
} else {
    abstract class AmbJolisearchModuleProxy extends AmbJolisearchModule
    {
    }
}
