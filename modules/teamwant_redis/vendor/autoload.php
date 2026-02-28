<?php

if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    require_once __DIR__ . '/8.0/vendor/composer/autoload_real.php';
    return ComposerAutoloaderInitac2819d223347bfc19c945fe66749764::getLoader();
} else {
    require_once __DIR__ . '/7.0/vendor/composer/autoload_real.php';
    return ComposerAutoloaderInitc0599dcdd4b3c74a8640ecd010c20e49::getLoader();
}
