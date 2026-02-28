<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminEcSeoController extends ModuleAdminController
{

    public function __construct()
    {
        if (Tools::getValue('token') && 'yes' === Tools::getValue('getMyIP')) {
            exit(Tools::getRemoteAddr());
        }
        parent::__construct();
    }

    public function display()
    {
        $PS_SHOP_ENABLE = Configuration::get('PS_SHOP_ENABLE');
        if (!$PS_SHOP_ENABLE) {
            $ips_raw = array();
            $ips_raw[] = Tools::getRemoteAddr() ?: null; //remote address
            $ips_raw[] = filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRING) ?: null; //server address
            $ips_raw[] = Tools::file_get_contents($this->context->link->getAdminLink('AdminEcSeo', true, null, array('getMyIP' => 'yes')));
            $ips_raw[] = Tools::file_get_contents('http://79.137.79.204/getMyIP.php?motdepasse='.md5(gmdate('YmdH')));
            $ips = array_unique(array_filter($ips_raw));
            sort($ips);
        
            // get all configured maintenance IPs
            $ps_maintenance_ip_multishop = Configuration::getMultiShopValues('PS_MAINTENANCE_IP');

            // complete configuration for each shop
            foreach ($ps_maintenance_ip_multishop as $id_shop => $ps_maintenance_ip) {
                if (true && $this->context->shop->id !== $id_shop) {
                    continue;
                }
                $id_shop_group = Shop::getGroupFromShop($id_shop);
                $maintenance_ips_raw = $ps_maintenance_ip ? explode(',', $ps_maintenance_ip) : array();
                $maintenance_ips = array_unique(array_map('trim', $maintenance_ips_raw));
                foreach ($ips as $ip) {
                    if (!in_array($ip, $maintenance_ips)) {
                        $maintenance_ips[] = $ip;
                    } 
                }
                $ps_maintenance_ip_new = implode(',', $maintenance_ips);
                if ($ps_maintenance_ip_new != $ps_maintenance_ip) {
                    Configuration::updateValue('PS_MAINTENANCE_IP', $ps_maintenance_ip_new, false, $id_shop_group, $id_shop);
                }
            }
        }

        $mod = 'ec_seo';
        Tools::redirectAdmin('?controller=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $mod . '&module_name=' . $mod);
    }
}
