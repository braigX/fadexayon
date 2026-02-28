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

if (!defined('_PS_VERSION_')) { exit; }

class EtsRVLink
{
    public static function getAdminLink($controller, $with_token = true, $sfRouteParams = array(), $params = array(), $context = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return $context->link->getAdminLink($controller, $with_token, $sfRouteParams, $params);
        } else {
            if ($with_token) {
                $params['token'] = Tools::getAdminTokenLite($controller);
            }
            return Dispatcher::getInstance()->createUrl($controller, $context->language->id, $params);
        }
    }

    public static function getPagination($module_name, $controller, $total, $p, $n, $params = [], $number_links = 7, $context = null)
    {
        if ($p <= 0)
            $p = 1;
        if ($n <= 0)
            $n = 10;

        $last = ceil($total / $n);
        $start = (($p - $number_links) > 0) ? $p - $number_links : 1;
        $end = (($p + $number_links) < $last) ? $p + $number_links : $last;
        $paginates = [];
        $paginates[] = [
            'class' => $p <= 1 ? 'disabled' : '',
            'page' => 1,
            'icon' => 'angle-double-left'
        ];
        $paginates[] = [
            'class' => ($p == 1) ? 'disabled' : '',
            'page' => ($p - 1),
            'icon' => 'angle-left'
        ];
        if ($start > 1) {
            $paginates[] = [
                'class' => '',
                'page' => 1,
                'title' => '1'
            ];
            if ($start > 2) {
                $paginates[] = [
                    'class' => '',
                    'page' => $start - 1,
                    'title' => '...'
                ];
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            $paginates[] = [
                'class' => ($p == $i) ? 'active' : '',
                'page' => $i,
                'title' => $i
            ];
        }
        if ($end < $last) {
            if ($end < ($last - 1)) {
                $paginates[] = [
                    'class' => '',
                    'page' => $end + 1,
                    'title' => '...'
                ];
            }
            $paginates[] = [
                'class' => '',
                'page' => $last,
                'title' => $last
            ];
        }
        $paginates[] = [
            'class' => ($p == $last) ? 'disabled' : '',
            'page' => ($p + 1),
            'icon' => 'angle-right'
        ];
        $paginates[] = [
            'class' => ($p == $last) ? 'disabled' : '',
            'page' => $last,
            'icon' => 'angle-double-right'
        ];
        if ($paginates) {
            foreach ($paginates as &$paginate) {
                if ($paginate['page'] > 0 && (int)$paginate['page'] <= (int)$last && $paginate['page'] !== $p && $paginate['class'] !== 'disabled')
                    $paginate['link'] = $context->link->getModuleLink($module_name, $controller, $params + ['per_page' => $n, 'page' => (int)$paginate['page']]);
            }
        }

        return $paginates;
    }

    public static function getMediaLink($path, $context = null)
    {
        if ($context == null) {
            $context = Context::getContext();
        }
        return defined('_PS_ADMIN_DIR_') && isset($context->employee) && $context->employee->id > 0 && $context->employee->isLoggedBack() ? $path : $context->link->getMediaLink($path);
    }
}