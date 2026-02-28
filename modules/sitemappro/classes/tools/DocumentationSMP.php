<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class DocumentationSMP
{
    public static function assignDocumentation()
    {
        ToolsModuleSMP::registerSmartyFunctions();
        $context = Context::getContext();

        ToolsModuleSMP::addCSS('documentation.css');
        ToolsModuleSMP::addCSS('admin-theme.css');
        $documentation_folder = _PS_MODULE_DIR_.ToolsModuleSMP::getModNameForPath(__FILE__)
            .'/views/templates/admin/documentation';
        $documentation_pages = ToolsModuleSMP::globRecursive($documentation_folder.'/**.tpl');
        natsort($documentation_pages);

        $tree = array();
        if (is_array($documentation_pages) && count($documentation_pages)) {
            foreach ($documentation_pages as &$documentation_page) {
                $name = str_replace(array($documentation_folder.'/', '.tpl'), '', $documentation_page);
                $path = explode('/', $name);

                $tmp_tree = &$tree;
                foreach ($path as $key => $item) {
                    $part = $item;
                    if ($key == (count($path) - 1)) {
                        $tmp_tree[$part] = $name;
                    } else {
                        if (!isset($tmp_tree[$part])) {
                            $tmp_tree[$part] = array();
                        }
                    }
                    $tmp_tree = &$tmp_tree[$part];
                }
            }
        }

        $context->smarty->assign('tree', self::buildTree($tree));
        $context->smarty->assign('documentation_pages', $documentation_pages);
        $context->smarty->assign('documentation_folder', $documentation_folder);
    }

    public static function buildTree($tree)
    {
        $tree_html = '';
        if (is_array($tree) && count($tree)) {
            foreach ($tree as $name => $tree_item) {
                preg_match('/^(\d+)\._(.*)$/', $name, $matches);
                $format_name = $matches[1].'. '.TransModSMP::getInstance()->ld($matches[2]);

                $tree_html .= '<li>';
                $tree_html .= '<a '.(!is_array($tree_item) ? 'data-tab="'.$tree_item.'" href="#"' : '')
                    .'>'.$format_name.'</a>';
                if (is_array($tree_item) && count($tree_item)) {
                    $tree_html .= '<ul>';
                    $tree_html .= self::buildTree($tree_item);
                    $tree_html .= '</ul>';
                }
                $tree_html .= '</li>';
            }
        }
        return $tree_html;
    }
}
