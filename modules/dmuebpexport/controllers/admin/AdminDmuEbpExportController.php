<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2024 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminDmuEbpExportController extends ModuleAdminController
{
    // Permet de ne pas exporter en CSV et afficher les infos
    protected $mode_debug = false;

    // Permet de n'exporter qu'une commande définie (id_order)
    protected $test_order = 0;
    protected $test_slip = 0;

    // Statistiques
    protected $nb_ok = 0;
    protected $nb_corrected = 0;
    protected $nb_error = 0;

    // Comptes comptables
    protected $dmuebp_export;
    protected $dmuebp_journal;
    protected $dmuebp_compte_client;
    protected $dmuebp_compte_produits_ttc;
    protected $dmuebp_compte_produits_ht;
    protected $dmuebp_compte_transport_ttc;
    protected $dmuebp_compte_transport_ht;
    protected $dmuebp_compte_rrr_ttc;
    protected $dmuebp_compte_rrr_ht;
    protected $dmuebp_compte_emballage_ttc;
    protected $dmuebp_compte_emballage_ht;
    protected $dmuebp_compte_payment;
    protected $dmuebp_compte_customers;
    protected $dmuebp_compte_produits_tva_name;
    protected $dmuebp_compte_tva_name;
    protected $dmuebp_compte_client_ligne;
    protected $id_lang;
    protected $iso_lang;

    public function __construct()
    {
        $this->module = 'dmuebpexport';
        $this->className = 'DmuEbpExportModel';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        parent::__construct();
    }

    public function postProcess()
    {
        $csv_output = '';

        if (_PS_MODE_DEV_ == false) {
            $this->mode_debug = false;
        }

        if (Tools::isSubmit('submitExport')) {
            $encoding = (int) Configuration::get('DMUEBP_EXPORT_ENCODING');

            if (true == $this->mode_debug) {
                if (1 == $encoding) {
                    header('Content-Type: text/html; charset=iso-8859-1');
                } else {
                    header('Content-Type: text/html; charset=utf-8');
                }
            }

            $this->emptyReport();

            Configuration::updateValue('DMUEBP_NB_OK', $this->nb_ok);
            Configuration::updateValue('DMUEBP_NB_CORRECTED', $this->nb_corrected);
            Configuration::updateValue('DMUEBP_NB_ERROR', $this->nb_error);

            // On assigne les comptes comptables
            $this->assignComptesExport();

            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');

            if (0 == $this->dmuebp_export) {
                $filename = 'export_comptable_' . $date_from . '_' . $date_to . '.csv';
            } else {
                $filename = 'export_ebp_' . $date_from . '_' . $date_to . '.csv';
            }

            // Ligne d'En-Tête
            if ('fr' == $this->iso_lang) {
                if (0 == $this->dmuebp_export) {
                    $csv_output .= 'Date;Journal;ID Commande;Ref Commande;Pays;ID Client;Client;Société;Compte;' .
                                    "Facture;Libelle;Type de paiement;Montant;Debit;Credit;ID de transaction\r\n";
                } else {
                    $csv_output .= "Date;Journal;Compte;Piece;Libelle;Debit;Credit;Devise\r\n";
                }
            } else {
                if (0 == $this->dmuebp_export) {
                    $csv_output .= 'Date;Journal;ID Order;Ref Order;Country;ID Client;Client;Company;Account;' .
                                    "Invoice;Label;Payment type;Amount;Debit;Credit;Transaction ID\r\n";
                } else {
                    $csv_output .= "Date;Journal;Account;Document;Label;Debit;Credit;Currency\r\n";
                }
            }

            $this->exportInvoices($csv_output);

            $this->exportSlips($csv_output);

            Configuration::updateValue('DMUEBP_NB_OK', $this->nb_ok);
            Configuration::updateValue('DMUEBP_NB_CORRECTED', $this->nb_corrected);
            Configuration::updateValue('DMUEBP_NB_ERROR', $this->nb_error);

            if (1 == $encoding) {
                $csv_output = iconv('UTF-8', 'ISO-8859-1//IGNORE', $csv_output);
            }

            if (false == $this->mode_debug) {
                @mkdir(_PS_MODULE_DIR_ . 'dmuebpexport/exports/');
                @chmod(_PS_MODULE_DIR_ . 'dmuebpexport/exports/', 0755);

                if (!file_put_contents(_PS_MODULE_DIR_ . 'dmuebpexport/exports/' . $filename, $csv_output)) {
                    exit('Une erreur est survenue, vérifiez que le dossier ' . _PS_MODULE_DIR_ .
                        "dmuebpexport/exports/ existe et que le droits d'écriture sont bons.");
                }

                Configuration::updateValue('DMUEBP_LAST_EXPORT', $filename);

                Tools::redirectAdmin('index.php?controller=AdminDmuEbpExport&token=' .
                    Tools::getValue('token') . '&date_from=' . Tools::getValue('date_from') .
                    '&date_to=' . Tools::getValue('date_to'));
            }

            exit;
        }

        return '';
    }

    public function assignComptesExport()
    {
        // FORMAT EXPORT
        $this->dmuebp_export = (int) Configuration::get('DMUEBP_EXPORT');
        if (empty($this->dmuebp_export)) {
            $this->dmuebp_export = 0; // Format export comptable par defaut
        }

        // DEFINITION DES NUMEROS DE COMPTE
        $this->dmuebp_journal = Configuration::get('DMUEBP_JOURNAL');
        if (empty($this->dmuebp_journal)) {
            $this->dmuebp_journal = 'VE';
        }

        $this->dmuebp_compte_client = Configuration::get('DMUEBP_COMPTE_CLIENT');
        if (empty($this->dmuebp_compte_client)) {
            $this->dmuebp_compte_client = 411;
        }

        $this->dmuebp_compte_produits_ttc = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');
        if (empty($this->dmuebp_compte_produits_ttc)) {
            $this->dmuebp_compte_produits_ttc = 702;
        }

        $this->dmuebp_compte_produits_ht = Configuration::get('DMUEBP_COMPTE_PRODUIT_HT');
        if (empty($this->dmuebp_compte_produits_ht)) {
            $this->dmuebp_compte_produits_ht = 701;
        }

        $this->dmuebp_compte_transport_ttc = Configuration::get('DMUEBP_COMPTE_TRANSPORT_TTC');
        if (empty($this->dmuebp_compte_transport_ttc)) {
            $this->dmuebp_compte_transport_ttc = 706;
        }

        $this->dmuebp_compte_transport_ht = Configuration::get('DMUEBP_COMPTE_TRANSPORT_HT');
        if (empty($this->dmuebp_compte_transport_ht)) {
            $this->dmuebp_compte_transport_ht = 706;
        }

        $this->dmuebp_compte_rrr_ttc = Configuration::get('DMUEBP_COMPTE_RRR_TTC');
        if (empty($this->dmuebp_compte_rrr_ttc)) {
            $this->dmuebp_compte_rrr_ttc = 609;
        }

        $this->dmuebp_compte_rrr_ht = Configuration::get('DMUEBP_COMPTE_RRR_HT');
        if (empty($this->dmuebp_compte_rrr_ht)) {
            $this->dmuebp_compte_rrr_ht = 609;
        }

        $this->dmuebp_compte_emballage_ttc = Configuration::get('DMUEBP_COMPTE_EMBALLAGE_TTC');
        if (empty($this->dmuebp_compte_emballage_ttc)) {
            $this->dmuebp_compte_emballage_ttc = 706;
        }

        $this->dmuebp_compte_emballage_ht = Configuration::get('DMUEBP_COMPTE_EMBALLAGE_HT');
        if (empty($this->dmuebp_compte_emballage_ht)) {
            $this->dmuebp_compte_emballage_ht = 706;
        }

        $this->dmuebp_compte_payment = json_decode(Configuration::get('DMUEBP_COMPTE_PAYMENT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id), true);
        $this->dmuebp_compte_customers = json_decode(Configuration::get('DMUEBP_COMPTE_CUSTOMERS', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id), true);

        $taxs = TaxRulesGroup::getTaxRulesGroups();

        foreach ($taxs as $t) {
            $taxes = DmuEbpExportModel::getModifiedTaxRuleCountry($t['id_tax_rules_group']);
            $id_default_country = Configuration::get('PS_COUNTRY_DEFAULT');

            // Récupération des associations si l'id a été changé par PrestaShop
            $associated_tax_rules_group = DmuEbpExportModel::getTaxRulesGroupAssociation($t['id_tax_rules_group']);

            $this->dmuebp_compte_produits_tva_name = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'];
            $this->{$this->dmuebp_compte_produits_tva_name} =
                Configuration::get($this->dmuebp_compte_produits_tva_name);

            if (empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                $this->{$this->dmuebp_compte_produits_tva_name} = 0;
            }

            if (!empty($associated_tax_rules_group)) {
                foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                    $associated_name = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $old_tax_rule_group['old_tax_rules_group'];
                    $this->{$associated_name} = Configuration::get($this->dmuebp_compte_produits_tva_name);
                    if (empty($this->{$associated_name})) {
                        $this->{$associated_name} = 0;
                    }
                }
            }

            // Valeur pays par défaut
            $this->dmuebp_compte_produits_tva_name =
                'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $id_default_country;
            $this->{$this->dmuebp_compte_produits_tva_name} =
                Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);
            if (empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                $this->{$this->dmuebp_compte_produits_tva_name} = $this->dmuebp_compte_produits_ttc;
            }

            if (!empty($associated_tax_rules_group)) {
                foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                    $associated_name = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $old_tax_rule_group['old_tax_rules_group'] . '_' . $id_default_country;
                    $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);
                    if (empty($this->{$associated_name})) {
                        $this->{$associated_name} = $this->dmuebp_compte_produits_ttc;
                    }
                }
            }

            foreach ($taxes as $tax2) {
                $this->dmuebp_compte_produits_tva_name =
                    'DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'];
                $this->{$this->dmuebp_compte_produits_tva_name} =
                    Configuration::get($this->dmuebp_compte_produits_tva_name);
                if (empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                    $this->{$this->dmuebp_compte_produits_tva_name} =
                        Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);
                    if (empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                        $this->{$this->dmuebp_compte_produits_tva_name} = $this->dmuebp_compte_produits_ttc;
                    }
                }

                if (!empty($associated_tax_rules_group)) {
                    foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                        $associated_name = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $old_tax_rule_group['old_tax_rules_group'] . '_' . $tax2['id_country'];
                        $this->{$associated_name} = Configuration::get($this->dmuebp_compte_produits_tva_name);
                        if (empty($this->{$associated_name})) {
                            $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_PRODUITS_TVA_' . $t['id_tax_rules_group']);
                            if (empty($this->{$associated_name})) {
                                $this->{$associated_name} = $this->dmuebp_compte_produits_ttc;
                            }
                        }
                    }
                }
            }

            $this->dmuebp_compte_tva_name = 'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'];
            $this->{$this->dmuebp_compte_tva_name} = Configuration::get($this->dmuebp_compte_tva_name);
            if (empty($this->{$this->dmuebp_compte_tva_name})) {
                $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
            }

            if (!empty($associated_tax_rules_group)) {
                foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                    $associated_name = 'DMUEBP_COMPTE_TVA_' . $old_tax_rule_group['old_tax_rules_group'];
                    $this->{$associated_name} = Configuration::get($this->dmuebp_compte_tva_name);
                    if (empty($this->{$associated_name})) {
                        $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }
                }
            }

            // Valeur pays par défaut
            $this->dmuebp_compte_tva_name =
                'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $id_default_country;
            $this->{$this->dmuebp_compte_tva_name} =
                Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);
            if (empty($this->{$this->dmuebp_compte_tva_name})) {
                $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
            }

            if (!empty($associated_tax_rules_group)) {
                foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                    $associated_name = 'DMUEBP_COMPTE_TVA_' . $old_tax_rule_group['old_tax_rules_group'] . '_' . $id_default_country;
                    $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);
                    if (empty($this->{$associated_name})) {
                        $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }
                }
            }

            foreach ($taxes as $tax2) {
                $this->dmuebp_compte_tva_name =
                    'DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group'] . '_' . $tax2['id_country'];
                $this->{$this->dmuebp_compte_tva_name} =
                    Configuration::get($this->dmuebp_compte_tva_name);
                if (empty($this->{$this->dmuebp_compte_tva_name})) {
                    $this->{$this->dmuebp_compte_tva_name} =
                        Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);
                    if (empty($this->{$this->dmuebp_compte_tva_name})) {
                        $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }
                }

                if (!empty($associated_tax_rules_group)) {
                    foreach ($associated_tax_rules_group as $old_tax_rule_group) {
                        $associated_name = 'DMUEBP_COMPTE_TVA_' . $old_tax_rule_group['old_tax_rules_group'] . '_' . $tax2['id_country'];
                        $this->{$associated_name} = Configuration::get($this->dmuebp_compte_tva_name);
                        if (empty($this->{$associated_name})) {
                            $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_TVA_' . $t['id_tax_rules_group']);
                            if (empty($this->{$associated_name})) {
                                $this->{$associated_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                            }
                        }
                    }
                }
            }
        }

        $this->id_lang = Context::getContext()->cookie->id_lang;
        $this->iso_lang = Language::getIsoById($this->id_lang);
    }

    public function exportInvoices(&$csv_output)
    {
        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');

        // RECUPERATION FACTURES
        $invoices = DmuEbpExportModel::getOrdersIdInvoiceByDate($date_from, $date_to, null, 'invoice');
        if (!empty($this->test_order)) {
            $invoices = [$this->test_order];
        }

        foreach ($invoices as $id_order) {
            $order_obj = new Order($id_order);

            $sql = 'SELECT id_order_invoice
                    FROM `' . _DB_PREFIX_ . 'order_invoice`
                    WHERE id_order = ' . (int) $id_order;
            if ($id_order_invoice = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                $id_order_invoice = (int) $id_order_invoice;
            }

            $orderinv_obj = new OrderInvoice($id_order_invoice);

            // Doit-on exporter les factures à 0 euros ?
            $export_zero = (bool) Configuration::get('DMUEBP_EXORTZERO');

            if ($export_zero || (!$export_zero && $orderinv_obj->total_paid_tax_incl > 0)) {
                if (!isset($order_obj->round_mode) || empty($order_obj->round_mode)) {
                    $order_obj->round_mode = (int) Configuration::get('PS_PRICE_ROUND_MODE');
                }
                if (!isset($order_obj->round_type) || empty($order_obj->round_type)) {
                    $order_obj->round_type = (int) Configuration::get('PS_ROUND_TYPE');
                }
                $address_obj = new Address($order_obj->id_address_delivery);
                $pays = $address_obj->country;

                $lignes_ecriture = [];

                // ** ** Vérification qu'on a le bon numéro de facture ! (certains modules foutent la merde)
                $sql = 'SELECT number
                        FROM `' . _DB_PREFIX_ . 'order_invoice`
                        WHERE id_order = ' . (int) $id_order;
                if ($inv_number = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                    $order_obj->invoice_number = (int) $inv_number;
                }
                // ** **

                $if_tva = true; // Verifie si c'est une commande soumise à la TVA ou non
                $order_total_wt = $order_obj->getTotalProductsWithoutTaxes();
                $order_total = $order_obj->getTotalProductsWithTaxes();
                if ($order_total_wt == $order_total) {
                    $if_tva = false; // Si aucune taxe trouvée pour cette commande
                }

                $num_facture = Configuration::get('PS_INVOICE_PREFIX', $this->id_lang)
                . sprintf('%06d', $order_obj->invoice_number);
                $products = $order_obj->getProducts();

                /* ** ** Date réelle de la facture ** ** */
                if (!$order_obj->invoice_date) {
                    $sql = 'SELECT date_add
                            FROM `' . _DB_PREFIX_ . 'order_invoice`
                            WHERE id_order = ' . (int) $id_order;
                    if ($date_add = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                        $order_obj->invoice_date = $date_add;
                    } else {
                        $sql = 'SELECT date_add
                                FROM `' . _DB_PREFIX_ . 'order_invoice`
                                WHERE number = ' . (int) $order_obj->invoice_number;
                        if ($date_add = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                            $order_obj->invoice_date = $date_add;
                        }
                    }
                }

                // Combien de produits au total dans la commande ?
                $total_produits_ttc = 0;
                $total_produits_ht = 0;
                foreach ($products as $p) {
                    $total_produits_ttc += (float) $p['total_price_tax_incl'];
                    $total_produits_ht += Tools::ps_round($p['total_price_tax_excl'], 2, $order_obj->round_mode);
                }

                $cart_rules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT *
                FROM `' . _DB_PREFIX_ . 'order_cart_rule`
                WHERE `id_order` = ' . (int) $id_order);

                foreach ($cart_rules as $cr) {
                    if ($cr['free_shipping']) {
                        $order_obj->total_discounts_tax_incl -= $order_obj->total_shipping_tax_incl;
                        $order_obj->total_discounts_tax_excl -= $order_obj->total_shipping_tax_excl;
                        $order_obj->total_shipping_tax_incl = 0;
                        $order_obj->total_shipping_tax_excl = 0;

                        $orderinv_obj->total_discount_tax_incl -= $orderinv_obj->total_shipping_tax_incl;
                        $orderinv_obj->total_discount_tax_excl -= $orderinv_obj->total_shipping_tax_excl;
                        $orderinv_obj->total_shipping_tax_incl = 0;
                        $orderinv_obj->total_shipping_tax_excl = 0;
                    }
                }

                // On va calculer les différentes TVA
                $taxs = [];
                $taxs_discount = [];
                $total_produits_ht_tva = [];

                // Montant total de TVA ventilé
                $total_tva = 0;

                foreach ($order_obj->getProductTaxesDetails() as $p) {
                    $prix_tva = $p['total_amount'];

                    $order_detail_obj = new OrderDetail((int) $p['id_order_detail']);
                    $order_detail_obj->id_tax_rules_group = isset($order_detail_obj->id_tax_rules_group) ?
                        $order_detail_obj->id_tax_rules_group : 0;
                    $key = $order_detail_obj->id_tax_rules_group;

                    if ($prix_tva > 0) {
                        $total_tva += round($prix_tva, 2);
                        if (isset($taxs[$key])) {
                            $taxs[$key] += $prix_tva;
                        } else {
                            $taxs[$key] = $prix_tva;
                        }
                    }
                }

                foreach ($products as $p) {
                    $order_detail_obj = new OrderDetail((int) $p['id_order_detail']);
                    $order_detail_obj->id_tax_rules_group = isset($order_detail_obj->id_tax_rules_group) ?
                        $order_detail_obj->id_tax_rules_group : 0;
                    $key = $order_detail_obj->id_tax_rules_group;
                    if (!$key) {
                        $key = (int) Product::getIdTaxRulesGroupByIdProduct($p['product_id'], $this->context);
                    }

                    $unit_price_te = $p['unit_price_tax_excl'];
                    $qty = $p['product_quantity'];

                    $total_price_te = 0;
                    switch ($order_obj->round_type) {
                        case Order::ROUND_ITEM:
                            $total_price_te = Tools::ps_round($unit_price_te, 3, $order_obj->round_mode);
                            $total_price_te *= $qty;
                            break;
                        case Order::ROUND_LINE:
                            $total_price_te = Tools::ps_round($unit_price_te * $qty, 3, $order_obj->round_mode);
                            break;
                    }

                    if (!isset($total_produits_ht_tva[$key])) {
                        $total_produits_ht_tva[$key] = $total_price_te;
                    } else {
                        $total_produits_ht_tva[$key] += $total_price_te;
                    }
                }

                // Arrondi final
                if (Order::ROUND_TOTAL == $order_obj->round_type) {
                    foreach ($total_produits_ht_tva as $key => &$amount) {
                        $amount = Tools::ps_round($amount, 2, $order_obj->round_mode);
                    }
                }

                // TVA de la réduction
                if ($orderinv_obj->total_discount_tax_incl > 0) {
                    $prix_tva_discount = $orderinv_obj->total_discount_tax_incl - $orderinv_obj->total_discount_tax_excl;
                    $taxs_discount = $prix_tva_discount;
                }

                // Montant TVA frais de port
                $tva_shipping = 0;
                if ($orderinv_obj->total_shipping_tax_incl > 0 && $orderinv_obj->total_shipping_tax_incl != $orderinv_obj->total_discount_tax_incl) {
                    $tva_shipping = $orderinv_obj->total_shipping_tax_incl - $orderinv_obj->total_shipping_tax_excl;
                    $total_tva += round($tva_shipping, 2);
                }

                // Calcul du Total TVA
                $total_tva_expected = round($orderinv_obj->total_paid_tax_incl - $orderinv_obj->total_paid_tax_excl, 2);
                $tva_options = 0;
                if ($total_tva < $total_tva_expected) {
                    $tva_options = $total_tva_expected - $total_tva;
                }

                // Ajout des IDs de Transaction
                $transaction_id = null;
                $sql = 'SELECT *
                        FROM `' . _DB_PREFIX_ . 'order_invoice_payment` oip
                        INNER JOIN `' . _DB_PREFIX_ . 'order_payment` op
                            ON op.id_order_payment = oip.id_order_payment
                        WHERE oip.id_order = ' . (int) $order_obj->id . ' 
                        ORDER BY op.id_order_payment ASC';
                if ($payments = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                    $payments_list = [];
                    foreach ($payments as $payment) {
                        $payments_list[] = $payment['transaction_id'];
                    }
                    if (!empty($payments_list)) {
                        $transaction_id = implode(',', $payments_list);
                    }
                }

                // 1 - Ligne total TTC commande (compte client/mode de paiement)
                $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_client;
                $each_payment = (int) Configuration::get('DMUEBP_EACHPAYMENT');

                if (1 == $each_payment) {
                    // Version 3.0.1 : Une ligne par mode de paiement
                    foreach ($payments as $payment_array) {
                        $payment = DmuEbpExportModel::getPaymentModeSimpleName($payment_array['payment_method']);
                        $payment = Tools::strtoupper(trim(Tools::replaceAccentedChars($payment)));
                        $payment = str_replace([' ', '[', ']'], ['_', '_', '_'], $payment);

                        if (isset($this->dmuebp_compte_payment[$payment]) && !empty($this->dmuebp_compte_payment[$payment])) {
                            $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_payment[$payment];
                        }

                        if (isset($this->dmuebp_compte_customers[$order_obj->id_customer])
                            && !empty($this->dmuebp_compte_customers[$order_obj->id_customer])) {
                            $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_customers[$order_obj->id_customer];
                        }

                        $total = Tools::ps_round($payment_array['amount'], 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->dmuebp_compte_client_ligne,
                            'label' => $this->l('Order') . ' ' . str_pad($id_order, 6, '0', STR_PAD_LEFT) . ' ' . $this->l('by') . ' '
                                        . $this->getCustomerNameByOrder($order_obj),
                            'debit' => number_format($total, 2, '.', ''),
                            'payment' => $payment_array['payment_method'],
                        ];
                    }
                } else {
                    $payment_array = array_shift($payments);

                    $payment = DmuEbpExportModel::getPaymentModeSimpleName($order_obj->payment);
                    $payment = Tools::strtoupper(trim(Tools::replaceAccentedChars($payment)));
                    $payment = str_replace([' ', '[', ']'], ['_', '_', '_'], $payment);

                    if (isset($this->dmuebp_compte_payment[$payment]) && !empty($this->dmuebp_compte_payment[$payment])) {
                        $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_payment[$payment];
                    }
                    if (isset($this->dmuebp_compte_customers[$order_obj->id_customer])
                        && !empty($this->dmuebp_compte_customers[$order_obj->id_customer])) {
                        $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_customers[$order_obj->id_customer];
                    }

                    $total = Tools::ps_round($orderinv_obj->total_paid_tax_incl, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $this->dmuebp_compte_client_ligne,
                        'label' => $this->l('Order') . ' ' . str_pad($id_order, 6, '0', STR_PAD_LEFT) . ' ' . $this->l('by') . ' '
                                    . $this->getCustomerNameByOrder($order_obj),
                        'debit' => number_format($total, 2, '.', ''),
                        'payment' => $payment_array['payment_method'],
                    ];
                }

                // 2 - Lignes de produits
                if (0 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
                    // Personnalisation par TVA
                    foreach ($total_produits_ht_tva as $tax_rate => $amount) {
                        $tax_rule = new TaxRulesGroup($tax_rate);
                        if ($if_tva) {
                            $this->dmuebp_compte_produits_tva_name =
                                'DMUEBP_COMPTE_PRODUITS_TVA_' . $tax_rate . '_' . $address_obj->id_country;

                            if (!empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                                $compte = $this->{$this->dmuebp_compte_produits_tva_name};
                            } else {
                                $compte = $this->dmuebp_compte_produits_ttc;
                            }

                            $amount_round = Tools::ps_round($amount, 2, $order_obj->round_mode);
                            $lignes_ecriture[] = [
                                'compte' => $compte,
                                'label' => $this->l('Products') . ' ' . $this->l('without tax')
                                . ' [' . $tax_rule->name . ']',
                                'credit' => number_format($amount_round, 2, '.', ''), ];
                        } else {
                            $amount_round = Tools::ps_round($amount, 2, $order_obj->round_mode);
                            $lignes_ecriture[] = [
                                'compte' => Configuration::get('DMUEBP_COMPTE_PRODUIT_HT', null, 0, 0),
                                'label' => $this->l('Products') . ' ' . $this->l('without tax'),
                                'credit' => number_format($amount_round, 2, '.', ''), ];
                        }
                    }
                } else {
                    // Personnalisation par catégorie principale
                    foreach ($products as $p) {
                        $data = DmuEbpExportModel::getProductAccountingInfos($p['id_product']);

                        if ($if_tva) {
                            // Si commande TTC
                            if ('' == trim($data[0]['accounting_vat'])) {
                                $dmuebp_category_ttc = json_decode(Configuration::get('DMUEBP_CATEGORY_TTC'), true);
                                $account_value = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');
                                // Priorité au numéro de compte de la catégorie principale
                                $dmuebp_cptva = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $p['id_tax_rules_group'];
                                if ('' != Configuration::get($dmuebp_cptva)) {
                                    $account_value = Configuration::get($dmuebp_cptva);
                                }
                                if (isset($dmuebp_category_ttc[$p['id_category_default']])
                                    && '' != $dmuebp_category_ttc[$p['id_category_default']]) {
                                    $account_value = $dmuebp_category_ttc[$p['id_category_default']];
                                }
                            } else {
                                $account_value = $data[0]['accounting_vat'];
                            }
                        } else {
                            // Si commande HT
                            if ('' == trim($data[0]['accounting_no_vat'])) {
                                $dmuebp_category_ht = json_decode(Configuration::get('DMUEBP_CATEGORY_HT'), true);
                                $account_value = Configuration::get('DMUEBP_COMPTE_PRODUIT_HT');
                                // Si numéro de compte pour la categorie
                                if (isset($dmuebp_category_ht[$p['id_category_default']])
                                    && '' != $dmuebp_category_ht[$p['id_category_default']]) {
                                    $account_value = $dmuebp_category_ht[$p['id_category_default']];
                                }
                            } else {
                                $account_value = $data[0]['accounting_no_vat'];
                            }
                        }

                        $pdt = new Product($p['product_id']);

                        // $tax_rule = new TaxRulesGroup($pdt->id_tax_rules_group);
                        $order_detail_obj = new OrderDetail((int) $p['id_order_detail']);
                        $order_detail_obj->id_tax_rules_group = isset($order_detail_obj->id_tax_rules_group) ?
                            $order_detail_obj->id_tax_rules_group : 0;
                        $key = $order_detail_obj->id_tax_rules_group;
                        $tax_rule = new TaxRulesGroup($key);

                        $total = Tools::ps_round($p['total_price_tax_excl'], 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $account_value,
                            'label' => $pdt->name[$this->context->language->id] . ' '
                                        . $this->l('without tax') . ' ['
                                        . $tax_rule->name . ']',
                            'credit' => number_format($total, 2, '.', ''), ];
                    }
                }

                // 3 - Lignes reductions
                if ($orderinv_obj->total_discount_tax_excl > 0) {
                    $account = $this->dmuebp_compte_rrr_ht;
                    if ($if_tva) {
                        $account = $this->dmuebp_compte_rrr_ttc;
                    }
                    $total = Tools::ps_round($orderinv_obj->total_discount_tax_excl, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $account,
                        'label' => $this->l('Reductions'),
                        'debit' => number_format($total, 2, '.', ''), ];
                }

                // 4 - Lignes total taxes réduction
                // Version 3.0.1 : Les TVA sont déduites directement au niveau des produits

                /*if (isset($taxs_discount) && !empty($taxs_discount)) {
                    $amount_round = Tools::ps_round($taxs_discount, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                            'compte' => Configuration::get('DMUEBP_COMPTE_TAX'),
                            'label' => $this->l('Reductions VAT'),
                            'debit' => number_format($amount_round, 2, '.', ''), ];
                }*/

                // 5 - Ligne du montant du transporteur HT : reprend le numéro de compte transporteur si defini
                $c = new Carrier($order_obj->id_carrier);
                if ($if_tva) {
                    // Si commande TTC
                    if ('' != trim(Configuration::get('DMUEBP_CARRIER_TTC_' . $order_obj->id_carrier))) {
                        $this->dmuebp_compte_transport_ttc = Configuration::get(
                            'DMUEBP_CARRIER_TTC_' . $order_obj->id_carrier
                        );
                    }

                    $compte = $this->dmuebp_compte_transport_ttc;
                } else {
                    // Si commande HT
                    if ('' != trim(Configuration::get('DMUEBP_CARRIER_HT_' . $order_obj->id_carrier))) {
                        $this->dmuebp_compte_transport_ht = Configuration::get(
                            'DMUEBP_CARRIER_HT_' . $order_obj->id_carrier
                        );
                    }

                    $compte = $this->dmuebp_compte_transport_ht;
                }

                $total = (float) $orderinv_obj->total_shipping_tax_excl;
                if ($total > 0) {
                    $amount_round = Tools::ps_round($total, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $compte,
                        'label' => $this->l('Carrier without tax') . ' ' . $c->name . ' - ' . $pays,
                        'credit' => number_format($amount_round, 2, '.', ''), ];
                }

                // 6 - Ligne d'emballage cadeau HT
                // Ruse... si pas de tax produit alors on compte pas la tax emballage...
                if ($order_obj->total_wrapping > $order_obj->total_wrapping_tax_excl) {
                    $gift_amount = (($order_obj->total_wrapping / $order_obj->total_wrapping_tax_excl) - 1) * 100;
                    $gift_tax_rate = Tools::ps_round($gift_amount, 2, $order_obj->round_mode);
                } else {
                    $gift_tax_rate = 0;
                }

                if ($order_obj->total_wrapping > 0) {
                    $wrapping = $order_obj->total_wrapping;
                    $wrapping_ht = $wrapping;
                    if ($gift_tax_rate > 0) {
                        $wrapping_ht = $order_obj->total_wrapping_tax_excl;
                    }

                    if ($if_tva) {
                        $amount_round = Tools::ps_round($wrapping_ht, 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->dmuebp_compte_emballage_ttc,
                            'label' => $this->l('Gift packaging without tax'),
                            'credit' => number_format($amount_round, 2, '.', ''), ];
                    } else {
                        $amount_round = Tools::ps_round($wrapping_ht, 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->dmuebp_compte_emballage_ht,
                            'label' => $this->l('Gift packaging without tax'),
                            'credit' => number_format($amount_round, 2, '.', ''), ];
                    }
                }

                // 7 - Ligne de taxe par produit
                foreach ($taxs as $id_tax_rules_group => $tax_amount) {
                    $tax_group_rules = new TaxRulesGroup($id_tax_rules_group);
                    $taxe_rules_group_name = $tax_group_rules->name;
                    $this->dmuebp_compte_tva_name =
                        'DMUEBP_COMPTE_TVA_' . $id_tax_rules_group . '_' . $address_obj->id_country;

                    if (!isset($this->{$this->dmuebp_compte_tva_name})) {
                        $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }

                    $amount_round = Tools::ps_round($tax_amount, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $this->{$this->dmuebp_compte_tva_name},
                        'label' => $this->l('Products VAT'),
                        'credit' => number_format($amount_round, 2, '.', ''), ];
                }

                // 8 - Ligne total taxe (transporteur)
                if ($tva_shipping > 0) {
                    $id_taxe_rules_group = Carrier::getIdTaxRulesGroupByIdCarrier($order_obj->id_carrier);
                    $tax_group_rules = new TaxRulesGroup($id_taxe_rules_group);
                    $taxe_rules_group_name = $tax_group_rules->name;
                    $this->dmuebp_compte_tva_name =
                        'DMUEBP_COMPTE_TVA_' . $id_taxe_rules_group . '_' . $address_obj->id_country;

                    if (!isset($this->{$this->dmuebp_compte_tva_name})) {
                        $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }

                    $amount_round = Tools::ps_round((float) $tva_shipping, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => @$this->{$this->dmuebp_compte_tva_name},
                        'label' => $this->l('Carrier VAT') . ' ' . $taxe_rules_group_name,
                        'credit' => number_format($amount_round, 2, '.', ''), ];
                }

                // 9 - Remplacé par une ligne de TVA "autres options" (écotaxe, emballage, etc.)
                if (round($tva_options, 2) > 0) {
                    $compte_tva_other = Configuration::get('DMUEBP_COMPTE_TAX');
                    $amount_round = Tools::ps_round((float) $tva_options, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $compte_tva_other,
                        'label' => $this->l('Other Tax VAT'),
                        'credit' => number_format($amount_round, 2, '.', ''), ];
                }

                // 9 - Ligne taxe emballage
                /*$wrapping_tva = 0;
                if ($order_obj->total_wrapping > 0) {
                    $wrapping = $order_obj->total_wrapping;
                    if ($gift_tax_rate > 0) {
                        $wrapping_tva = $wrapping - $order_obj->total_wrapping_tax_excl;
                    }

                    if ($wrapping_tva > 0) {
                        $id_taxe_rules_groupe_wrapping = Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP');
                        $tax_group_rules = new TaxRulesGroup($id_taxe_rules_groupe_wrapping);
                        $taxe_rules_group_name = $tax_group_rules->name;
                        $this->dmuebp_compte_tva_name =
                            'DMUEBP_COMPTE_TVA_' . $id_taxe_rules_groupe_wrapping . '_' . $address_obj->id_country;
                        if (empty($this->{$this->dmuebp_compte_tva_name})) {
                            $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                        }
                        $total = Tools::ps_round($wrapping_tva, 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->{$this->dmuebp_compte_tva_name},
                            'label' => $this->l('Gift packaging VAT'),
                            'credit' => number_format($total, 2, '.', ''), ];
                    }
                }*/

                // FIN - Vérification de l'intégrité et de l'équilibrage de l'écriture
                $this->verifEquilibrage($lignes_ecriture, false, $num_facture, $order_obj->id);

                // Ecriture du CSV
                foreach ($lignes_ecriture as $le) {
                    $this->printCSVLine(
                        $csv_output,
                        $order_obj,
                        date('d/m/Y', strtotime($order_obj->invoice_date)),
                        $this->dmuebp_journal,
                        $le['compte'],
                        $num_facture,
                        $le['label'],
                        @$le['debit'],
                        @$le['credit'],
                        $pays,
                        $transaction_id,
                        @$le['payment']
                    );
                }
            }
        } // Fin foreach invoices
    }

    public function exportSlips(&$csv_output)
    {
        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');

        // RECUPERATION AVOIRS
        $slips = DmuEbpExportModel::getSlipsIdByDate($date_from, $date_to);

        if (!empty($this->test_slip)) {
            $slips = [$this->test_slip];
        }

        foreach ($slips as $id_slip) {
            $order_slip = new OrderSlip($id_slip);
            $order_slip->order_slip_type = isset($order_slip->order_slip_type) ?
            $order_slip->order_slip_type : 0;
            $order_obj = new Order($order_slip->id_order);
            $order_obj->round_mode = isset($order_obj->round_mode) ? $order_obj->round_mode : 2;
            $order_obj->round_type = isset($order_obj->round_type) ? $order_obj->round_type : 1;
            $products = OrderSlip::getOrdersSlipProducts($order_slip->id, $order_obj);
            $order_slip_detail = OrderSlip::getOrdersSlipDetail($order_slip->id);

            $address_obj = new Address($order_obj->id_address_delivery);
            $pays = $address_obj->country;

            $if_tva = true; // Verifie si c'est commande soumise à la TVA ou non
            $order_total_wt = $order_obj->getTotalProductsWithoutTaxes();
            $order_total = $order_obj->getTotalProductsWithTaxes();
            if ($order_total_wt == $order_total) {
                $if_tva = false; // Si aucune taxe trouvée pour cette commande
            }

            $prefix_num_avoir = Configuration::get('PS_CREDIT_SLIP_PREFIX', $this->id_lang);
            if (empty($prefix_num_avoir)) {
                $prefix_num_avoir = 'A';
            }
            $num_avoir = $prefix_num_avoir . sprintf('%06d', $id_slip);

            // SOLDE équilibrage
            $lignes_ecriture = [];

            // On va calculer les différentes tva
            $taxs_avoirs = [];
            $total_produits_ht = 0;
            $total_produits_ttc = 0;
            if (empty($order_slip->shipping_cost_amount)
                || ($order_slip->shipping_cost_amount != $order_slip->amount)) {
                foreach ($products as $k => $p) {
                    $order_detail_obj = new OrderDetail((int) $p['id_order_detail']);
                    $order_detail_obj->id_tax_rules_group = isset($order_detail_obj->id_tax_rules_group) ?
                        $order_detail_obj->id_tax_rules_group : 0;
                    $key = $order_detail_obj->id_tax_rules_group;
                    foreach ($order_slip_detail as $detail) {
                        if ($detail['id_order_detail'] != $k) {
                            continue;
                        }
                        $total_produits_ttc += $detail['amount_tax_incl'];
                        $total_produits_ht += $detail['amount_tax_excl'];

                        if (!isset($taxs_avoirs[$key])) {
                            $taxs_avoirs[$key] = 0;
                        }

                        $tax_tmp = $detail['amount_tax_incl'] - $detail['amount_tax_excl'];
                        $taxs_avoirs[$key] += Tools::ps_round($tax_tmp, 2, $order_obj->round_mode);
                    }
                }
            }

            // montant T.V.A frais de port
            $tva_shipping = 0;
            $shipping_cost = 0;
            $shipping_cost_ht = 0;
            if (1 == (int) $order_slip->shipping_cost
                || (!empty($order_slip->shipping_cost_amount)
                    && $order_slip->shipping_cost_amount > 0)) {
                if (!empty($order_slip->shipping_cost_amount) && $order_slip->shipping_cost_amount > 0) {
                    $shipping_cost = $order_slip->shipping_cost_amount;
                } else {
                    $shipping_cost = $order_obj->total_shipping;
                }

                $shipping_cost_ht = $shipping_cost / (($order_obj->carrier_tax_rate / 100) + 1);
                $tva_shipping = $shipping_cost - $shipping_cost_ht;
            }

            // $total_avoir = $order_slip->amount;
            $total_avoir = $total_produits_ttc + $shipping_cost;

            $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_client;
            $payment = Tools::strtoupper(trim(Tools::replaceAccentedChars($order_obj->payment)));
            $payment = str_replace([' ', '[', ']'], ['_', '_', '_'], $payment);
            if (isset($this->dmuebp_compte_payment[$payment]) && !empty($this->dmuebp_compte_payment[$payment])) {
                $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_payment[$payment];
            }
            if (isset($this->dmuebp_compte_customers[$order_obj->id_customer])
                && !empty($this->dmuebp_compte_customers[$order_obj->id_customer])) {
                $this->dmuebp_compte_client_ligne = $this->dmuebp_compte_customers[$order_obj->id_customer];
            }

            // Si soustraction du montant de la reduction
            if (1 == $order_slip->order_slip_type) {
                $total_avoir -= $order_obj->total_discounts;
            }

            // Doit-on exporter les factures à 0 euros ?
            $export_zero = (bool) Configuration::get('DMUEBP_EXORTZERO');

            if ($export_zero || (!$export_zero && $total_avoir > 0)) {
                $lignes_ecriture[] = [
                    'compte' => $this->dmuebp_compte_client_ligne,
                    'label' => $this->l('Order Slip') . ' ' .
                        str_pad($id_slip, 6, '0', STR_PAD_LEFT) . ' ' . $this->l('by') . ' ' .
                        DmuEbpExportModel::encodeToCSV($this->getCustomerNameByOrder($order_obj)),
                    'credit' => number_format($total_avoir, 2, '.', ''), ];

                // On va calculer les différentes TVA
                $slip = new OrderSlip($id_slip);
                $products = $slip->getProducts();
                $total_produits_ht = [];
                foreach ($products as $p) {
                    $order_detail_obj = new OrderDetail((int) $p['id_order_detail']);
                    $order_detail_obj->id_tax_rules_group = isset($order_detail_obj->id_tax_rules_group) ?
                        $order_detail_obj->id_tax_rules_group : 0;
                    $key = $order_detail_obj->id_tax_rules_group;

                    // ** ** Résolution problème remboursement partiel
                    foreach ($order_slip_detail as $detail) {
                        if ($detail['id_order_detail'] != $p['id_order_detail']) {
                            continue;
                        }
                        if (!isset($total_produits_ht[$key])) {
                            $total_produits_ht[$key] = $detail['amount_tax_excl'];
                        } else {
                            $total_produits_ht[$key] += $detail['amount_tax_excl'];
                        }
                    }
                    // ** **
                }

                // Ligne de produits
                if (0 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
                    // Personnalisation par TVA
                    foreach ($total_produits_ht as $id_tax_rules_group => $amount) {
                        $tax_rule = new TaxRulesGroup($id_tax_rules_group);
                        $this->dmuebp_compte_produits_tva_name =
                            'DMUEBP_COMPTE_PRODUITS_TVA_' . $id_tax_rules_group . '_' . $address_obj->id_country;
                        if (empty($this->{$this->dmuebp_compte_produits_tva_name})) {
                            $this->{$this->dmuebp_compte_produits_tva_name} = $this->dmuebp_compte_produits_ht;
                        }

                        $amount_round = Tools::ps_round($amount, 2, $order_obj->round_mode);

                        $lignes_ecriture[] = [
                            'compte' => $this->{$this->dmuebp_compte_produits_tva_name},
                            'label' => $this->l('Products') . ' ' . $this->l('without tax')
                                        . ' [' . DmuEbpExportModel::encodeToCSV($tax_rule->name) . ']',
                            'debit' => number_format($amount_round, 2, '.', ''), ];
                    }
                } else {
                    // Personnalisation par catégorie principale
                    foreach ($order_slip_detail as $detail) {
                        $order_detail = new OrderDetail($detail['id_order_detail']);
                        $data = DmuEbpExportModel::getProductAccountingInfos($order_detail->product_id);
                        $pdt = new Product($order_detail->product_id);

                        if ($if_tva) {
                            // Si commande TTC
                            if ('' == trim($data[0]['accounting_vat'])) {
                                $dmuebp_category_ttc = json_decode(Configuration::get('DMUEBP_CATEGORY_TTC'), true);
                                $account_value = Configuration::get('DMUEBP_COMPTE_PRODUIT_TTC');
                                // Priorité au numéro de compte de la catégorie principale
                                $dmuebp_cptva = 'DMUEBP_COMPTE_PRODUITS_TVA_' . $pdt->id_tax_rules_group;
                                if ('' != Configuration::get($dmuebp_cptva)) {
                                    $account_value = Configuration::get($dmuebp_cptva);
                                }
                                if (isset($dmuebp_category_ttc[$pdt->id_category_default])
                                    && '' != $dmuebp_category_ttc[$pdt->id_category_default]) {
                                    $account_value = $dmuebp_category_ttc[$pdt->id_category_default];
                                }
                            } else {
                                $account_value = $data[0]['accounting_vat'];
                            }
                        } else {
                            // Si commande HT
                            if ('' == trim($data[0]['accounting_no_vat'])) {
                                $dmuebp_category_ht = json_decode(Configuration::get('DMUEBP_CATEGORY_HT'), true);
                                $account_value = Configuration::get('DMUEBP_COMPTE_PRODUIT_HT');
                                // Si numéro de compte pour la categorie
                                if (isset($dmuebp_category_ht[$pdt->id_category_default])
                                    && '' != $dmuebp_category_ht[$pdt->id_category_default]) {
                                    $account_value = $dmuebp_category_ht[$pdt->id_category_default];
                                }
                            } else {
                                $account_value = $data[0]['accounting_no_vat'];
                            }
                        }

                        $tax_rule = new TaxRulesGroup($pdt->id_tax_rules_group);
                        $total = $detail['total_price_tax_excl'];
                        $total = Tools::ps_round($total, 2, $order_obj->round_mode);

                        $lignes_ecriture[] = [
                            'compte' => $account_value,
                            'label' => DmuEbpExportModel::encodeToCSV($pdt->name[$this->context->language->id]) . ' '
                                        . $this->l('without tax') . ' ['
                                        . DmuEbpExportModel::encodeToCSV($tax_rule->name) . ']',
                            'debit' => number_format($total, 2, '.', ''), ];
                    }
                }

                // Ligne du montant du transporteur HT
                if ($shipping_cost_ht > 0) {
                    if ($if_tva) {
                        $amount_shipping = Tools::ps_round($shipping_cost_ht, 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->dmuebp_compte_transport_ttc,
                            'label' => $this->l('Carrier without tax'),
                            'debit' => number_format($amount_shipping, 2, '.', ''), ];
                    } else {
                        $amount_shipping = Tools::ps_round($shipping_cost_ht, 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $this->dmuebp_compte_transport_ht,
                            'label' => $this->l('Carrier without tax'),
                            'debit' => number_format($amount_shipping, 2, '.', ''), ];
                    }
                }

                // Lignes de TVA
                if (!empty($taxs_avoirs)) {
                    foreach ($taxs_avoirs as $id_taxe_rules_group => $tax_amount) {
                        if ((float) $tax_amount > 0) {
                            $tax_group_rules = new TaxRulesGroup($id_taxe_rules_group);
                            $taxe_rules_group_name = $tax_group_rules->name;
                            $this->dmuebp_compte_tva_name =
                                'DMUEBP_COMPTE_TVA_' . $id_taxe_rules_group . '_' . $address_obj->id_country;

                            if (empty($this->{$this->dmuebp_compte_tva_name})) {
                                $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                            }

                            $amount_tva = Tools::ps_round($tax_amount, 2, $order_obj->round_mode);
                            $lignes_ecriture[] = [
                                'compte' => $this->{$this->dmuebp_compte_tva_name},
                                'label' => $this->l('Products VAT') . ' ' .
                                    DmuEbpExportModel::encodeToCSV($taxe_rules_group_name),
                                'debit' => number_format($amount_tva, 2, '.', ''), ];
                        }
                    }
                }

                if ($tva_shipping > 0) {
                    $id_taxe_rules_group = Carrier::getIdTaxRulesGroupByIdCarrier($order_obj->id_carrier);
                    $tax_group_rules = new TaxRulesGroup($id_taxe_rules_group);
                    $taxe_rules_group_name = $tax_group_rules->name;
                    $this->dmuebp_compte_tva_name =
                        'DMUEBP_COMPTE_TVA_' . $id_taxe_rules_group . '_' . $address_obj->id_country;

                    if (empty($this->{$this->dmuebp_compte_tva_name})) {
                        $this->{$this->dmuebp_compte_tva_name} = Configuration::get('DMUEBP_COMPTE_TAX');
                    }

                    $amount = Tools::ps_round($tva_shipping, 2, $order_obj->round_mode);
                    $lignes_ecriture[] = [
                        'compte' => $this->{$this->dmuebp_compte_tva_name},
                        'label' => $this->l('Carrier VAT') . ' ' . DmuEbpExportModel::encodeToCSV($taxe_rules_group_name),
                        'debit' => number_format($amount, 2, '.', ''), ];
                }

                // Si soustraction du montant de la reduction
                if (1 == $order_slip->order_slip_type) {
                    $account = $this->dmuebp_compte_rrr_ht;
                    if ($if_tva) {
                        $account = $this->dmuebp_compte_rrr_ttc;
                    }

                    $cart_rules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT *
                    FROM `' . _DB_PREFIX_ . 'order_cart_rule`
                    WHERE `id_order` = ' . (int) $order_obj->id);
                    foreach ($cart_rules as $cart_rule) {
                        $amount = Tools::ps_round($cart_rule['value'], 2, $order_obj->round_mode);
                        $lignes_ecriture[] = [
                            'compte' => $account,
                            'label' => DmuEbpExportModel::encodeToCSV($this->l('Reduction:')) . ' '
                                        . DmuEbpExportModel::encodeToCSV($cart_rule['name']),
                            'credit' => number_format($amount, 2, '.', ''), ];
                    }
                }

                // Vérification de l'intégrité et de l'équilibrage de l'écriture
                $this->verifEquilibrage($lignes_ecriture, true, $num_avoir, $order_obj->id);

                // Ecriture du CSV
                foreach ($lignes_ecriture as $le) {
                    $this->printCSVLine(
                        $csv_output,
                        $order_obj,
                        date('d/m/Y', strtotime($order_slip->date_add)),
                        $this->dmuebp_journal,
                        $le['compte'],
                        $num_avoir,
                        $le['label'],
                        @$le['debit'],
                        @$le['credit'],
                        $pays,
                        '',
                        ''
                    );
                }
            }
        }
    }

    public function printCSVLine(
        &$csv_output,
        $order_obj,
        $date,
        $journal,
        $compte,
        $num,
        $label,
        $debit,
        $credit,
        $pays,
        $transaction_id,
        $payment
    ) {
        // Récupération du séparateur
        $separator = Configuration::get('DMUEBP_SEPARATOR');
        $separator = str_replace('\\t', "\t", $separator);
        if (empty($separator)) {
            $separator = ';';
        }

        $ligne_csv = '';

        $ligne_csv .= $date . $separator;

        if (1 == $this->dmuebp_export) {
            $ligne_csv .= $journal . $separator;
        } else {
            $ligne_csv .= $journal . $separator;
            $ligne_csv .= $order_obj->id . $separator;
            $ligne_csv .= $order_obj->reference . $separator;
            $ligne_csv .= $pays . $separator;
            $ligne_csv .= $order_obj->id_customer . $separator;
            $ligne_csv .= DmuEbpExportModel::encodeToCSV($this->getCustomerNameByOrder($order_obj)) . $separator;
            $ligne_csv .= DmuEbpExportModel::encodeToCSV($this->getCustomerCompanyByOrder($order_obj)) . $separator;
        }

        $ligne_csv .= $compte . $separator;
        $ligne_csv .= DmuEbpExportModel::encodeToCSV($num, false) . $separator;
        $ligne_csv .= DmuEbpExportModel::encodeToCSV($label) . $separator;

        if (0 == $this->dmuebp_export) {
            $ligne_csv .= $payment . $separator;
            if (!empty($debit)) {
                $ligne_csv .= $debit . $separator;
            } else {
                $ligne_csv .= $credit . $separator;
            }
        }

        $ligne_csv .= $debit . $separator;
        $ligne_csv .= $credit . $separator;

        if (1 == $this->dmuebp_export) {
            $ligne_csv .= $this->getCurrencyByOrder($order_obj);
        } else {
            $ligne_csv .= $transaction_id;
        }

        if (true == $this->mode_debug) {
            echo $ligne_csv . '<br/>';
        }

        $csv_output .= $ligne_csv . "\r\n";
    }

    public function verifEquilibrage(&$lignes_ecriture, $is_avoir, $num_piece, $id_order)
    {
        $solde_equilibre = 0;
        foreach ($lignes_ecriture as $le) {
            if (!empty($le['credit'])) {
                $solde_equilibre += $le['credit'] * 100;
            }
            if (!empty($le['debit'])) {
                $solde_equilibre -= $le['debit'] * 100;
            }
        }

        $solde_test = number_format($solde_equilibre / 100, 2, '.', '');
        if (true == $this->mode_debug) {
            if (0 == $solde_test) {
                echo 'OK - LIGNE EQUILIBREE';
            }
            if (0 != $solde_test) {
                echo 'NOK - LIGNE NON EQUILIBREE : ' . $solde_test;
            }
            echo '<pre>' . print_r($lignes_ecriture, true) . '</pre>';
        }

        // Equilibrage à corriger ?
        if (0 != $solde_test) {
            // Correction de l'équilibrage
            if (1 == (int) Configuration::get('DMUEBP_AUTOBALANCE')) {
                $lignes_ecriture_avant_correction = $lignes_ecriture;
                $limite = (float) Configuration::get('DMUEBP_AUTOBALANCE_LIMIT') * 100;
                if ($solde_equilibre >= -$limite && $solde_equilibre <= $limite) {
                    // On va corriger sur le compte de produit
                    foreach ($lignes_ecriture as $key => $le) {
                        if (preg_match('`^7`iUs', $le['compte'])) {
                            if (true == $is_avoir) {
                                $lignes_ecriture[$key]['debit'] += ($solde_equilibre / 100);
                                $lignes_ecriture[$key]['debit'] =
                                    number_format($lignes_ecriture[$key]['debit'], 2, '.', '');
                            } else {
                                $lignes_ecriture[$key]['credit'] -= ($solde_equilibre / 100);
                                $lignes_ecriture[$key]['credit'] =
                                    number_format($lignes_ecriture[$key]['credit'], 2, '.', '');
                            }
                            break;
                        }
                    }

                    if (true == $this->mode_debug) {
                        echo 'OK - ECRITURE CORRIGEE';
                        echo '<pre>' . print_r($lignes_ecriture, true) . '</pre>';
                    }
                    ++$this->nb_corrected;
                    $this->addReportLine(
                        'correction',
                        $lignes_ecriture_avant_correction,
                        $solde_equilibre / 100,
                        $is_avoir,
                        $lignes_ecriture,
                        $num_piece,
                        $id_order
                    );
                } else {
                    ++$this->nb_error;
                    $this->addReportLine(
                        'error',
                        $lignes_ecriture,
                        $solde_equilibre / 100,
                        $is_avoir,
                        '',
                        $num_piece,
                        $id_order
                    );
                    unset($lignes_ecriture);
                }
            } else {
                ++$this->nb_error;
                $this->addReportLine(
                    'error',
                    $lignes_ecriture,
                    $solde_equilibre / 100,
                    $is_avoir,
                    '',
                    $num_piece,
                    $id_order
                );
                unset($lignes_ecriture);
            }
        } else {
            ++$this->nb_ok;
        }
    }

    public function emptyReport()
    {
        $sql = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'dmuebp_report`';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }

    public function addReportLine(
        $type,
        $ecriture_originale,
        $diff,
        $is_avoir,
        $ecriture_corrigee,
        $num_piece,
        $id_order
    ) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'dmuebp_report` 
                (type, ecriture_originale, difference, is_avoir, ecriture_corrigee, num_piece, id_order)
                VALUES ("' . pSQL($type) . '",
                "' . pSQL(json_encode($ecriture_originale)) . '",
                ' . number_format((float) $diff, 2, '.', '') . ',
                ' . (int) $is_avoir . ',
                "' . pSQL(json_encode($ecriture_corrigee)) . '",
                "' . pSQL($num_piece) . '",
                "' . (int) $id_order . '")';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
    }

    public function getReportLines($type = 'error')
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'dmuebp_report`
                WHERE type = "' . $type . '"
                ORDER BY id_order ASC';
        $lignes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $report_lines = [];
        foreach ($lignes as $ligne) {
            $report_lines[] = [
                'id_report' => $ligne['id_report'],
                'id_order' => $ligne['id_order'],
                'is_avoir' => (int) $ligne['is_avoir'],
                'num_piece' => $ligne['num_piece'],
                'difference' => Tools::displayPrice($ligne['difference']),
                'ecriture' => print_r(json_decode($ligne['ecriture_originale'], true), true),
            ];
        }

        $this->context->smarty->assign([
            'report_lines' => $report_lines,
        ]);

        $html = $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/export_line.tpl');

        return $html;
    }

    public function getConfigFieldsValues()
    {
        $data = [];
        $data['DMUEBP_JOURNAL'] = Configuration::get('DMUEBP_JOURNAL');

        if (Tools::getIsset('date_from')) {
            $data['date_from'] = Tools::getValue('date_from');
            $data['date_to'] = Tools::getValue('date_to');
        } else {
            $data['date_from'] = date('Y-m-d');
            $data['date_to'] = date('Y-m-d');
        }

        return $data;
    }

    public function renderView()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Export form'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'date',
                        'label' => $this->l('Start date'),
                        'name' => 'date_from',
                        'size' => 10,
                        'required' => true,
                    ],
                    [
                        'type' => 'date',
                        'label' => $this->l('End date'),
                        'name' => 'date_to',
                        'size' => 10,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'name' => 'submitExport',
                    'title' => $this->l('Créer l\'export'),
                ],
            ],
        ];

        $helper_form = new HelperForm();
        $helper_form->show_toolbar = false;
        $helper_form->table = $this->table;

        $helper_form->identifier = $this->identifier;
        $helper_form->currentIndex = 'index.php?controller=AdminDmuEbpExport';
        $helper_form->token = Tools::getAdminTokenLite('AdminDmuEbpExport');
        $helper_form->default_form_language = (int) $this->context->language->id; // IMPORTANT !
        $helper_form->allow_employee_form_lang = (int) $this->context->employee->id; // IMPORTANT !
        $helper_form->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => (int) $this->context->language->id,
        ];

        // v2.4.3 : Correction d'un bug PrestaShop
        $helper_form->default_form_language = (int) $this->context->language->id;

        $url = 'index.php?controller=AdminModules&amp;configure=dmuebpexport' .
            '&amp;tab_module=administration&amp;module_name=dmuebpexport&amp;token=' .
            Tools::getAdminTokenLite('AdminModules');

        if (0 == Configuration::get('DMUEBP_COMPTE_PRIORITE')) {
            $txt_ventilation = $this->l('VAT account');
        } else {
            $txt_ventilation = $this->l('Main product/category account');
        }

        $this->context->smarty->assign([
            'selected_format' => (int) Configuration::get('DMUEBP_EXPORT', null, 0, 0),
            'txt_infos' => $this->l('Informations'),
            'txt_select' => $this->l('Selected export format:'),
            'txt_export_comptable' => $this->l('Accountant export'),
            'txt_export_ebp' => $this->l('EBP Export'),
            'txt_ventilation' => $txt_ventilation,
            'txt_switch' => $this->l('defined from '),
            'txt_cnfpage' => $this->l('configuration page'),
            'cnf_link' => $url,
        ]);

        $this->nb_ok = (int) Configuration::get('DMUEBP_NB_OK');
        $this->nb_corrected = (int) Configuration::get('DMUEBP_NB_CORRECTED');
        $this->nb_error = (int) Configuration::get('DMUEBP_NB_ERROR');

        $desc_report = '';

        $type_input = 'html_content';
        $class_alert_ok = 'alert alert-success';
        $class_alert_error = 'alert alert-danger';
        $class_alert_warn = 'alert alert-warning';
        $class_button = 'btn btn-primary';
        if (version_compare(_PS_VERSION_, '1.6.0.7', '<')) {
            $type_input = 'desc';
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $class_alert_ok = 'conf';
                $class_alert_error = 'alert';
                $class_alert_warn = 'warning';
                $class_button = 'button';
            }
        }

        $this->context->smarty->assign([
            'nb_ok' => $this->nb_ok,
            'nb_corrected' => $this->nb_corrected,
            'nb_error' => $this->nb_error,
            'class_alert_ok' => $class_alert_ok,
            'class_alert_error' => $class_alert_error,
            'class_alert_warn' => $class_alert_warn,
            'txt_ok' => $this->l('accounting entries exported'),
            'txt_error' => $this->l('accounting entries in error'),
            'txt_corrected' => $this->l('accounting entries corrected'),
            'report_lines_error' => $this->getReportLines('error'),
            'report_lines_correction' => $this->getReportLines('correction'),
        ]);

        $desc_report = $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/export_desc.tpl');

        $this->context->smarty->assign([
            'last_export' => Configuration::get('DMUEBP_LAST_EXPORT'),
            'class_button' => $class_button,
            'txt_download' => $this->l('Download'),
        ]);

        $generate_form = [$fields_form];
        $last_export = Configuration::get('DMUEBP_LAST_EXPORT');
        if (!empty($last_export)) {
            $generate_form[] = [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Export Report'),
                        'icon' => 'icon-cogs',
                    ],
                    'input' => [
                        [
                            'type' => 'html',
                            'class' => 'input fixed-width-xxl',
                            'label' => '',
                            'name' => '',
                            $type_input => $this->context->smarty->fetch(dirname(__FILE__) . '/../../views/templates/admin/export_btn.tpl'),
                        ],
                        [
                            'type' => 'html',
                            'class' => 'input fixed-width-xxl',
                            'label' => '',
                            'name' => '',
                            $type_input => $desc_report,
                        ],
                    ],
                ],
            ];
        }

        return parent::renderView() . $helper_form->generateForm($generate_form);
    }

    public function renderList()
    {
        return $this->renderView();
    }

    public function renderForm()
    {
        return $this->renderView();
    }

    public function getCustomerNameByOrder($order)
    {
        $name = null;
        if (isset($order->id_address_invoice) && $order->id_address_invoice) {
            $address = new Address($order->id_address_invoice);
            if ($address) {
                $name = $address->firstname . ' ' . $address->lastname;
            }
        }
        if (!$name && isset($order->id_customer) && $order->id_customer) {
            $customer = new Customer($order->id_customer);
            $name = $customer->firstname . ' ' . $customer->lastname;
        }

        return $name;
    }

    public function getCustomerCompanyByOrder($order)
    {
        $name = null;
        if (isset($order->id_address_invoice) && $order->id_address_invoice) {
            $address = new Address($order->id_address_invoice);
            if ($address) {
                if (isset($address->company) && !empty($address->company)) {
                    $name = $address->company;
                }
            }
        }

        return $name;
    }

    public function getCurrencyByOrder($order)
    {
        $devise = null;
        if (isset($order->id_currency) && $order->id_currency) {
            $currency = Currency::getCurrency($order->id_currency);
            if (isset($currency['iso_code'])) {
                $devise = $currency['iso_code'];
            }
        }
        if (!$devise) {
            $devise = 'EUR';
        }

        return $devise;
    }
}
