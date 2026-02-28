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
 * Class TNTOfficielLabel
 */
class TNTOfficielLabel extends ObjectModel
{
    // id_tntofficiel_label
    public $id;

    public $id_order;
    /** @var string name */
    public $label_name;
    /** @var string base64 (16 Mb Max) */
    public $label_pdf_content;
    /** @var string label_date_created */
    public $label_type;
    public $date_add;

    public static $definition = array(
        'table' => 'tntofficiel_label',
        'primary' => 'id_tntofficiel_label',
        'fields' => array(
            'id_order' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'label_name' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 255,
            ),
            'label_pdf_content' => array(
                'type' => ObjectModel::TYPE_NOTHING,
            ),
            'label_type' => array(
                'type' => ObjectModel::TYPE_STRING,
                'size' => 32,
            ),
            'date_add' => array(
                'type' => ObjectModel::TYPE_DATE,
            ),
        ),
    );

    // cache and prevent race condition.
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

        $strTableName = $strTablePrefix . TNTOfficielLabel::$definition['table'];

        // Create table.
        $strSQLCreateLabel = <<<SQL
CREATE TABLE IF NOT EXISTS `${strTableName}` (
    `id_tntofficiel_label`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_order`                      INT(10) UNSIGNED NOT NULL,
    `label_name`                    VARCHAR(255) NOT NULL,
    `label_pdf_content`             MEDIUMBLOB,
    `label_type`                    VARCHAR(32) NOT NULL,
-- State.
    `date_add`                      DATETIME NOT NULL DEFAULT '0000-00-00',
-- Key.
    PRIMARY KEY (`id_tntofficiel_label`),
    UNIQUE INDEX `id_order` (`id_order`)
) ENGINE = ${strTableEngine} DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';
SQL;

        $boolDBResult = TNTOfficiel_Tools::getDbExecute($strSQLCreateLabel);
        if (is_string($boolDBResult)) {
            TNTOfficiel_Logger::logInstall($strLogMessage . ' : ' . $boolDBResult, false);

            return false;
        }

        TNTOfficiel_Logger::logInstall($strLogMessage);

        return TNTOfficielLabel::checkTables();
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
        $strTableName = $strTablePrefix . TNTOfficielLabel::$definition['table'];
        $arrColumnsList = array_keys(TNTOfficielLabel::$definition['fields']);

        return (TNTOfficiel_Tools::isTableColumnsExist($strTableName, $arrColumnsList) === true);
    }

    /**
     * Constructor.
     */
    public function __construct($intArgID = null)
    {
        TNTOfficiel_Logstack::log();

        parent::__construct($intArgID);
    }

    /**
     * Load existing object model or optionally create a new one for its ID.
     *
     * @param      $intArgOrderID
     * @param bool $boolArgCreate
     *
     * @return TNTOfficielLabel|null
     */
    public static function loadOrderID($intArgOrderID, $boolArgCreate = true)
    {
        TNTOfficiel_Logstack::log();

        $intOrderID = (int)$intArgOrderID;

        // No new order ID.
        if (!($intOrderID > 0)) {
            return null;
        }

        $strEntityID = '_' . $intOrderID . '-' . (int)null . '-' . (int)null;
        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficielLabel::$arrLoadedEntities)) {
            $objTNTLabelModel = TNTOfficielLabel::$arrLoadedEntities[$strEntityID];
            // Check.
            if (Validate::isLoadedObject($objTNTLabelModel)
                && (int)$objTNTLabelModel->id_order === $intOrderID
            ) {
                return $objTNTLabelModel;
            }
        }

        // Search row for order ID.
        $objDbQuery = new DbQuery();
        $objDbQuery->select('*');
        $objDbQuery->from(TNTOfficielLabel::$definition['table']);
        $objDbQuery->where('id_order = ' . $intOrderID);

        $arrDBResult = TNTOfficiel_Tools::getDbSelect($objDbQuery);
        // If row found and match order ID.
        if (is_array($arrDBResult) && count($arrDBResult) === 1 && $intOrderID === (int)$arrDBResult[0]['id_order']) {
            // Load existing TNT order entry.
            $objTNTLabelModel = new TNTOfficielLabel((int)$arrDBResult[0]['id_tntofficiel_label']);
        } elseif ($boolArgCreate === true) {
            // Create a new TNT order entry.
            $objTNTLabelModelCreate = new TNTOfficielLabel(null);
            $objTNTLabelModelCreate->id_order = $intOrderID;
            $objTNTLabelModelCreate->save();
            // Reload to get default DB values after creation.
            $objTNTLabelModel = TNTOfficielLabel::loadOrderID($intOrderID, false);
        } else {
            // Log.
            $objException = new Exception(sprintf(
                TNTOfficiel::getCodeTranslate('errorUnableLoadNotFoundStr'),
                'TNTOfficielLabel',
                'Order',
                $intOrderID
            ));
            TNTOfficiel_Logger::logException($objException);

            return null;
        }

        // Check.
        if (!Validate::isLoadedObject($objTNTLabelModel)
            || (int)$objTNTLabelModel->id_order !== $intOrderID
        ) {
            return null;
        }

        $objTNTLabelModel->id = (int)$objTNTLabelModel->id;
        $objTNTLabelModel->id_order = (int)$objTNTLabelModel->id_order;

        TNTOfficielLabel::$arrLoadedEntities[$strEntityID] = $objTNTLabelModel;

        return $objTNTLabelModel;
    }

    /**
     * @return mixed
     */
    public function getLabelPDFContent()
    {
        TNTOfficiel_Logstack::log();

        return TNTOfficiel_Tools::decodeBase64($this->label_pdf_content);
    }

    /**
     * Get label name. Used as filename.
     *
     * @return mixed
     */
    public function getLabelName()
    {
        TNTOfficiel_Logstack::log();

        $intLabelSize = (int)TNTOfficielLabel::$definition['fields']['label_name']['size'];
        $strLabelName = TNTOfficiel_Tools::slugName($this->label_name, $intLabelSize);

        return $strLabelName;
    }

    /**
     * Save BT in BDD.
     *
     * @param $strArgName
     * @param $strArgPDFContent
     * @param $strArgPickupLabelType
     *
     * @return mixed
     */
    public function addLabel($strArgName, $strArgPDFContent, $strArgPickupLabelType)
    {
        TNTOfficiel_Logstack::log();

        $objDateTimeNow = new DateTime('now');

        $intLabelSize = (int)TNTOfficielLabel::$definition['fields']['label_name']['size'];
        $this->label_name = TNTOfficiel_Tools::slugName($strArgName, $intLabelSize);
        $this->label_pdf_content = TNTOfficiel_Tools::encodeBase64($strArgPDFContent);
        $this->label_type = $strArgPickupLabelType;
        $this->date_add = $objDateTimeNow->format('Y-m-d H:i:s');

        return $this->save();
    }
}
