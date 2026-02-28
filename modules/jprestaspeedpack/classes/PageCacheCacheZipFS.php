<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('PageCacheCacheZipFS')) {
    class PageCacheCacheZipFS extends PageCacheCacheSimpleFS
    {
        public function __construct($dir, $log = false)
        {
            parent::__construct($dir, $log);
            $this->extension = '.zip';
        }

        public static function isCompatible()
        {
            return class_exists('ZipArchive');
        }

        protected function storeContent($filepath, $content)
        {
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CREATE) !== true) {
                return false;
            }
            $zip->addFromString('content.html', $content);

            return $zip->close();
        }

        protected function readContent($filepath)
        {
            $zip = new ZipArchive();
            if ($zip->open($filepath, ZipArchive::CHECKCONS) !== true) {
                return false;
            }
            $content = $zip->getFromIndex(0);
            $zip->close();

            return $content;
        }
    }
}
