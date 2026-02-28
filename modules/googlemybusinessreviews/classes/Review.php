<?php
/**
*
*  @author AN Eshop Group
*  @copyright  AN Eshop Group
*  @license    Private
*  @version  Release: $Revision$
*/

/**
 * Class Review
 * @package Anshop\GoogleBusinessReview
 */
class Review extends ObjectModel
{
    /** @var int */
    public $id;

    /**  @var string */
    public $author;

    /** @var string */
    public $author_url;

    /** @var string */
    public $language;

    /** @var string */
    public $profile_photo;

    /** @var float */
    public $rating;

    /** @var string */
    public $text;

    /** @var string */
    public $time_description;

    /** @var int */
    public $time;

    /** @var string $date_add */
    public $date_add;

    /** @var string $date_upd */
    public $date_upd;

    /** @var string $place_id */
    public $place_id;

    /** @var array */
    public static $definition
        = [
            'table' => 'anshop_reviews',
            'primary' => 'id',
            'multilang' => false,
            'fields' => [
                'author' => ['type' => self::TYPE_STRING],
                'author_url' => ['type' => self::TYPE_STRING],
                'language' => ['type' => self::TYPE_STRING],
                'profile_photo' => ['type' => self::TYPE_STRING],
                'rating' => ['type' => self::TYPE_FLOAT],
                'text' => ['type' => self::TYPE_STRING],
                'time_description' => ['type' => self::TYPE_STRING],
                'time' => ['type' => self::TYPE_INT],
                'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
                'place_id' => ['type' => self::TYPE_STRING],
            ],
        ];

    /**
     * @param bool $auto_date
     * @param bool $null_values
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function add($auto_date = true, $null_values = false)
    {
        return parent::add($auto_date, $null_values);
    }

    /**
     * @param string $locale
     * @param int $limit
     * @param int $offset
     * @return array|bool|false|mysqli_result|PDOStatement|resource|null
     * @throws PrestaShopDatabaseException
     */
    public static function getAll($locale = 'en', $limit = 10, $offset = 0, $orderBy = 'date')
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(self::$definition['table']);
        $sql->where("place_id ='" . Configuration::get(Googlemybusinessreviews::CONFIG_PLACE_ID) . "'");
        $sql->where("language ='" . $locale . "'");

        if ($orderBy == 'date') {
            $sql->orderBy('time DESC');
        }

        if ($orderBy == 'random') {
            $sql->orderBy('RAND()');
        }

        $sql->limit($limit, $offset);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param string $locale
     * @return int
     * @throws PrestaShopDatabaseException
     */
    public static function getNbReviews($locale = 'en')
    {
        $sql = new DbQuery();
        $sql->select('count(*) as nb');
        $sql->from(self::$definition['table']);
        $sql->where("place_id ='" . Configuration::get(Googlemybusinessreviews::CONFIG_PLACE_ID) . "'");
        $sql->where("language ='" . $locale . "'");

        $result = Db::getInstance()->executeS($sql);
        if (!isset($result[0])) {
            return 0;
        }

        return (int)$result[0]['nb'];
    }
}
