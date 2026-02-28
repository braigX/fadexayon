<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait EtsSeoRequestControllerTrait
 */
trait EtsSeoRequestControllerTrait
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private static $_sfContainer = null;
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private static $_sfRequest = null;
    public static function getSfContainer()
    {
        if (self::$_sfRequest == null) {
            if (class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer')) {
                self::$_sfContainer = call_user_func(['\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance']);
            }
        }

        return self::$_sfContainer;
    }

    public static function getRequestContainer()
    {
        if (self::$_sfRequest !== null && self::$_sfRequest instanceof \Symfony\Component\HttpFoundation\Request) {
            return self::$_sfRequest;
        }

        $sfContainer = self::getSfContainer();
        if ($sfContainer) {
            self::$_sfRequest = $sfContainer->get('request_stack')->getCurrentRequest();
            return self::$_sfRequest;
        }

        return null;
    }
    public function getCurrentSymfonyController()
    {
        $request = self::getRequestContainer();
        /** @var \Symfony\Component\HttpKernel\Controller\ControllerResolver $controllerResolver */
        $controllerResolver = self::getSfContainer()->get('controller_resolver');

        return $controllerResolver->getController($request);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route The name of the route
     * @param array $parameters An array of parameters
     * @param int $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface     *
     */
    private function generateSymfonyUrl($route, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return self::getSfContainer()->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @return bool
     */
    private function isMetaController()
    {
        if ($request = self::getRequestContainer()) {
            $route = $request->get('_route');
            if ($route && (0 === strpos($route, 'admin_metas_'))) {
                return true;
            }
        }

        return 'AdminMeta' === Tools::getValue('controller');
    }

    /**
     * @return bool
     */
    private function isCreatePage()
    {
        if ($request = self::getRequestContainer()) {
            $route = $request->get('_route');
            if (!class_exists(EtsSeoStrHelper::class)) {
                require_once __DIR__ . '/../utils/EtsSeoStrHelper.php';
            }
            if (EtsSeoStrHelper::endsWith($route, '_create')) {
                return true;
            }
        }
        if (isset($_SERVER['REQUEST_URI']) && $request = $_SERVER['REQUEST_URI']) {
            if ((false !== strpos($request, '/new'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * getIdCurrentPage.
     *
     * @return int
     */
    private function getIdCurrentPage()
    {
        static $currentId;
        if ($currentId) {
            return $currentId;
        }
        if ($request = self::getRequestContainer()) {
            $routes = [
                'admin_product_form' => 'id',
                'admin_products_edit' => 'productId',
                'admin_categories_edit' => 'categoryId',
                'admin_manufacturers_edit' => 'manufacturerId',
                'admin_suppliers_edit' => 'supplierId',
                'admin_cms_pages_edit' => 'cmsPageId',
                'admin_cms_pages_category_edit' => 'cmsCategoryId',
                'admin_metas_edit' => 'metaId',
            ];
            if (array_key_exists($key = $request->get('_route'), $routes)) {
                return $currentId = (int) $request->get($routes[$key]);
            }
        }

        $controller = ($controller = Tools::getValue('controller')) && Validate::isControllerName($controller) ? $controller : '';
        if ('AdminCmsContent' == $controller) {
            $id_cms_category = (int) Tools::getValue('id_cms_category');
            $id_cms = (int) Tools::getValue('id_cms');

            return $currentId = (Tools::getIsset('updatecms_category') ? $id_cms_category : $id_cms);
        }
        if ('AdminMeta' == $controller) {
            return $currentId = (int) Tools::getValue('id_meta');
        }
        if ('AdminCategories' == $controller) {
            return $currentId = (int) Tools::getValue('id_category');
        }
        if ('AdminManufacturers' == $controller) {
            return $currentId = (int) Tools::getValue('id_manufacturer');
        }
        if ('AdminSuppliers' == $controller) {
            return $currentId = (int) Tools::getValue('id_supplier');
        }
        if ('AdminProducts' == $controller) {
            return $currentId = (int) Tools::getValue('id_product');
        }

        return 0;
    }

    /**
     * @return bool
     */
    private function isCmsCategoryPage()
    {
        $request = self::getRequestContainer();
        if ($request) {
            if ('admin_cms_pages_category_edit' == $request->get('_route') || 'admin_cms_pages_category_create' == $request->get('_route')) {
                return true;
            }
        } else {
            if (Tools::getIsset('addcms_category') || Tools::getIsset('updatecms_category')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function isProductV1ListPage()
    {
        if (self::getRequestContainer() && ('admin_product_catalog' === self::getRequestContainer()->get('_route'))) {
            return true;
        }

        return 'AdminProducts' === Tools::getValue('controller') && !$this->getIdCurrentPage();
    }

    /**
     * @return bool
     */
    private function isProductListPage()
    {
        return $this->isProductV2ListPage() || $this->isProductV1ListPage();
    }

    /**
     * @param bool $onlyEditMode
     *
     * @return bool
     */
    private function isProductV2Page($onlyEditMode = false)
    {
        if ($onlyEditMode) {
            return $this->isProductV2EditPage();
        }

        return self::getRequestContainer() && in_array(self::getRequestContainer()->get('_route'), ['admin_products_edit', 'admin_products_index'], true);
    }

    /**
     * @return bool
     */
    private function isProductV2EditPage()
    {
        return self::getRequestContainer() && ('admin_products_edit' === self::getRequestContainer()->get('_route'));
    }

    /**
     * @return bool
     */
    private function isProductV2ListPage()
    {
        return self::getRequestContainer() && ('admin_products_index' === self::getRequestContainer()->get('_route'));
    }

    /**
     * @return bool
     */
    private function isAjaxRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH']) {
            return true;
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('#\bapplication/json\b#', $_SERVER['HTTP_ACCEPT'])) {
            return true;
        }

        return Tools::isSubmit('ajax');
    }
}
