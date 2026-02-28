<?php

return Db::getInstance()->execute(
    "DROP TABLE IF EXISTS
        " . _DB_PREFIX_ . "minimumcartfee_category,
        " . _DB_PREFIX_ . "minimumcartfee_product,
        " . _DB_PREFIX_ . "minimumcartfee_config"
);