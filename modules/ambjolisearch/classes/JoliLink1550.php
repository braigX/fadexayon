<?php
/**
 *   AmbJoliSearch Module : Search for prestashop
 *
 *    @author    Ambris Informatique
 *    @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
 *    @license   Licensed under the EUPL-1.2-or-later
 *
 *    @module     Advanced Search (AmbJoliSearch)
 *
 *    @file        jolilink1550.php
 *
 *    @subject     core class Link decorator
 *    Support by mail: support@ambris.com
 */
class JoliLink extends JoliLinkCore
{
    public function getModuleLink(
        $module,
        $controller = 'default',
        array $params = [],
        $ssl = false,
        $id_lang = null
    ) {
        return $this->myGetModuleLink($module, $controller, $params, $ssl, $id_lang, null);
    }

    public function getAmbJolisearchLink(
        $controller = 'default',
        $alias = null,
        array $params = [],
        $ssl = null,
        $id_lang = null,
        $id_shop = null
    ) {
        return $this->myGetModuleLink('ambjolisearch', $controller, $params, $ssl, $id_lang, $id_shop);
    }
}
