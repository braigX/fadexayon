<?php

return [
    "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."minimumcartfee_config` (
        `id_minimumcartfee_config` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `fee_name` VARCHAR(255) NOT NULL,
        `min_amount` DECIMAL(20,6) NOT NULL,
        `id_shop` INT UNSIGNED NOT NULL
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."minimumcartfee_product` (
        `id_minimumcartfee_config` INT UNSIGNED NOT NULL,
        `id_product` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`id_minimumcartfee_config`, `id_product`),
        FOREIGN KEY (`id_minimumcartfee_config`) REFERENCES `"._DB_PREFIX_."minimumcartfee_config` (`id_minimumcartfee_config`) ON DELETE CASCADE
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."minimumcartfee_category` (
        `id_minimumcartfee_config` INT UNSIGNED NOT NULL,
        `id_category` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`id_minimumcartfee_config`, `id_category`),
        FOREIGN KEY (`id_minimumcartfee_config`) REFERENCES `"._DB_PREFIX_."minimumcartfee_config` (`id_minimumcartfee_config`) ON DELETE CASCADE
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8mb4;",

    "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."minimumcartfee_order` (
        `id_order` INT UNSIGNED NOT NULL PRIMARY KEY,
        `fee_label` VARCHAR(255) NOT NULL,
        `fee_amount` DECIMAL(20,6) NOT NULL
    ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8mb4;",
];
