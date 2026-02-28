<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_ClassLoader.php';

// HTMLTemplate<NAME>.
// HTMLTemplate Class located in classes/pdf/HTMLTemplate.php
// See Instantiation in /classes/pdf/PDF.php:62
class HTMLTemplateTNTOfficielManifest extends HTMLTemplate
{
    public $custom_model;

    public function __construct($custom_object, $smarty/*, $send_bulk_flag = null*/)
    {
        TNTOfficiel_Logstack::log();

        $this->custom_model = $custom_object;

        $this->title = HTMLTemplateTNTOfficielManifest::l('DOCUMENT MANIFESTE');
        //$this->date = '';

        /** @var \Smarty */
        $this->smarty = $smarty;

        /** @var \Shop */
        $this->shop = TNTOfficielAccount::getPSShopByID((int)Context::getContext()->shop->id);
    }

    /**
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        TNTOfficiel_Logstack::log();

        $this->smarty->assign(
            array(
                'manifestData' => $this->custom_model,
            )
        );

        return $this->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/manifest/custom_template_header.tpl'
        );
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        TNTOfficiel_Logstack::log();

        $this->smarty->assign(
            array(
                'manifestData' => $this->custom_model,
            )
        );

        return $this->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/manifest/custom_template_content.tpl'
        );
    }

    /**
     * Returns the template's HTML footer.
     *
     * @return string HTML footer
     */
    public function getFooter()
    {
        TNTOfficiel_Logstack::log();

        return $this->smarty->fetch(
            TNTOfficiel::getDirModule('template') . 'admin/manifest/custom_template_footer.tpl'
        );
    }

    /**
     * Returns the template's HTML pagination block.
     *
     * @return string HTML pagination block
     */
    public function getPagination()
    {
        TNTOfficiel_Logstack::log();

        return '';
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        TNTOfficiel_Logstack::log();

        return 'Manifeste.pdf';
    }

    /**
     * Returns the template filename when using bulk rendering.
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        TNTOfficiel_Logstack::log();

        return 'Manifeste.pdf';
    }
}
