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

/**
 * Class TNTOfficielCarrier
 */
class TNTOfficielCarrier extends ObjectModel
{
    // Hard to Reach Area.
    const URL_HRA_JSON = 'https://www.tnt.fr/Telechargements/cit/tnt-zda.json';
    const URL_HRA_HELP = 'https://www.tnt.fr/zone-difficilement-accessible';
    const PATH_HRA_JSON = 'libraries/data/postcode/hra.json';

    // id_tntofficiel_carrier
    public $id;

    /** @var int Carrier ID. */
    public $id_carrier;
    /** @var int Associated unique shop ID. */
    public $id_shop;
    /** @var int Account ID who create this carrier. */
    public $id_account;
    /** @var string Account Type. */
    public $account_type;
    /** @var string Carrier Type. */
    public $carrier_type;
    /** @var string Carrier Code 1. */
    public $carrier_code1;
    /** @var string Carrier Code 2 (optional). */
    public $carrier_code2;
    /** @var bool Is zones configuration enabled. */
    public $zones_enabled;
    /** @var bool Is zones cloning configuration enabled. */
    public $zones_cloning_enabled;
    /** @var string Serialized zones configuration. */
    public $zones_config;

    /** @var array Model definition. */
    public static $definition = array(
        'table' => 'tntofficiel_carrier',
        'primary' => 'id_tntofficiel_carrier',
        'fields' => array(
            'id_carrier' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_shop' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'id_account' => array(
                'type' => ObjectModel::TYPE_INT,
                'size' => 10,
                'validate' => 'isUnsignedId',
            ),
            'account_type' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
            'carrier_type' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 16,
            ),
            'carrier_code1' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 1,
            ),
            'carrier_code2' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 1,
            ),
            'zones_enabled' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'zones_cloning_enabled' => array(
                'type' => ObjectModel::TYPE_BOOL,
                'validate' => 'isBool',
            ),
            'zones_config' => array(
                'type' => ObjectModel::TYPE_STRING,
            ),
        ),
    );

    /** @var array Carrier Type (AKA Receiver Type) list. */
    public static $arrCarrierTypeList = array(
        'ENTERPRISE',
        'INDIVIDUAL',
        'DROPOFFPOINT',
        'DEPOT',
    );

    /** @var array White list using Account Type, Carrier Type (AKA Receiver Type) and Carrier Code. */
    public static $arrWhiteList = array(
        '*' => array(
            'ENTERPRISE' => array(
                'N' => true,
                'A' => true,
                'T' => true,
                'M' => true,
                'J' => true,
                'P' => true,
            ),
            'DEPOT' => array(
                'J' => true,
                'P' => true,
            ),
            'DROPOFFPOINT' => array(
                'JD' => true,
                'PD' => true,
            ),
            'INDIVIDUAL' => array(
                'AZ' => true,
                'TZ' => true,
                'MZ' => true,
                'JZ' => true,
                'PZ' => true,
            ),
        ),
        // ALIMENT not detected, evaluated as * (see getAccountType()).
        'ALIMENT' => array(
            'ENTERPRISE' => array(
                'N' => false,
                'A' => false,
                'T' => false,
                'M' => false,
                'J' => false,
                'P' => false,
            ),
            'DEPOT' => array(
                'J' => false,
                'P' => false,
            ),
            'DROPOFFPOINT' => array(
                'JD' => false,
            ),
            'INDIVIDUAL' => array(
                'AZ' => false,
                'TZ' => false,
                'MZ' => false,
                'JZ' => false,
            ),
        ),
        // Assurance Systématique.
        'ASSU' => array(
            'ENTERPRISE' => array(
                'N' => true,
                'A' => true,
                'T' => true,
                'M' => true,
                'J' => true,
                'P' => true,
            ),
            'DEPOT' => array(
                'J' => true,
            ),
            'DROPOFFPOINT' => array(
                'JD' => true,
            ),
            'INDIVIDUAL' => array(
                'AZ' => true,
                'TZ' => true,
                'MZ' => true,
                'JZ' => true,
            ),
        ),
        'RP' => array(
            'ENTERPRISE' => array(
                'AP' => true,
                'TP' => true,
                'MP' => true,
                'JP' => true,
                'PP' => true,
            ),
            'DEPOT' => array(
                'JP' => true,
            ),
        ),
        // ALIMENT not detected, evaluated as RP (see getAccountType()).
        'RP ALIMENT' => array(
            'ENTERPRISE' => array(
                'AP' => false,
                'TP' => false,
                'MP' => false,
                'JP' => false,
            ),
            'DEPOT' => array(
                'JP' => false,
            ),
        ),
        'RP ASSU' => array(
            'ENTERPRISE' => array(
                'AP' => true,
                'TP' => true,
                'MP' => true,
                'JP' => true,
            ),
            'DEPOT' => array(
                'JP' => true,
            ),
        ),
        'ESP' => array(
            'ENTERPRISE' => array(
                'AW' => true,
                'TW' => true,
                'MW' => true,
                'JW' => true,
                'PW' => true,
            ),
            'DEPOT' => array(
                'JW' => true,
            ),
        ),
        // ALIMENT not detected, evaluated as ESP (see getAccountType()).
        'ESP ALIMENT' => array(
            'ENTERPRISE' => array(
                'AW' => false,
                'TW' => false,
                'MW' => false,
                'JW' => false,
            ),
            'DEPOT' => array(
                'JW' => false,
            ),
        ),
        'ESP ASSU' => array(
            'ENTERPRISE' => array(
                'AW' => false,
                'TW' => false,
                'MW' => false,
                'JW' => false,
            ),
            'DEPOT' => array(
                'JW' => false,
            ),
        ),
        'LPSE' => array(
            'ENTERPRISE' => array(
                'NE' => true,
                'AE' => true,
                'TE' => true,
                'ME' => true,
                'JE' => true,
                'PE' => true,
            ),
        ),
        // Essentiel 24h
        'ESSENTIEL' => array(
            'INDIVIDUAL' => array(
                'AZ' => true,
                'TZ' => true,
                'MZ' => true,
                'JZ' => true,
                'PZ' => true,
            ),
        ),
        // Essentiel Flexibilité
        'LPSE ESSENTIEL' => array(
            /*'ENTERPRISE' => array(
                'AE' => true,
                'TE' => true,
                'ME' => true,
                'JE' => true,
                'PE' => true,
            ),*/
            'INDIVIDUAL' => array(
                'AE' => true,
                'TE' => true,
                'ME' => true,
                'JE' => true,
                'PE' => true,
            ),
        ),
        'FROID' => array(
            'ENTERPRISE' => array(
                'AF' => true,
                'TF' => true,
                'MF' => true,
                'JF' => true,
                'PF' => true,
            ),
        ),
    );

    /** @var array Displayed informations on Front. */
    private static $arrCarrierCodeInfos = array(
        '*' => array(
            'ENTERPRISE:N' => array(
                'label' => '08:00 Express en entreprise',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 8 heures.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 8 heures.',
            ),
            'ENTERPRISE:A' => array(
                'label' => '09:00 Express en entreprise',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 9 heures.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 9 heures.',
            ),
            'ENTERPRISE:T' => array(
                'label' => '10:00 Express en entreprise',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 10 heures.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 10 heures.',
            ),
            'ENTERPRISE:M' => array(
                'label' => '12:00 Express en entreprise',
                'delay' => 'Livraison en entreprise dès le lendemain de l\'expédition, avant midi.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant midi.',
            ),
            'ENTERPRISE:J' => array(
                'label' => '13:00 Express en entreprise',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 13 heures<small> <i><sup>(1)</sup></i></small>.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande<small> <i><sup>(1)</sup></i></small>.',
                'reference' => '<small><i><sup>(1)</sup> Avant 13 heures ou en début d\'après-midi en zone rurale.</i></small>',
            ),
            'ENTERPRISE:P' => array(
                'label' => '18:00 Express en entreprise',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 18 heures.',
                'description' => 'Pour une livraison aux entreprises en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 18 heures.',
            ),
            'INDIVIDUAL:A' => array(
                'label' => '09:00 Express à domicile',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 9 heures.',
                'description' => 'Pour une livraison à domicile en France métropolitaine.<br />
Livraison  en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 9 heures.',
            ),
            'INDIVIDUAL:T' => array(
                'label' => '10:00 Express à domicile',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 10 heures.',
                'description' => 'Pour une livraison à domicile en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 10 heures.',
            ),
            'INDIVIDUAL:M' => array(
                'label' => '12:00 Express à domicile',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant midi.',
                'description' => 'Pour une livraison à domicile en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant midi.',
            ),
            'INDIVIDUAL:J' => array(
                'label' => '13:00 Express à domicile',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 13 heures<small> <i><sup>(1)</sup></i></small>.',
                'description' => 'Pour une livraison à domicile en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande<small> <i><sup>(1)</sup></i></small>.',
                'reference' => '<small><i><sup>(1)</sup> Avant 13 heures ou en début d\'après-midi en zone rurale.</i></small>',
            ),
            'INDIVIDUAL:P' => array(
                'label' => '18:00 Express à domicile',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 18 heures.',
                'description' => 'Pour une livraison à domicile en France métropolitaine.<br />
Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 18 heures.',
            ),
            'DROPOFFPOINT:J' => array(
                'label' => '13:00 Express chez un commerçant partenaire',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 13 heures<small> <i><sup>(1)</sup></i></small>.',
                'description' => 'Mise à disposition chez l\'un de nos commerçants partenaires en France métropolitaine.<br />
Remise contre signature et présentation d\'une pièce d\'identité dès le lendemain de l\'expédition de votre commande<small> <i><sup>(1)</sup></i></small>.',
                'reference' => '<small><i><sup>(1)</sup> Avant 13 heures ou en début d\'après-midi en zone rurale.</i></small>',
            ),
            'DROPOFFPOINT:P' => array(
                'label' => '18:00 Express chez un commerçant partenaire',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 18 heures.',
                'description' => 'Mise à disposition chez l\'un de nos commerçants partenaires en France métropolitaine.<br />
Remise contre signature et présentation d\'une pièce d\'identité dès le lendemain de l\'expédition de votre commande.',
            ),
            'DEPOT:J' => array(
                'label' => '13:00 Express en agence TNT',
                'delay' => 'Livraison dès le lendemain de l\'expédition. Mise à votre disposition pendant 10 Jours.',
                'description' => 'Pour une livraison dans l\'une de nos agences TNT en France métropolitaine.<br />
Mise à votre disposition sur présentation d\'une pièce d\'identité et contre signature le lendemain de l\'expédition de votre commande et ce pendant 10 Jours.',
            ),
            'DEPOT:P' => array(
                'label' => '18:00 Express en agence TNT',
                'delay' => 'Livraison dès le lendemain de l\'expédition. Mise à votre disposition pendant 10 Jours.',
                'description' => 'Pour une livraison dans l\'une de nos agences TNT en France métropolitaine.<br />
Mise à votre disposition sur présentation d\'une pièce d\'identité et contre signature le lendemain de l\'expédition de votre commande et ce pendant 10 Jours.',
            ),
        ),
        // Essentiel 24h
        'ESSENTIEL' => array(
            'INDIVIDUAL:A' => array(
                'label' => '09:00 Express à domicile - Essentiel 24h',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 9 heures.',
                'description' => 'Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 9h.<br />
En cas d\'absence, le colis est déposé chez le commerçant partenaire le plus proche dès 14h.',
            ),
            'INDIVIDUAL:T' => array(
                'label' => '10:00 Express à domicile - Essentiel 24h',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 10 heures.',
                'description' => 'Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 10h.<br />
En cas d\'absence, le colis est déposé chez le commerçant partenaire le plus proche dès 14h.',
            ),
            'INDIVIDUAL:M' => array(
                'label' => '12:00 Express à domicile - Essentiel 24h',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant midi.',
                'description' => 'Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 12h.<br />
En cas d\'absence, le colis est déposé chez le commerçant partenaire le plus proche dès 14h.',
            ),
            'INDIVIDUAL:J' => array(
                'label' => '13:00 Express à domicile - Essentiel 24h',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 13 heures<small> <i><sup>(1)</sup></i></small>.',
                'description' => 'Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande <small><i><sup>(1)</sup></i></small>.<br />
En cas d\'absence, le colis est déposé chez le commerçant partenaire le plus proche dès 14h.',
                'reference' => '<small><i><sup>(1)</sup> Avant 13 heures ou en début d\'après-midi en zone rurale.</i></small>',
            ),
            'INDIVIDUAL:P' => array(
                'label' => '18:00 Express à domicile - Essentiel 24h',
                'delay' => 'Livraison dès le lendemain de l\'expédition, avant 18 heures.',
                'description' => 'Livraison en mains propres et contre signature dès le lendemain de l\'expédition de votre commande, avant 18h.<br />
En cas d\'absence, le colis est déposé chez le commerçant partenaire le plus proche dès 14h.',
            ),
        ),
        // Essentiel Flexibilité
        'LPSE ESSENTIEL' => array(
            'INDIVIDUAL:A' => array(
                'label' => '09:00 Express - Essentiel Flexibilité',
                'delay' => 'Livraison dès le lendemain de l\'expédition<small> <i><sup>(1)</sup></i></small>, avant 9 heures.',
                'description' => 'Livraison <small><i><sup>(1)</sup></i></small> (à domicile ou entreprise) dès le lendemain de l\'expédition de votre commande, avant 9h.<br />
En cas d\'absence (ou d\'impossibilité de livrer), vous pourrez donner vos instructions ou bénéficier d\'une 2ème présentation du colis le lendemain.',
                'reference' => '<small><i><sup>(1)</sup> Livraison possible sans émargement (colis livré en boîte aux lettres, sur le pas de porte…). Indiquez votre choix dans la zone "Instructions particulières". Sans instruction de votre part, votre colis vous sera livré en main propre.</i></small>',
            ),
            'INDIVIDUAL:T' => array(
                'label' => '10:00 Express - Essentiel Flexibilité',
                'delay' => 'Livraison dès le lendemain de l\'expédition<small> <i><sup>(1)</sup></i></small>, avant 10 heures.',
                'description' => 'Livraison <small><i><sup>(1)</sup></i></small> (à domicile ou entreprise) dès le lendemain de l\'expédition de votre commande, avant 10h.<br />
En cas d\'absence (ou d\'impossibilité de livrer), vous pourrez donner vos instructions ou bénéficier d\'une 2ème présentation du colis le lendemain.',
                'reference' => '<small><i><sup>(1)</sup> Livraison possible sans émargement (colis livré en boîte aux lettres, sur le pas de porte…). Indiquez votre choix dans la zone "Instructions particulières". Sans instruction de votre part, votre colis vous sera livré en main propre.</i></small>',
            ),
            'INDIVIDUAL:M' => array(
                'label' => '12:00 Express - Essentiel Flexibilité',
                'delay' => 'Livraison dès le lendemain de l\'expédition<small> <i><sup>(1)</sup></i></small>, avant midi.',
                'description' => 'Livraison <small><i><sup>(1)</sup></i></small> (à domicile ou entreprise) dès le lendemain de l\'expédition de votre commande, avant 12h.<br />
En cas d\'absence (ou d\'impossibilité de livrer), vous pourrez donner vos instructions ou bénéficier d\'une 2ème présentation du colis le lendemain.',
                'reference' => '<small><i><sup>(1)</sup> Livraison possible sans émargement (colis livré en boîte aux lettres, sur le pas de porte…). Indiquez votre choix dans la zone "Instructions particulières". Sans instruction de votre part, votre colis vous sera livré en main propre.</i></small>',
            ),
            'INDIVIDUAL:J' => array(
                'label' => '13:00 Express - Essentiel Flexibilité',
                'delay' => 'Livraison dès le lendemain de l\'expédition<small> <i><sup>(1)</sup></i></small>, avant 13 heures<small> <i><sup>(2)</sup></i></small>.',
                'description' => 'Livraison <small><i><sup>(1)</sup></i></small> (à domicile ou entreprise) dès le lendemain de l\'expédition de votre commande <small><i><sup>(2)</sup></i></small>.<br />
En cas d\'absence (ou d\'impossibilité de livrer), vous pourrez donner vos instructions ou bénéficier d\'une 2ème présentation du colis le lendemain.',
                'reference' => '<small><i><sup>(1)</sup> Livraison possible sans émargement (colis livré en boîte aux lettres, sur le pas de porte…). Indiquez votre choix dans la zone "Instructions particulières". Sans instruction de votre part, votre colis vous sera livré en main propre.</i></small><br />
<small><i><sup>(2)</sup> Avant 13 heures ou en début d\'après-midi en zone rurale.</i></small>',
            ),
            'INDIVIDUAL:P' => array(
                'label' => '18:00 Express - Essentiel Flexibilité',
                'delay' => 'Livraison dès le lendemain de l\'expédition<small> <i><sup>(1)</sup></i></small>, avant 18 heures.',
                'description' => 'Livraison <small><i><sup>(1)</sup></i></small> (à domicile ou entreprise) dès le lendemain de l\'expédition de votre commande, avant 18h.<br />
En cas d\'absence (ou d\'impossibilité de livrer), vous pourrez donner vos instructions ou bénéficier d\'une 2ème présentation du colis le lendemain.',
                'reference' => '<small><i><sup>(1)</sup> Livraison possible sans émargement (colis livré en boîte aux lettres, sur le pas de porte…). Indiquez votre choix dans la zone "Instructions particulières". Sans instruction de votre part, votre colis vous sera livré en main propre.</i></small>',
            ),
        ),
    );

    /** @var array Displayed informations on Front (optional). */
    private static $arrCarrierTypeInfos = array(
        // https://www.tnt.com/express/fr_fr/site/home/comment-expedier/services-livraison/services-complementaires/livraison-avec-paiement.html
        'RP' => 'Colis remis contre un règlement par chèque.',
        'RP ASSU' => 'Colis remis contre un règlement par chèque.',
        // https://www.tnt.com/express/fr_fr/site/home/comment-expedier/services-livraison/services-complementaires/livraison-sous-protection.html
        'ESP' => 'Marchandises sensibles expédiées avec une sûreté renforcée du ramassage jusqu\'à la livraison.',
        'FROID' => 'En cas d\'instance de votre colis, un traitement opérationnel spécifique prévoit sa mise en chambre froide.',
        'LPSE' => 'En cas d\'absence, livraison possible sans émargement',
    );

    /** @var array Supported weight unit and conversion factor for Kg. */
    private static $arrWeightUnitFactorList = array(
        'kg' => 1.0,
        'g' => 1000.0,
        'mg' => 1000000.0,
        'lb' => 2.204622621,
        'oz' => 35.2739961949,
    );

    /** @var array Cache and prevent race condition. */
    private static $arrLoadedEntities = array();

    /**
     * Creates the tables needed by the model.
     *
     * @return bool
     */
    public static function createTables()
    {
        TNTOfficiel_Logstack::log();

        $strLogMessage = sprintf('%s::%s', __CLASS__, __FUNCTION__);

        $strTablePrefix = _DB_PREFIX_;
        $strTableEngine = _MYSQL_ENGINE_;

        $strTableName = $strTablePrefix . TNTOfficielCarrier::$definition['table'];

        // Create table.
        $strSQLCreateCarrier = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_carrier`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_carrier`                    INT(10) UNSIGNED NOT NULL,
    `id_shop`                       INT(10) UNSIGNED NOT NULL,
    `id_account`                    INT(10) UNSIGNED NOT NULL,
    `account_type`                  VARCHAR(50) NULL DEFAULT NULL,
    `carrier_type`                  VARCHAR(16) NOT NULL,
    `carrier_code1`                 VARCHAR(1) NOT NULL,
    `carrier_code2`                 VARCHAR(1) NOT NULL,
    `zones_enabled`                 TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    `zones_cloning_enabled`         TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `zones_config`                  TEXT NULL,
-- Key.
    PRIMARY KEY (`id_tntofficiel_carrier`),
    UNIQUE INDEX `id_carrier` (`id_carrier`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

        $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreateCarrier);
        if (is_string($boolDBResult)) {
            TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

            return false;
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return TNTOfficielCarrier::checkTables();
    }

    /**
     * Check if table and columns exist.
     *
     * @return bool
     */
    public static function checkTables()
    {
        TNTOfficiel_Logstack::log();

        $strTablePrefix = _DB_PREFIX_;
        $strTableName = $strTablePrefix . TNTOfficielCarrier::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielCarrier::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null, $intArgLangID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID, $intArgLangID);
    }

    /**
     * Load existing object model or optionally create a new one for its ID.
     *
     * @param      $intArgCarrierID
     * @param bool $boolArgCreate
     * @param null $intArgLangID
     *
     * @return mixed|null|TNTOfficielCarrier
     */
    public static function loadCarrierID($intArgCarrierID, $boolArgCreate = false, $intArgLangID = null)
    {
        TNTOfficiel_Logstack::log();

        $intCarrierID = (int)$intArgCarrierID;

        // An existing Carrier ID is required (to load or create).
        if (!($intCarrierID > 0)) {
            return null;
        }

        // If is not a TNT carrier.
        if (!TNTOfficielCarrier::isTNTOfficielCarrierID($intCarrierID)) {
            // Do not create or load.
            return null;
        }

        $strEntityID = '_' . $intCarrierID . '-' . (int)$intArgLangID . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielCarrier::$arrLoadedEntities)) {
            $objTNTCarrierModel = TNTOfficielCarrier::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTCarrierModel)
                && (int)$objTNTCarrierModel->id_carrier === $intCarrierID
            ) {
                return $objTNTCarrierModel;
            }
        }

        // Search row for carrier ID.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielCarrier::$definition['table']);
        $objDbQuery->where('id_carrier = ' . $intCarrierID);

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found and match carrier ID.
        if (is_array($arrDBResult) && count($arrDBResult) === 1 &&
            $intCarrierID === (int)$arrDBResult[0]['id_carrier']
        ) {
            // Load existing TNT carrier entry.
            $objTNTCarrierModel = new TNTOfficielCarrier((int)$arrDBResult[0]['id_tntofficiel_carrier'], $intArgLangID);
        } elseif ($boolArgCreate === true) {
            // Create a new TNT carrier entry.
            $objTNTCarrierModelCreate = new TNTOfficielCarrier(null, $intArgLangID);
            $objTNTCarrierModelCreate->id_carrier = $intCarrierID;
            // init zonesEnabled by default is true
            $objTNTCarrierModelCreate->setZonesEnabled(1);
            $objTNTCarrierModelCreate->save();
            // Reload to get default DB values after creation.
            $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false, $intArgLangID);
        } else {
            // Log.
            $objException = new Exception(sprintf(
                TNTOfficiel::getCodeTranslate('errorUnableLoadNotFoundStr'),
                'TNTOfficielCarrier',
                'Carrier',
                $intCarrierID
            ));
            TNTOfficiel_Logger::logException($objException);

            return null;
        }

        // Check.
        if (!Validate::isLoadedObject($objTNTCarrierModel)
            || (int)$objTNTCarrierModel->id_carrier !== $intCarrierID
        ) {
            return null;
        }

        $objTNTCarrierModel->id = (int)$objTNTCarrierModel->id;
        $objTNTCarrierModel->id_carrier = (int)$objTNTCarrierModel->id_carrier;
        $objTNTCarrierModel->id_shop = (int)$objTNTCarrierModel->id_shop;
        $objTNTCarrierModel->id_account = (int)$objTNTCarrierModel->id_account;

        TNTOfficielCarrier::$arrLoadedEntities[$strEntityID] = $objTNTCarrierModel;

        return $objTNTCarrierModel;
    }

    public static function isWhiteListed(
        $strArgAccountType,
        $strArgCarrierType,
        $strArgCarrierCode1,
        $strArgCarrierCode2
    ) {
        TNTOfficiel_Logstack::log();

        $strAccountType = (($strArgAccountType === '') ? '*' : $strArgAccountType);

        return (array_key_exists($strAccountType, TNTOfficielCarrier::$arrWhiteList)
            && is_array(TNTOfficielCarrier::$arrWhiteList[$strAccountType])
            && array_key_exists($strArgCarrierType, TNTOfficielCarrier::$arrWhiteList[$strAccountType])
            && is_array(TNTOfficielCarrier::$arrWhiteList[$strAccountType][$strArgCarrierType])
            && array_key_exists(
                $strArgCarrierCode1 . $strArgCarrierCode2,
                TNTOfficielCarrier::$arrWhiteList[$strAccountType][$strArgCarrierType]
            )
            && TNTOfficielCarrier::$arrWhiteList[$strAccountType]
            [$strArgCarrierType][$strArgCarrierCode1 . $strArgCarrierCode2] === true
        );
    }

    /**
     * Load an existing Prestashop Carrier object from ID.
     *
     * @param int $intArgCarrierID
     *
     * @return Carrier|null
     */
    public static function getPSCarrierByID($intArgCarrierID)
    {
        TNTOfficiel_Logstack::log();

        // Cache.
        static $arrStaticPSCarrier = array();

        // Carrier ID must be an integer greater than 0.
        if (empty($intArgCarrierID) || $intArgCarrierID != (int)$intArgCarrierID || !((int)$intArgCarrierID > 0)) {
            return null;
        }

        $intCarrierID = (int)$intArgCarrierID;

        // If already loaded.
        if (array_key_exists($intCarrierID, $arrStaticPSCarrier)) {
            $objPSCarrierMem = $arrStaticPSCarrier[$intCarrierID];
            // Check.
            if (Validate::isLoadedObject($objPSCarrierMem)
                && (int)$objPSCarrierMem->id === $intCarrierID
            ) {
                return $objPSCarrierMem;
            }
        }

        // Load Carrier.
        $objPSCarrier = new Carrier($intCarrierID);

        // If Carrier object not available.
        if (!Validate::isLoadedObject($objPSCarrier)
            || (int)$objPSCarrier->id !== $intCarrierID
        ) {
            return null;
        }

        // Add.
        $arrStaticPSCarrier[$intCarrierID] = $objPSCarrier;

        return $objPSCarrier;
    }

    /**
     * Get the Prestashop Carrier associated with this carrier.
     *
     * @return Carrier|null
     */
    public function getPSCarrier()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficielCarrier::getPSCarrierByID($this->id_carrier);
    }

    /**
     * Get the Prestashop Shop associated with this carrier.
     *
     * @return Shop|null
     */
    public function getPSShop()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficielAccount::getPSShopByID($this->id_shop);
    }

    /**
     * Check if a Prestashop carrier ID is a TNTOfficel module one.
     *
     * @param int $intArgCarrierID
     *
     * @return bool
     */
    public static function isTNTOfficielCarrierID($intArgCarrierID)
    {
        TNTOfficiel_Logstack::log();

        $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($intArgCarrierID);
        // If Carrier object not available.
        if ($objPSCarrier === null) {
            return false;
        }

        return $objPSCarrier->external_module_name === TNTOfficiel::MODULE_NAME;
    }

    /**
     * Get the carrier Account model.
     *
     * @return TNTOfficielAccount|null
     */
    public function getTNTAccountModel()
    {
        TNTOfficiel_Logstack::log();

        // If no id_account for this carrier.
        if (!($this->id_account > 0)) {
            $objException = new Exception(
                sprintf(
                    TNTOfficiel::getCodeTranslate('errorUnableLoadForIDStr'),
                    'TNTOfficielAccount',
                    'Carrier',
                    $this->id_carrier
                )
            );
            TNTOfficiel_Logger::logException($objException);

            return null;
        }

        return TNTOfficielAccount::loadAccountID($this->id_account);
    }

    /**
     * Check if a non deleted carrier is already existing for a shop.
     *
     * @param int    $intArgShopID
     * @param string $strArgAccountType
     * @param string $strArgCarrierType
     * @param string $strArgCarrierCode1
     * @param string $strArgCarrierCode2
     *
     * @return bool
     */
    public static function isExist(
        $intArgShopID,
        $strArgAccountType,
        $strArgCarrierType,
        $strArgCarrierCode1,
        $strArgCarrierCode2
    ) {
        TNTOfficiel_Logstack::log();

        $arrObjTNTCarrierModelList = array();

        // Search row.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielCarrier::$definition['table'], 't');
        $objDbQuery->where('id_shop = ' . $intArgShopID);
        $objDbQuery->where('account_type = \'' . pSQL($strArgAccountType) . '\'');
        $objDbQuery->where('carrier_type = \'' . pSQL($strArgCarrierType) . '\'');
        $objDbQuery->where('carrier_code1 = \'' . pSQL($strArgCarrierCode1) . '\'');
        $objDbQuery->where('carrier_code2 = \'' . pSQL($strArgCarrierCode2) . '\'');
        /*
        $objDbQuery->innerJoin(
            'carrier', 'c'
        ,   't.id_carrier = c.id_carrier AND c.deleted = 0'
        );
        */

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found and match accound ID.
        if (is_array($arrDBResult) && count($arrDBResult) > 0) {
            foreach ($arrDBResult as $arrValue) {
                $intCarrierID = (int)$arrValue['id_carrier'];
                $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                // If success.
                if ($objTNTCarrierModel !== null) {
                    $objPSCarrier = $objTNTCarrierModel->getPSCarrier();
                    // If Carrier object available and not deleted.
                    if ($objPSCarrier !== null && !$objPSCarrier->deleted) {
                        $arrObjTNTCarrierModelList[(int)$objTNTCarrierModel->id_carrier] = $objTNTCarrierModel;
                    }
                }
            }
        }

        return count($arrObjTNTCarrierModelList) > 0;
    }

    /**
     * Get the Prestashop Carrier name.
     *
     * @return string
     */
    public function getName()
    {
        TNTOfficiel_Logstack::log();

        $objPSCarrier = $this->getPSCarrier();
        // If Carrier object not available.
        if ($objPSCarrier === null) {
            return 'N/A';
        }

        return $objPSCarrier->name;
    }

    /**
     * Get carrier label.
     *
     * @return stdClass|null
     */
    public static function getCarrierLabel($strArgAccountType, $strArgCarrierType, $strArgCarrierCode1)
    {
        TNTOfficiel_Logstack::log();

        $strCarrierLabel = null;

        $strCarrierKey = $strArgCarrierType . ':' . $strArgCarrierCode1;

        // Get default code informations.
        $arrCarrierCodeInfos = TNTOfficielCarrier::$arrCarrierCodeInfos['*'];
        // Or a specific one for account type if exist.
        if (array_key_exists($strArgAccountType, TNTOfficielCarrier::$arrCarrierCodeInfos)) {
            $arrCarrierCodeInfos = TNTOfficielCarrier::$arrCarrierCodeInfos[$strArgAccountType];
        }

        // Get label using carrier type and code1.
        if (array_key_exists($strCarrierKey, $arrCarrierCodeInfos)) {
            $strCarrierLabel = $arrCarrierCodeInfos[$strCarrierKey]['label'];
            // For specific account type without code information available.
            // but excluding default, ASSU, RP ASSU account type.
            if (!array_key_exists($strArgAccountType, TNTOfficielCarrier::$arrCarrierCodeInfos)
                && !in_array($strArgAccountType, array('*', 'ASSU', 'RP ASSU'))
            ) {
                // Append account type on label.
                $strCarrierLabel .= ' - ' . $strArgAccountType;
            }
        }

        return $strCarrierLabel;
    }

    /**
     * Get carrier information.
     *
     * @return stdClass|null
     */
    public function getCarrierInfos()
    {
        TNTOfficiel_Logstack::log();

        $objCarrierInfos = null;

        $strCarrierKey = $this->carrier_type . ':' . $this->carrier_code1;

        // Get default code informations.
        $arrCarrierCodeInfos = TNTOfficielCarrier::$arrCarrierCodeInfos['*'];
        // Or a specific one for account type if exist.
        if (array_key_exists($this->account_type, TNTOfficielCarrier::$arrCarrierCodeInfos)) {
            $arrCarrierCodeInfos = TNTOfficielCarrier::$arrCarrierCodeInfos[$this->account_type];
        }

        // Get label using carrier type and code1.
        if (array_key_exists($strCarrierKey, $arrCarrierCodeInfos)) {
            $objCarrierInfos = (object)$arrCarrierCodeInfos[$strCarrierKey];
            $objCarrierInfos->label = TNTOfficielCarrier::getCarrierLabel(
                $this->account_type,
                $this->carrier_type,
                $this->carrier_code1
            );
            if (array_key_exists($this->account_type, TNTOfficielCarrier::$arrCarrierTypeInfos)) {
                $objCarrierInfos->description2 = TNTOfficielCarrier::$arrCarrierTypeInfos[$this->account_type];
            }
        }

        return $objCarrierInfos;
    }

    /**
     * Get receiver type parameter for LPSE ESSENTIEL AKA "Essentiel Flexibilité"
     * (for feasibility, liveFeasibility, expeditionCreation in hookDisplayCarrierExtraContent or saveShipment).
     *
     * @param $objArgPSAddressDelivery
     *
     * @return string Receiver Type
     */
    public function getReceiverType($objArgPSAddressDelivery)
    {
        TNTOfficiel_Logstack::log();

        // Essentiel Flexibilité.
        if (in_array($this->account_type, array('LPSE ESSENTIEL'))) {
            if (in_array($this->carrier_type, array('ENTERPRISE', 'INDIVIDUAL'))) {
                $boolIsReceiverB2B = !!trim($objArgPSAddressDelivery->company);

                // Return final receiver type.
                return ($boolIsReceiverB2B ? 'ENTERPRISE' : 'INDIVIDUAL');
            }
        }

        // Return standard receiver type.
        return $this->carrier_type;
    }

    /**
     * Get the account type using carrier info from feasibility.
     *
     * @param string $strArgCarrierType
     * @param string $strArgCarrierCode2
     * @param string $strArgCarrierLabel
     *
     * @return string ['*', 'ALIMENT', 'ASSU', 'RP', 'RP ALIMENT', 'RP ASSU',
     * 'ESP', 'ESP ALIMENT', 'ESP ASSU', 'LPSE', 'FROID']
     */
    public static function getAccountType($strArgCarrierType, $strArgCarrierCode2, $strArgCarrierLabel)
    {
        TNTOfficiel_Logstack::log();

        $strAccountType = '*';
        $arrAccountType = array();

        if ($strArgCarrierCode2 === 'P') {
            $arrAccountType[] = 'RP';
        } elseif ($strArgCarrierCode2 === 'W') {
            $arrAccountType[] = 'ESP';
        } elseif ($strArgCarrierCode2 === 'D') {
            // Commerçants Partenaires.
            //$arrAccountType[] = 'DROPOFFPOINT';
        } elseif ($strArgCarrierCode2 === 'Z') {
            //$arrAccountType[] = 'A Domicile';
        } elseif ($strArgCarrierCode2 === 'E') {
            $arrAccountType[] = 'LPSE';
        }

        if (preg_match('/\bFROID\b/ui', $strArgCarrierLabel) === 1) {
            $arrAccountType[] = 'FROID';
        }
        if (preg_match('/\bASSU\b/ui', $strArgCarrierLabel) === 1) {
            $arrAccountType[] = 'ASSU';
        }
        if (preg_match('/\bESSENTIEL\b/ui', $strArgCarrierLabel) === 1) {
            $arrAccountType[] = 'ESSENTIEL';
        }
        // Aliment may not be detected.
        if (preg_match('/\bALIMENT\b/ui', $strArgCarrierLabel) === 1) {
            $arrAccountType[] = 'ALIMENT';
        }

        if ($strArgCarrierType === 'DEPOT') {
            // Agences TNT.
            //$arrAccountType[] = 'DEPOT';
        }

        if (count($arrAccountType) > 0) {
            $strAccountType = implode(' ', $arrAccountType);
        }

        return $strAccountType;
    }

    /**
     * Modify a current TNT carrier ID to a new one.
     *
     * @param int $intArgCarrierOldID
     * @param int $intArgCarrierNewID
     *
     * @return bool
     */
    public static function updateCarrierID($intArgCarrierOldID, $intArgCarrierNewID)
    {
        TNTOfficiel_Logstack::log();

        // Load the carrier ID.
        $objTNTCarrierModelOld = TNTOfficielCarrier::loadCarrierID($intArgCarrierOldID, false);
        // If fail or not an existing TNT carrier.
        if ($objTNTCarrierModelOld === null) {
            return false;
        }

        // Create a new model for copy.
        $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intArgCarrierNewID, true);
        // If fail or not an existing TNT carrier.
        if ($objTNTCarrierModel === null) {
            return false;
        }

        // Copy.
        $objTNTCarrierModel->id_shop = $objTNTCarrierModelOld->id_shop;
        $objTNTCarrierModel->id_account = $objTNTCarrierModelOld->id_account;
        $objTNTCarrierModel->account_type = $objTNTCarrierModelOld->account_type;
        $objTNTCarrierModel->carrier_type = $objTNTCarrierModelOld->carrier_type;
        $objTNTCarrierModel->carrier_code1 = $objTNTCarrierModelOld->carrier_code1;
        $objTNTCarrierModel->carrier_code2 = $objTNTCarrierModelOld->carrier_code2;
        $objTNTCarrierModel->zones_enabled = $objTNTCarrierModelOld->zones_enabled;
        $objTNTCarrierModel->zones_cloning_enabled = $objTNTCarrierModelOld->zones_cloning_enabled;
        $objTNTCarrierModel->zones_config = $objTNTCarrierModelOld->zones_config;

        return $objTNTCarrierModel->save();
    }

    /**
     * Search for a list of non deleted existing carrier object model, associated to a shop context.
     *
     * @param bool  $boolArgIncludeDeleted include deleted carrier ?
     * @param float $fltArgHeaviestProduct filter result using the heaviest product weight.
     * @param bool  $boolArgIsReceiverB2B  filter result as B2B (true) or B2C (false). null for no filter.
     *
     * @return array list of TNTOfficielCarrier model found.
     */
    public static function getContextCarrierModelList(
        $boolArgIncludeDeleted,
        $fltArgHeaviestProduct = 0.0,
        $boolArgIsReceiverB2B = null
    ) {
        TNTOfficiel_Logstack::log();

        $fltHeaviestProduct = (float)$fltArgHeaviestProduct;

        $arrObjTNTCarrierModelList = array();

        $arrArgIntShopIDList = Shop::getContextListShopID();
        $arrIntShopIDList = array_map('intval', $arrArgIntShopIDList);

        try {
            // Search row for account ID.
            $objDbQuery = new DbQuery();
            $objDbQuery->select('*');
            $objDbQuery->from(TNTOfficielCarrier::$definition['table'], 't');
            $objDbQuery->where('id_shop IN (' . implode(',', $arrIntShopIDList) . ')');
            /*
            $objDbQuery->innerJoin(
                'carrier', 'c'
            ,   't.id_carrier = c.id_carrier AND c.deleted = 0'
            );
            */

            $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
            // If row found and match accound ID.
            if (is_array($arrDBResult) && count($arrDBResult) > 0) {
                foreach ($arrDBResult as $arrValue) {
                    $intCarrierID = (int)$arrValue['id_carrier'];
                    $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                    // If success.
                    if ($objTNTCarrierModel !== null) {
                        $objPSCarrier = $objTNTCarrierModel->getPSCarrier();
                        // If Carrier object available and
                        if ($objPSCarrier !== null
                            // not deleted.
                            && ($boolArgIncludeDeleted || !$objPSCarrier->deleted)
                            // Filter using receiver type (B2B, B2C or unknown).
                            && $objTNTCarrierModel->isAvailableForReceiverType($boolArgIsReceiverB2B)
                            // Filter using heaviest product weight (against package maximum weight).
                            && $objTNTCarrierModel->isAvailableForProductWeight($fltHeaviestProduct)
                        ) {
                            $arrObjTNTCarrierModelList[$objTNTCarrierModel->id_carrier] = $objTNTCarrierModel;
                        }
                    }
                }
            }
        } catch (Exception $objException) {
            TNTOfficiel_Logger::logException($objException);
        }

        return $arrObjTNTCarrierModelList;
    }

    /**
     * Get the list of carrier object model, through live feasibility.
     * Takes optionally into account :
     * - The heaviest product weight.
     * - The receiver address.
     *
     * @param float   $fltArgHeaviestProduct
     * @param Address $objArgAddressDelivery
     *
     * @return array[TNTOfficielCarrier]
     */
    public static function getLiveFeasibilityContextCarrierModelList(
        $fltArgHeaviestProduct = 0.0,
        $objArgAddressDelivery = null
    ) {
        TNTOfficiel_Logstack::log();

        $fltHeaviestProduct = (float)$fltArgHeaviestProduct;

        $arrResult = array();

        // If delivery address object is available.
        if ($objArgAddressDelivery !== null) {
            $strReceiverCountryISO = TNTOfficielReceiver::getCountryISOCode($objArgAddressDelivery->id_country);
            $strReceiverZipCode = trim($objArgAddressDelivery->postcode);
            $strReceiverCity = trim($objArgAddressDelivery->city);
            $boolIsReceiverB2B = !!trim($objArgAddressDelivery->company);

            $arrObjTNTCarrierModelList = TNTOfficielCarrier::getContextCarrierModelList(
                false,
                $fltHeaviestProduct,
                $boolIsReceiverB2B
            );
            // Adding matching carriers with live feasibility.
            /** @var TNTOfficielCarrier $objTNTCarrierModel */
            foreach ($arrObjTNTCarrierModelList as $intCarrierID => $objTNTCarrierModel) {
                $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();
                if ($objTNTCarrierAccountModel === null) {
                    continue;
                }
                // Get cities from the webservice from the given postal code.
                $arrResultCitiesGuide = $objTNTCarrierAccountModel->citiesGuide(
                    $strReceiverCountryISO,
                    $strReceiverZipCode,
                    $strReceiverCity
                );
                // If the country is not supported
                // or the city does not match the postcode for the delivery address (without communication error).
                if (!$arrResultCitiesGuide['boolIsCountrySupported']
                    || (!$arrResultCitiesGuide['boolIsRequestComError']
                        && !$arrResultCitiesGuide['boolIsCityNameValid']
                    )
                ) {
                    continue;
                }

                $arrTNTServiceList = $objTNTCarrierAccountModel->liveFeasibility(
                    $strReceiverZipCode,
                    $strReceiverCity,
                    array($objTNTCarrierModel->carrier_type)
                );

                foreach ($arrTNTServiceList as $arrTNTService) {
                    if ($objTNTCarrierModel->account_type === $arrTNTService['accountType']
                        && $objTNTCarrierModel->carrier_type === $arrTNTService['carrierType']
                        && $objTNTCarrierModel->carrier_code1 === $arrTNTService['carrierCode1']
                        && $objTNTCarrierModel->carrier_code2 === $arrTNTService['carrierCode2']
                    ) {
                        $arrResult[$intCarrierID] = $objTNTCarrierModel;
                    }
                }
            }
        } else {
            // If there is no delivery address, use all carriers using only heaviest product.
            $arrResult = TNTOfficielCarrier::getContextCarrierModelList(false, $fltHeaviestProduct);
        }

        return $arrResult;
    }

    /**
     * Force all current carrier settings.
     *
     * @return bool
     */
    public static function forceAllCarrierDefaultValues()
    {
        TNTOfficiel_Logstack::log();

        $boolResult = true;

        $arrObjTNTCarrierModelList = TNTOfficielCarrier::getContextCarrierModelList(false);
        /** @var TNTOfficielCarrier $objTNTCarrierModel */
        foreach ($arrObjTNTCarrierModelList as /*$intCarrierID =>*/ $objTNTCarrierModel) {
            $boolResult = $objTNTCarrierModel->forceCarrierDefaultValues() && $boolResult;
        }

        TNTOfficielCarrier::autoClean();

        return $boolResult;
    }

    /**
     * Force a current carrier settings.
     *
     * @param string $strArgCarrierCode
     *
     * @return bool
     */
    public function forceCarrierDefaultValues()
    {
        TNTOfficiel_Logstack::log();

        $this->forceAssoUniqueShop();
        //$this->forceAssoExcludeGroup();

        return true;
    }

    /**
     * Force the groups exclusion (unused).
     *
     * @return bool
     */
    protected function forceAssoExcludeGroup()
    {
        TNTOfficiel_Logstack::log();

        // Users groups to exclude.
        $arrCarrierGroupExcludeConfigList = array(
            //'PS_UNIDENTIFIED_GROUP',
            //'PS_GUEST_GROUP'
        );

        $objPSCarrier = $this->getPSCarrier();
        // If Carrier object available.
        if ($objPSCarrier !== null) {
            // Get all users groups associated with the TNT carrier.
            $arrCarrierGroups = $objPSCarrier->getGroups();
            // If there is currently at least one users groups set.
            if (is_array($arrCarrierGroups) && count($arrCarrierGroups) > 0) {
                // Current users groups set.
                $arrCarrierGroupSetIDList = array();
                foreach ($arrCarrierGroups as $arrRowCarrierGroup) {
                    // DB request fail. stop here.
                    if (!array_key_exists('id_group', $arrRowCarrierGroup)) {
                        return false;
                    }
                    $arrCarrierGroupSetIDList[] = (int)$arrRowCarrierGroup['id_group'];
                }
                // Get users groups ID list to exclude.
                $arrCarrierGroupExcludeIDList = array();
                foreach ($arrCarrierGroupExcludeConfigList as $strGroupExcludeConfig) {
                    $arrCarrierGroupExcludeIDList[] = (int)Configuration::get($strGroupExcludeConfig);
                }

                // Get groups previously set, minus groups to exclude.
                $arrCarrierGroupsApply = array_diff($arrCarrierGroupSetIDList, $arrCarrierGroupExcludeIDList);

                // If groups change.
                if (count(array_diff($arrCarrierGroupSetIDList, $arrCarrierGroupsApply)) > 0) {
                    // Force carrier users groups (delete all, then set).
                    $objPSCarrier->setGroups($arrCarrierGroupsApply, true);
                }
            }
        }

        return true;
    }

    /**
     * Force the shop associations to an unique one.
     *
     * @return bool|string
     */
    protected function forceAssoUniqueShop()
    {
        TNTOfficiel_Logstack::log();

        if (!Shop::isFeatureActive()
            || !Shop::isTableAssociated('carrier')
        ) {
            return false;
        }

        // List associated shop to current carrier.
        $arrCarrierAssoCurrentShopIDList = array();
        $arrDBResultCarrierAssoCurrentShopIDList = TNTOfficiel_Tools::getDbSelect(
            'SELECT id_shop FROM `' . _DB_PREFIX_ . 'carrier_shop` WHERE `id_carrier` = ' . $this->id_carrier
        );
        // If row found and match carrier ID.
        if (is_array($arrDBResultCarrierAssoCurrentShopIDList) && count($arrDBResultCarrierAssoCurrentShopIDList) > 0) {
            foreach ($arrDBResultCarrierAssoCurrentShopIDList as $arrRowAssoCurrentShop) {
                $arrCarrierAssoCurrentShopIDList[] = (int)$arrRowAssoCurrentShop['id_shop'];
            }
        }

        // If only one correct shop ID associated (no change needed).
        if (count($arrCarrierAssoCurrentShopIDList) === 1
            && in_array($this->id_shop, $arrCarrierAssoCurrentShopIDList, true)
        ) {
            return false;
        }

        $arrCarrierAssoForcedShopIDList = array($this->id_shop);

        // Delete non associated shop ID for the current carrier.
        TNTOfficiel_Tools::getDbDelete(
            'carrier_shop',
            '`id_carrier` = ' . (int)$this->id_carrier
            . ' AND `id_shop` NOT IN (' . implode(',', $arrCarrierAssoForcedShopIDList) . ')'
        );

        // Create insert data.
        $arrRowInsertCarrierAssoForcedShopIDList = array();
        foreach ($arrCarrierAssoForcedShopIDList as $intForcedShopID) {
            $arrRowInsertCarrierAssoForcedShopIDList[] = array(
                'id_carrier' => (int)$this->id_carrier,
                'id_shop' => (int)$intForcedShopID,
            );
        }

        // Insert associated shop ID to the current carrier.
        // Ignoring duplicate (primary key, unique).
        return TNTOfficiel_Tools::getDbInsert(
            'carrier_shop',
            $arrRowInsertCarrierAssoForcedShopIDList
        );
    }

    /**
     * Auto delete unused Prestashop carrier.
     */
    public static function autoClean()
    {
        TNTOfficiel_Logstack::log();

        // Get all module carriers non deleted, enabled or not.
        $arrAllCarrier = Carrier::getCarriers(
            //(int)$this->context->language->id,
            (int)Configuration::get('PS_LANG_DEFAULT'),
            false,
            false,
            false,
            null,
            Carrier::CARRIERS_MODULE
        );
        foreach ($arrAllCarrier as $arrCarrier) {
            $intCarrierID = (int)$arrCarrier['id_carrier'];
            // If is a TNT Carrier.
            if (TNTOfficielCarrier::isTNTOfficielCarrierID($intCarrierID)) {
                $objPSCarrier = TNTOfficielCarrier::getPSCarrierByID($intCarrierID);
                // If Carrier object available.
                if ($objPSCarrier !== null) {
                    $objTNTCarrierModel = TNTOfficielCarrier::loadCarrierID($intCarrierID, false);
                    // If TNT carrier model does not exist.
                    if ($objTNTCarrierModel === null) {
                        $objException = new Exception(
                            sprintf(
                                TNTOfficiel::getCodeTranslate('errorDeleteIDCauseNotFoundStr'),
                                'Carrier',
                                $intCarrierID,
                                'TNTOfficielCarrier'
                            )
                        );
                        TNTOfficiel_Logger::logException($objException);
                        // Delete carrier.
                        $objPSCarrier->active = false;
                        $objPSCarrier->deleted = true;
                        // Do not eval for associated order.
                        $objPSCarrier->external_module_name = TNTOfficiel::MODULE_NAME . '_';
                        $objPSCarrier->save();
                    } else {
                        $objTNTCarrierAccountModel = $objTNTCarrierModel->getTNTAccountModel();
                        // If no TNT account or account is deleted.
                        if ($objTNTCarrierAccountModel === null
                            || $objTNTCarrierAccountModel->deleted
                        ) {
                            $strCodeTranslate = ($objTNTCarrierAccountModel === null ?
                                'errorDeleteIDCauseNotFoundStr' : 'errorDeleteIDCauseDeleteStr');

                            $objException = new Exception(
                                sprintf(
                                    TNTOfficiel::getCodeTranslate($strCodeTranslate),
                                    'Carrier',
                                    $intCarrierID,
                                    'TNTOfficielAccount'
                                )
                            );
                            TNTOfficiel_Logger::logException($objException);
                            // Delete carrier.
                            $objPSCarrier->active = false;
                            $objPSCarrier->deleted = true;
                            $objPSCarrier->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Get current shop weight unit from Prestashop configuration.
     *
     * @return string
     */
    public static function getShopWeightUnit()
    {
        return trim(Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT')));
    }

    /**
     * Get list of weight unit and factor.
     *
     * @return array
     */
    public static function getWeightUnitList()
    {
        return TNTOfficielCarrier::$arrWeightUnitFactorList;
    }

    /**
     * Get Prestashop weight converted in Kg.
     *
     * @param float $fltArgPSWeight Prestashop weight to convert.
     *
     * @return float
     */
    public static function getShopWeightKg($fltArgPSWeight)
    {
        $fltWeight = (float)$fltArgPSWeight;
        $arrWeightUnitList = TNTOfficielCarrier::getWeightUnitList();
        $strShopWeightUnit = TNTOfficielCarrier::getShopWeightUnit();

        if (array_key_exists($strShopWeightUnit, $arrWeightUnitList)) {
            return $fltWeight / $arrWeightUnitList[$strShopWeightUnit];
        }

        return $fltWeight;
    }

    /**
     * Get the maximum package weight (Kg).
     *
     * @return float
     */
    public function getMaxPackageWeight()
    {
        // Essentiel Flexibilité.
        if (in_array($this->account_type, array('LPSE ESSENTIEL'))) {
            return 30.0;
        }

        if (in_array($this->carrier_type, array('ENTERPRISE', 'DEPOT'))) {
            return 30.0;
        }

        // INDIVIDUAL, DROPOFFPOINT.
        return 30.0;
    }

    /**
     * Is carrier available for a product weight.
     *
     * @param float $fltArgHeaviestProduct the heaviest product weight (Kg).
     *
     * @return bool
     */
    public function isAvailableForProductWeight($fltArgHeaviestProduct = 0.0)
    {
        TNTOfficiel_Logstack::log();

        $fltHeaviestProduct = (float)$fltArgHeaviestProduct;

        // Filter Weight : If heaviest product is in B2C range (less or equal to 20 kg)
        // or in B2B range (greater than 20 kg and less or equal to 30 kg) with B2B option.
        return ($fltHeaviestProduct <= $this->getMaxPackageWeight());
    }

    /**
     * Is carrier available for a receiver.
     *
     * @param bool $boolArgIsReceiverB2B true for B2B, false for B2C, null for unknown.
     *
     * @return bool
     */
    public function isAvailableForReceiverType($boolArgIsReceiverB2B = null)
    {
        TNTOfficiel_Logstack::log();

        // Filter B2B/B2C :
        // If receiver business type is unknown,
        // or account type is LPSE ESSENTIEL (available for B2B and B2C receiver).
        // or carrier type is DROPOFFPOINT or DEPOT,
        // or receiver and carrier are B2B, or receiver and carrier are B2C.
        return ($boolArgIsReceiverB2B === null
            || in_array($this->account_type, array('LPSE ESSENTIEL'))
            || $this->isDeliveryPoint()
            || ($boolArgIsReceiverB2B === true && $this->carrier_type === 'ENTERPRISE')
            || ($boolArgIsReceiverB2B === false && $this->carrier_type === 'INDIVIDUAL')
        );
    }

    /**
     * Update the hard to reach areas zipcode list.
     *
     * @return bool
     */
    public static function updateHRAZipCodeList()
    {
        TNTOfficiel_Logstack::log();

        $strFileLocation = TNTOfficiel::getDirModule() . TNTOfficielCarrier::PATH_HRA_JSON;

        $arrResult = TNTOfficiel_Tools::cURLRequest(
            TNTOfficielCarrier::URL_HRA_JSON,
            array(
                CURLOPT_HTTPHEADER => array(
                    'User-Agent: PHP/cURL',
                    'Accept: application/json',
                    'Connection: close',
                ),
            )
        );

        $arrResponse = TNTOfficiel_Tools::decJSON($arrResult['response']);
        if (is_array($arrResponse) && array_key_exists('default', $arrResponse)) {
            return file_put_contents($strFileLocation, $arrResult['response']) > 0;
        }

        return false;
    }

    /**
     * Get the hard to reach areas zipcode list.
     *
     * @return array
     */
    public static function getHRAZipCodeList()
    {
        TNTOfficiel_Logstack::log();

        $strFileLocation = TNTOfficiel::getDirModule() . TNTOfficielCarrier::PATH_HRA_JSON;
        if (file_exists($strFileLocation)) {
            $strFileContent = Tools::file_get_contents($strFileLocation);
            if ($strFileContent !== false) {
                $arrFileContent = TNTOfficiel_Tools::decJSON($strFileContent);
                if (array_key_exists('default', $arrFileContent) && is_array($arrFileContent['default'])) {
                    return $arrFileContent['default'];
                }
            }
        }

        return array();
    }

    /**
     * Get delivery points list.
     * Only available for carrier type DROPOFFPOINT or DEPOT.
     *
     * @param $strArgReceiverZipCode
     * @param $strArgReceiverCity
     *
     * @return array|null
     */
    public function getDeliveryPoints($strArgReceiverZipCode, $strArgReceiverCity)
    {
        TNTOfficiel_Logstack::log();

        $objTNTCarrierAccountModel = $this->getTNTAccountModel();
        // If no account available for this carrier.
        if ($objTNTCarrierAccountModel === null) {
            return null;
        }

        $arrResultDeliveryPoints = null;

        if ($this->carrier_type === 'DROPOFFPOINT') {
            // Get an Estimated delivery date from the carrier selected in cart.
            $strArgEDD = null;
            $arrLiveFeasibility = $this->liveFeasibility($strArgReceiverZipCode, $strArgReceiverCity);
            if (is_array($arrLiveFeasibility)) {
                $strArgEDD = $arrLiveFeasibility['dueDate'];
            }
            // Call WS to get list of delivery points.
            $arrResultDeliveryPoints = $objTNTCarrierAccountModel->dropOffPoints(
                $strArgReceiverZipCode,
                $strArgReceiverCity,
                $strArgEDD
            );
        } elseif ($this->carrier_type === 'DEPOT') {
            // Call WS to get list of delivery points
            $arrResultDeliveryPoints = $objTNTCarrierAccountModel->tntDepots(
                $strArgReceiverZipCode,
                $strArgReceiverCity
            );
        }

        return $arrResultDeliveryPoints;
    }

    /**
     * Is delivery point required for this carrier.
     *
     * @return bool
     */
    public function isDeliveryPoint()
    {
        // Is delivery point (DROPOFFPOINT or DEPOT).
        return in_array($this->carrier_type, array('DROPOFFPOINT', 'DEPOT'));
    }

    /**
     * Get the delivery point type (xett or pex) for this carrier.
     *
     * @return null|string
     */
    public function getDeliveryPointType()
    {
        if ($this->carrier_type === 'DEPOT') {
            return 'pex';
        } elseif ($this->carrier_type === 'DROPOFFPOINT') {
            return 'xett';
        }

        return null;
    }

    /**
     * Is HRA applicable for this carrier.
     *
     * @return bool
     */
    public function isHRAApplicable()
    {
        // No HRA for delivery point.
        return !$this->isDeliveryPoint();
    }

    /**
     * Check if a zipcode match the hard to reach areas.
     *
     * @param string $strArgZipCode
     *
     * @return bool
     */
    public function isAnHRAZipCode($strArgZipCode)
    {
        TNTOfficiel_Logstack::log();

        if (!$this->isHRAApplicable()) {
            return false;
        }

        $arrHRAZipcodeList = TNTOfficielCarrier::getHRAZipCodeList();

        return in_array($strArgZipCode, $arrHRAZipcodeList, true);
    }

    /**
     * Get carrier live feasibility options for a receiver.
     * Response include the estimated delivery date.
     *
     * @param string $strArgReceiverZipCode
     * @param string $strArgReceiverCity
     * @param string $strArgReceiverType (optional) Allow override of carrier type with a receiver type.
     *
     * @return array|null
     */
    public function liveFeasibility($strArgReceiverZipCode, $strArgReceiverCity, $strArgReceiverType = null)
    {
        TNTOfficiel_Logstack::log();

        $objTNTCarrierAccountModel = $this->getTNTAccountModel();
        // If no account available for this carrier.
        if ($objTNTCarrierAccountModel === null) {
            return null;
        }

        $strReceiverType = $this->carrier_type;
        if (in_array($strArgReceiverType, TNTOfficielCarrier::$arrCarrierTypeList)) {
            $strReceiverType = $strArgReceiverType;
        }

        $arrTNTServiceList = $objTNTCarrierAccountModel->liveFeasibility(
            $strArgReceiverZipCode,
            $strArgReceiverCity,
            array($strReceiverType)
        );

        foreach ($arrTNTServiceList as $arrTNTService) {
            if ($this->account_type === $arrTNTService['accountType']
                && $strReceiverType === $arrTNTService['carrierType']
                && $this->carrier_code1 === $arrTNTService['carrierCode1']
                && $this->carrier_code2 === $arrTNTService['carrierCode2']
            ) {
                return array(
                    'shippingDate' => $arrTNTService['shippingDate'],
                    'dueDate' => $arrTNTService['dueDate'],
                    'saturdayDelivery' => (bool)$arrTNTService['saturdayDelivery'],
                    'afternoonDelivery' => (bool)$arrTNTService['afternoonDelivery'],
                    'insurance' => (bool)$arrTNTService['insurance'],
                    'priorityGuarantee' => (bool)$arrTNTService['priorityGuarantee'],
                );
            }
        }

        return null;
    }

    /**
     * Get carrier feasibility options for a receiver.
     * Response include the estimated delivery date.
     *
     * @param string      $strArgReceiverZipCode
     * @param string      $strArgReceiverCity
     * @param string      $strArgShippingDate         (optional)
     * @param string|null $strArgReceiverTypeOverride (optional) Allow override of carrier type with a receiver type.
     *
     * @return array|null
     */
    public function feasibility(
        $strArgReceiverZipCode,
        $strArgReceiverCity,
        $strArgShippingDate = null,
        $strArgReceiverTypeOverride = null
    ) {
        TNTOfficiel_Logstack::log();

        $objTNTCarrierAccountModel = $this->getTNTAccountModel();
        // If no account available for this carrier.
        if ($objTNTCarrierAccountModel === null) {
            return null;
        }

        $strReceiverType = $this->carrier_type;
        if (in_array($strArgReceiverTypeOverride, TNTOfficielCarrier::$arrCarrierTypeList)) {
            $strReceiverType = $strArgReceiverTypeOverride;
        }

        $arrTNTServiceList = $objTNTCarrierAccountModel->feasibility(
            $strArgReceiverZipCode,
            $strArgReceiverCity,
            $strArgShippingDate,
            array($strReceiverType)
        );

        foreach ($arrTNTServiceList as $arrTNTService) {
            if ($this->account_type === $arrTNTService['accountType']
                && $strReceiverType === $arrTNTService['carrierType']
                && $this->carrier_code1 === $arrTNTService['carrierCode1']
                && $this->carrier_code2 === $arrTNTService['carrierCode2']
            ) {
                return array(
                    'shippingDate' => $arrTNTService['shippingDate'],
                    'dueDate' => $arrTNTService['dueDate'],
                    'saturdayDelivery' => (bool)$arrTNTService['saturdayDelivery'],
                    'afternoonDelivery' => (bool)$arrTNTService['afternoonDelivery'],
                    'insurance' => (bool)$arrTNTService['insurance'],
                    'priorityGuarantee' => (bool)$arrTNTService['priorityGuarantee'],
                );
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isZonesEnabled()
    {
        TNTOfficiel_Logstack::log();

        return (bool)$this->zones_enabled;
    }

    /**
     * @param bool $zones_enabled
     */
    public function setZonesEnabled($zones_enabled)
    {
        TNTOfficiel_Logstack::log();

        $this->zones_enabled = (bool)$zones_enabled;

        return $this->save();
    }

    /**
     * @return bool
     */
    public function isZonesCloningEnabled()
    {
        TNTOfficiel_Logstack::log();

        return $this->zones_cloning_enabled;
    }

    /**
     * @param $zones__cloning_enabled
     */
    public function setZonesCloningEnabled($zones_cloning_enabled)
    {
        TNTOfficiel_Logstack::log();

        $this->zones_cloning_enabled = $zones_cloning_enabled;

        return $this->save();
    }

    /**
     * Get all zones configuration or the specified one if exist.
     *
     * @param int $intArgZoneConfID
     *
     * @return array|null
     */
    public function getZonesConf($intArgZoneConfID = null)
    {
        TNTOfficiel_Logstack::log();

        $arrZonesConfList = TNTOfficiel_Tools::unserialize($this->zones_config);

        if (!is_array($arrZonesConfList)) {
            $arrZoneConfDefault = array(
                'strRangeType' => 'weight',
                'arrRangeWeightList' => array(),
                'fltRangeWeightPricePerKg' => 0.0,
                'fltRangeWeightLimitMax' => 0.0,
                'arrRangePriceList' => array(),
                'strOutOfRangeBehavior' => 'lastrange',
                'fltHRAAdditionalCost' => 0.0,
                'fltMarginPercent' => 0.0,
            );

            $arrZonesConfList = array(
                $arrZoneConfDefault,
                $arrZoneConfDefault,
                $arrZoneConfDefault,
            );
        }

        // If the zone ID is specified.
        if ($intArgZoneConfID !== null) {
            // If found.
            if (array_key_exists($intArgZoneConfID, $arrZonesConfList)) {
                // Get it.
                return $arrZonesConfList[$intArgZoneConfID];
            }

            // Not found.
            return array();
        }

        // All Zones
        return $arrZonesConfList;
    }

    /**
     * Save a zone configuration.
     *
     * @param int   $intArgZoneConfID
     * @param array $arrArgZoneConf
     *
     * @return bool
     */
    private function saveZoneConf($intArgZoneConfID, $arrArgZoneConf)
    {
        TNTOfficiel_Logstack::log();

        $arrZonesConfList = $this->getZonesConf();
        $arrZonesConfList[$intArgZoneConfID] = $arrArgZoneConf;

        // Filtering zones index.
        $arrZonesConfListSave = array_intersect_key(
            $arrZonesConfList,
            array(array(), array(), array())
        );

        $this->zones_config = TNTOfficiel_Tools::serialize($arrZonesConfListSave);

        return $this->save();
    }

    /**
     * Check if a zone is configured using a minimum of one limit in corresponding range type.
     *
     * @param int $intArgZoneConfID
     *
     * @return bool
     */
    public function isZoneConfigured($intArgZoneConfID)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        if ($this->isZoneRangeByWeight($intArgZoneConfID)
            && is_array($arrZoneConf)
            && array_key_exists('arrRangeWeightList', $arrZoneConf)
            && is_array($arrZoneConf['arrRangeWeightList'])
            && count($arrZoneConf['arrRangeWeightList']) > 0
        ) {
            return true;
        }

        if (!$this->isZoneRangeByWeight($intArgZoneConfID)
            && is_array($arrZoneConf)
            && array_key_exists('arrRangePriceList', $arrZoneConf)
            && is_array($arrZoneConf['arrRangePriceList'])
            && count($arrZoneConf['arrRangePriceList']) > 0
        ) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param int    $intArgZoneConfID
     * @param string $strArgRangeType
     *
     * @return bool
     */
    public function setZoneRangeType($intArgZoneConfID, $strArgRangeType = 'weight')
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $strRangeType = 'price';
        if ($strArgRangeType === 'weight') {
            $strRangeType = 'weight';
        }

        $arrZoneConf['strRangeType'] = $strRangeType;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int $intArgZoneConfID
     *
     * @return bool
     */
    public function isZoneRangeByWeight($intArgZoneConfID)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        if (is_array($arrZoneConf)
            && array_key_exists('strRangeType', $arrZoneConf)
            && $arrZoneConf['strRangeType'] === 'weight'
        ) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param array $arrArgRangeWeightList
     * @param float $fltArgRangeWeightPricePerKg
     * @param float $fltArgRangeWeightLimitMax
     *
     * @return bool
     */
    public function setZoneRangeWeightList(
        $intArgZoneConfID,
        array $arrArgRangeWeightList = array(),
        $fltArgRangeWeightPricePerKg = 0.0,
        $fltArgRangeWeightLimitMax = 0.0
    ) {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $arrRangeWeightList = array();
        foreach ($arrArgRangeWeightList as $key => &$value) {
            $arrRangeWeightList[(string)$key] = (float)$value;
        }

        $arrZoneConf['arrRangeWeightList'] = $arrRangeWeightList;
        $arrZoneConf['fltRangeWeightPricePerKg'] = (float)$fltArgRangeWeightPricePerKg;
        $arrZoneConf['fltRangeWeightLimitMax'] = (float)$fltArgRangeWeightLimitMax;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param float $fltArgCartWeight
     *
     * @return float|false false if disabled.
     */
    public function getZoneRangeWeightCost($intArgZoneConfID, $fltArgCartWeight)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $arrRangeWeightList = $arrZoneConf['arrRangeWeightList'];
        $fltRangeWeightPricePerKg = $arrZoneConf['fltRangeWeightPricePerKg'];
        $fltRangeWeightLimitMax = $arrZoneConf['fltRangeWeightLimitMax'];

        $fltWeightCost = 0.0;

        $strFltRangeLimitMatch = (string)-1;
        $strFltRangeLimitLast = (string)-1;

        $strFltRangeLimitWeight = (string)-1;
        $fltRangeLimitCost = 0;

        foreach ($arrRangeWeightList as $strFltRangeLimitWeight => $fltRangeLimitCost) {
            if ($strFltRangeLimitMatch == -1 && $fltArgCartWeight <= $strFltRangeLimitWeight) {
                $strFltRangeLimitMatch = (string)$strFltRangeLimitWeight;
            }
            $strFltRangeLimitLast = (string)$strFltRangeLimitWeight;
        }

        // If no max limit defined, but there is a price per kg.
        if (!($fltRangeWeightLimitMax > 0) && $fltRangeWeightPricePerKg > 0) {
            // Max limit is set to the cart weight, to keep usage of the price per kg.
            $fltRangeWeightLimitMax = $fltArgCartWeight;
        }

        // If max defined and is greater than last limit.
        if ($fltRangeWeightLimitMax > $strFltRangeLimitLast) {
            while (++$strFltRangeLimitWeight <= $fltRangeWeightLimitMax) {
                $fltRangeLimitCost += $fltRangeWeightPricePerKg;
                // Extending range.
                $arrRangeWeightList[(string)$strFltRangeLimitWeight] = $fltRangeLimitCost;
                if ($strFltRangeLimitMatch == -1 && $fltArgCartWeight <= $strFltRangeLimitWeight) {
                    $strFltRangeLimitMatch = (string)$strFltRangeLimitWeight;
                }
            }
            $strFltRangeLimitLast = (string)$fltRangeWeightLimitMax;
            // Termination.
            if (!array_key_exists((string)$fltRangeWeightLimitMax, $arrRangeWeightList)) {
                $fltRangeLimitCost += $fltRangeWeightPricePerKg;
                $arrRangeWeightList[(string)$fltRangeWeightLimitMax] = $fltRangeLimitCost;
                if ($strFltRangeLimitMatch == -1 && $fltArgCartWeight <= $fltRangeWeightLimitMax) {
                    $strFltRangeLimitMatch = (string)$fltRangeWeightLimitMax;
                }
            }
        }

        // Range match.
        if (array_key_exists($strFltRangeLimitMatch, $arrRangeWeightList)) {
            $fltWeightCost = $arrRangeWeightList[$strFltRangeLimitMatch];
        }

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'id_carrier' => $this->id_carrier,
                'intArgZoneConfID' => $intArgZoneConfID,
                'fltArgCartWeight' => $fltArgCartWeight,
                'arrRangeWeightList' => $arrRangeWeightList,
                'fltRangeLimitMatch' => $strFltRangeLimitMatch,
                'fltRangeLimitLast' => $strFltRangeLimitLast,
                'fltWeightCost' => $fltWeightCost,
                'boolIsOutOfRange' => ($strFltRangeLimitLast == -1 || ($fltArgCartWeight > $strFltRangeLimitLast)),
            )
        );

        // Out of range.
        if ($strFltRangeLimitLast == -1 || ($fltArgCartWeight > $strFltRangeLimitLast)) {
            if ($this->isZoneOutOfRangeDisabled($intArgZoneConfID)) {
                // Carrier is disabled.
                return false;
            } else {
                // Use the last limit.
                if (array_key_exists($strFltRangeLimitLast, $arrRangeWeightList)) {
                    $fltWeightCost = $arrRangeWeightList[$strFltRangeLimitLast];
                }
            }
        }

        return $fltWeightCost;
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param array $arrArgRangePriceList
     *
     * @return bool
     */
    public function setZoneRangePriceList(
        $intArgZoneConfID,
        array $arrArgRangePriceList = array()
    ) {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $arrRangePriceList = array();
        foreach ($arrArgRangePriceList as $key => &$value) {
            $arrRangePriceList[(string)$key] = (float)$value;
        }

        $arrZoneConf['arrRangePriceList'] = $arrRangePriceList;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param float $fltArgCartPrice
     *
     * @return float|false false if disabled.
     */
    public function getZoneRangePriceCost($intArgZoneConfID, $fltArgCartPrice)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $arrRangePriceList = $arrZoneConf['arrRangePriceList'];

        $fltPriceCost = 0.0;

        $strFltRangeLimitMatch = (string)-1;
        $strFltRangeLimitLast = (string)-1;
        foreach ($arrRangePriceList as $strFltRangeLimitPrice => $fltRangeLimitCost) {
            if ($strFltRangeLimitMatch == -1 && $fltArgCartPrice < $strFltRangeLimitPrice) {
                $strFltRangeLimitMatch = (string)$strFltRangeLimitPrice;
            }
            $strFltRangeLimitLast = (string)$strFltRangeLimitPrice;
        }

        // Range match.
        if (array_key_exists($strFltRangeLimitMatch, $arrRangePriceList)) {
            $fltPriceCost = $arrRangePriceList[$strFltRangeLimitMatch];
        }

        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'id_carrier' => $this->id_carrier,
                'intArgZoneConfID' => $intArgZoneConfID,
                'fltArgCartPrice' => $fltArgCartPrice,
                'arrRangePriceList' => $arrRangePriceList,
                'fltRangeLimitMatch' => $strFltRangeLimitMatch,
                'fltRangeLimitLast' => $strFltRangeLimitLast,
                'fltPriceCost' => $fltPriceCost,
                'boolIsOutOfRange' => ($strFltRangeLimitLast == -1 || ($fltArgCartPrice > $strFltRangeLimitLast)),
            )
        );

        // Out of range.
        if ($strFltRangeLimitLast == -1 || ($fltArgCartPrice >= $strFltRangeLimitLast)) {
            if ($this->isZoneOutOfRangeDisabled($intArgZoneConfID)) {
                // Carrier is disabled.
                return false;
            } else {
                if (array_key_exists($strFltRangeLimitLast, $arrRangePriceList)) {
                    $fltPriceCost = $arrRangePriceList[$strFltRangeLimitLast];
                }
            }
        }

        return $fltPriceCost;
    }

    /**
     * Set a zone configuration out of range behavior.
     *
     * @param int    $intArgZoneConfID
     * @param string $strArgOutOfRangeBehavior
     *
     * @return bool
     */
    public function setZoneOutOfRangeBehavior($intArgZoneConfID, $strArgOutOfRangeBehavior = 'lastrange')
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $strOutOfRangeBehavior = 'disabled';
        if ($strArgOutOfRangeBehavior === 'lastrange') {
            $strOutOfRangeBehavior = 'lastrange';
        }

        $arrZoneConf['strOutOfRangeBehavior'] = $strOutOfRangeBehavior;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int $intZoneConfID
     *
     * @return bool
     */
    public function isZoneOutOfRangeDisabled($intZoneConfID)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intZoneConfID);

        if (is_array($arrZoneConf)
            && array_key_exists('strOutOfRangeBehavior', $arrZoneConf)
            && $arrZoneConf['strOutOfRangeBehavior'] === 'lastrange'
        ) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param float $fltArgHRAAdditionalCost
     *
     * @return bool
     */
    public function setZoneHRAAdditionalCost($intArgZoneConfID, $fltArgHRAAdditionalCost)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $fltHRAAdditionalCost = 0.0;
        if ($fltArgHRAAdditionalCost > 0.0) {
            $fltHRAAdditionalCost = $fltArgHRAAdditionalCost;
        }

        $arrZoneConf['fltHRAAdditionalCost'] = $fltHRAAdditionalCost;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int $intArgZoneConfID
     *
     * @return float
     */
    public function getZoneHRAAdditionalCost($intArgZoneConfID)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        if (is_array($arrZoneConf)
            && array_key_exists('fltHRAAdditionalCost', $arrZoneConf)
            && $arrZoneConf['fltHRAAdditionalCost'] > 0.0
        ) {
            return $arrZoneConf['fltHRAAdditionalCost'];
        }

        return 0.0;
    }

    /**
     *
     * @param int   $intArgZoneConfID
     * @param float $fltArgMarginPercent
     *
     * @return bool
     */
    public function setZoneMarginPercent($intArgZoneConfID, $fltArgMarginPercent)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        $fltMarginPercent = 0.0;
        if ($fltArgMarginPercent > 0.0) {
            $fltMarginPercent = $fltArgMarginPercent;
        }

        $arrZoneConf['fltMarginPercent'] = $fltMarginPercent;

        return $this->saveZoneConf($intArgZoneConfID, $arrZoneConf);
    }

    /**
     *
     * @param int $intArgZoneConfID
     *
     * @return float
     */
    public function getZoneMarginPercent($intArgZoneConfID)
    {
        TNTOfficiel_Logstack::log();

        $arrZoneConf = $this->getZonesConf($intArgZoneConfID);

        if (is_array($arrZoneConf)
            && array_key_exists('fltMarginPercent', $arrZoneConf)
            && $arrZoneConf['fltMarginPercent'] > 0.0
        ) {
            return $arrZoneConf['fltMarginPercent'];
        }

        return 0.0;
    }

    /**
     *
     * @return float|false|null false if carrier is not available, null if must use native Prestashop configuration.
     */
    public function getPrice($fltArgCartWeight, $fltArgCartPrice, $strArgReceiverZipCode = null)
    {
        TNTOfficiel_Logstack::log();

        if (!$this->zones_enabled) {
            // Use Prestashop configuration.
            return null;
        }

        $objTNTCarrierAccountModel = $this->getTNTAccountModel();
        if ($objTNTCarrierAccountModel === null) {
            // Use Prestashop configuration.
            return null;
        }

        // Default Zone.
        $intZoneConfID = 0;
        $boolIsAnHRAZipCode = false;

        // If delivery address postcode is available.
        if ($strArgReceiverZipCode !== null) {
            $intZoneConfID = $objTNTCarrierAccountModel->getZipCodeZone($strArgReceiverZipCode);
            $boolIsAnHRAZipCode = $this->isAnHRAZipCode($strArgReceiverZipCode);
        }

        // If matching zone is not configured.
        if (!$this->isZoneConfigured($intZoneConfID)) {
            // Use default zone.
            $intZoneConfID = 0;
        }

        if ($this->isZoneRangeByWeight($intZoneConfID)) {
            $fltPrice = $this->getZoneRangeWeightCost($intZoneConfID, $fltArgCartWeight);
        } else {
            $fltPrice = $this->getZoneRangePriceCost($intZoneConfID, $fltArgCartPrice);
        }

        // Disabled.
        if ($fltPrice === false) {
            // Carrier is disabled.
            return false;
        }

        // Add HRA Additional cost only for non free shipping.
        if ($boolIsAnHRAZipCode && $fltPrice > 0.0) {
            $fltPrice += $this->getZoneHRAAdditionalCost($intZoneConfID);
        }
        // Add Margin.
        $fltPrice += ($this->getZoneMarginPercent($intZoneConfID) * $fltPrice / 100.0);

        return $fltPrice;
    }

    /**
     * @param string $strArgCountryISO
     *
     * @return array
     */
    public function getTaxInfos($strArgCountryISO)
    {
        TNTOfficiel_Logstack::log();

        $strCountryName = null;
        $strTaxGroup = null;
        $fltTaxRate = 0.0;
        $strTaxName = null;

        $strCountryISO = Tools::strtoupper(trim($strArgCountryISO));
        $intCountryID = (int)Country::getByIso($strCountryISO);
        // If country exist.
        if ($intCountryID > 0) {
            // Use current language for name translation.
            $intLangID = (int)Context::getContext()->language->id;
            $objCountry = new Country($intCountryID, $intLangID);
            $strCountryName = $objCountry->name;

            $ObjAddressTmp = new Address();
            $ObjAddressTmp->id_country = $intCountryID;
            $ObjAddressTmp->id_state = 0;
            $ObjAddressTmp->postcode = 0;

            $intTaxRulesGroupID = (int)Carrier::getIdTaxRulesGroupByIdCarrier($this->id_carrier);
            if ($intTaxRulesGroupID > 0) {
                $objTaxRulesGroup = new TaxRulesGroup($intTaxRulesGroupID);
                $strTaxGroup = $objTaxRulesGroup->name;

                $objTaxManager = TaxManagerFactory::getManager($ObjAddressTmp, $intTaxRulesGroupID);
                $objTaxCalculator = $objTaxManager->getTaxCalculator();
                $fltTaxRate = (float)$objTaxCalculator->getTotalRate();
                $strTaxName = $objTaxCalculator->getTaxesName();
                if (!$strTaxName) {
                    $strTaxName = 'N/D';
                }
            }
        }

        //$arrTaxRulesGroupCountryRate = $objTaxRulesGroup->getAssociatedTaxRatesByIdCountry($intCountryID);
        //$fltTaxRate = 0.0;
        //if (array_key_exists($intTaxRulesGroupID, $arrTaxRulesGroupCountryRate)) {
        //    $fltTaxRate = (float)$arrTaxRulesGroupCountryRate[$intTaxRulesGroupID];
        //}

        return array(
            'country' => $strCountryName,
            'group' => $strTaxGroup,
            'name' => $strTaxName,
            'rate' => $fltTaxRate,
        );
    }
}
