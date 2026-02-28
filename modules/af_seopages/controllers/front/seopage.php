<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Af_SeopagesSeopageModuleFrontController extends ModuleFrontControllerCore
{
    public $controller_name = 'seopage'; // required for applying native hook exceptions

    public function init()
    {
        $this->module->defineSettings();
        $this->af = $this->module->af();
        if (!empty($this->module->x['matched_seopage'])) {
            $this->seopage_data = $this->module->x['matched_seopage'];
        } else {
            $this->seopage_data = [];
            $params = ['multilang' => 1, 'f' => ['active' => 1]];
            if ($link_rewrite = $this->module->url('getPossibleLinkRerwite')) {
                $params['f']['link_rewrite'] = $link_rewrite;
                $this->seopage_data = $this->module->pageData('get', $params);
            }
            if (!$this->seopage_data) {
                $params['f'] = ['sp.id_seopage' => $this->module->default_id];
                if (!$this->seopage_data = $this->module->pageData('get', $params)) {
                    Tools::redirect('404');
                } elseif ($link_rewrite) {
                    $this->af->redirect301($this->seopage_data['canonical']);
                }
            }
        }
        $this->defineLayoutColumns();
        parent::init();
    }

    public function defineLayoutColumns()
    {
        if ($this->module->is_modern) {
            if ($this->module->settings['lc'] && $this->module->settings['rc']) {
                $layout = 'layout-both-columns';
            } elseif ($this->module->settings['lc']) {
                $layout = 'layout-left-column';
            } elseif ($this->module->settings['rc']) {
                $layout = 'layout-right-column';
            } else {
                $layout = 'layout-full-width';
            }
            $this->context->shop->theme->setPageLayouts([$this->module->fc_identifier => $layout]);
        } else {
            $this->display_column_left = $this->module->settings['lc'];
            $this->display_column_right = $this->module->settings['rc'];
        }
    }

    public function initContent()
    {
        $this->preInitContent();
        if (!$this->module->is_modern) {
            return $this->retroInitContent();
        }
        $this->php_self = null; // required to add correct routes for other languages in Link::getLanguageLink()
        $current_url = $this->getCurrentURL();
        $current_sorting_label = '';
        $this->context->smarty->assign([
            'listing' => [
                'products' => $this->presentProducts($this->af->products_data['products']),
                'pagination' => $this->getPaginationVars($current_url),
                'sort_orders' => $this->getSortingVars($current_url, $current_sorting_label),
                'sort_selected' => $current_sorting_label,
                'current_url' => $current_url,
                'rendered_facets' => '',
                'rendered_active_filters' => '',
            ],
            'page' => [
                'meta' => [
                    'title' => $this->seopage_data['meta_title'],
                    'description' => $this->seopage_data['meta_description'],
                    'keywords' => $this->seopage_data['meta_keywords'],
                ] + $this->context->smarty->tpl_vars['page']->value['meta'],
                'canonical' => $this->seopage_data['canonical'],
            ] + $this->module->getSmartyValue('page'),
            'seo_data' => $this->seopage_data,
            'breadcrumb' => $this->module->getBreadCrumbVariables($this->seopage_data),
        ]);
        if (!empty($this->seopage_data['alternate_urls'])) {
            $this->context->smarty->tpl_vars['urls']->value['alternative_langs'] =
            $this->seopage_data['alternate_urls'];
        }
        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/seopage.tpl');
    }

    public function preInitContent()
    {
        if ($this->module->is_modern) {
            $this->php_self = $this->module->fc_identifier; // correct items per row in iqitthemeeditor::getOptions()
        } else {
            $this->context->smarty->assign(['urls' => ['alternative_langs' => []]]);
        }
        $this->paginateTitle();
        parent::initContent();
        if (!$this->seopage_data || !$this->af->params_defined) {
            Tools::redirect('404');
        }
    }

    public function presentProducts($products)
    {
        $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
        $factory_presenter = $factory->getPresenter();
        $presentation_settings = $factory->getPresentationSettings();
        foreach ($products as &$p) {
            $p = Product::getProductProperties($this->context->language->id, $p);
            $p = $factory_presenter->present($presentation_settings, $p, $this->context->language);
        }

        return $products;
    }

    public function getSortingVars($current_url, &$current_sorting_label)
    {
        $current_sorting_option = 'product.' . $this->context->filtered_result['sorting'];
        $sorting_options = $this->af->getSortingOptions($current_sorting_option, '', $current_url);
        if (isset($sorting_options[$current_sorting_option]['label'])) {
            $current_sorting_label = $sorting_options[$current_sorting_option]['label'];
        }

        return $sorting_options;
    }

    public function getPaginationVars($current_url)
    {
        $pagination_vars = $this->af->getPaginationVariables(
            $this->af->products_data['page'],
            $this->af->products_data['filtered_ids_count'],
            $this->af->products_data['products_per_page'],
            $current_url
        );
        $this->setRelPagesIfRequired($pagination_vars['pages']);

        return $pagination_vars;
    }

    public function paginateTitle()
    {
        $page_num = Tools::getValue($this->af->param_names['p'], 1);
        if ($page_num > 1 && !empty($this->seopage_data['meta_title'])) {
            $this->seopage_data['meta_title'] .= ' (' . $page_num . ')';
        }
    }

    public function setRelPagesIfRequired($pages_data)
    {
        if (!empty($this->module->settings['page_canonical'])) {
            $current_page_number = $this->af->products_data['page'];
            $prev = current($pages_data);
            $next = end($pages_data);
            $ret = '';
            if ($prev['page'] != $current_page_number) {
                $ret .= '<link rel="prev" href="' . $prev['url'] . '">';
            }
            if ($next['page'] != $current_page_number) {
                $ret .= '<link rel="next" href="' . $next['url'] . '">';
            }
            if ($ret) {
                $hook_header = $ret . $this->module->getSmartyValue('HOOK_HEADER');
                $this->context->smarty->assign('HOOK_HEADER', $hook_header);
            }
        }
    }

    /* PS 1.6 */
    public function retroInitContent()
    {
        $this->module->retro('assignBreadcrumbTplVars', $this->seopage_data);
        $this->context->smarty->assign([
            'products' => $this->af->products_data['products'],
            'meta_title' => $this->seopage_data['meta_title'],
            'meta_description' => $this->seopage_data['meta_description'],
            'meta_keywords' => $this->seopage_data['meta_keywords'],
            'seo_data' => $this->seopage_data,
        ]);
        $this->setTemplate('seopage-16.tpl');
    }
}
