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

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class AdminYbcBlogPostController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogPostController extends ModuleAdminController
{
    public $errorMessage;
    public $baseLink;
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogPost');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'))
        {
            $this->checked = true;
            $this->_postPost();
            if(Tools::isSubmit('chatGPT'))
            {
                Ybc_chatgpt::getInstance()->chatGPT();
            }
            if(Tools::isSubmit('ajaxproductsearch'))
                $this->ajaxProductSearch();
            if(Tools::isSubmit('ajaxCustomersearch'))
                $this->ajaxCustomerSearch();
        }
    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('post'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }

    public function _getContent()
    {
        if (Tools::isSubmit('addNew') || Tools::isSubmit('editybc_post'))
            return $this->renderPostForm();
        else
            return $this->renderListPost();
    }
    public function ajaxCustomerSearch()
    {
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR (Tools::strlen($query) < 3 AND !Validate::isUnsignedId($query)) OR !Validate::isCleanHtml($query))
            die();
        $filter ='AND (';
        $filter .= " c.id_customer = ".(int)trim(urldecode($query));
        $filter .= " OR (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($query)."%' OR be.name like'".pSQL($query)."%')";
        $filter .= " OR c.email like '".pSQL($query)."%'";
        $filter .=')';
        $customers= Ybc_blog_post_employee_class::getCustomersFilter($filter);
        if($customers)
        {
            foreach ($customers as $customer)
            {
                echo $customer['id_customer'].'|'.($customer['name'] ? $customer['name'] : $customer['customer'] ).'|'.$customer['email'].'|'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.(int)$customer['id_customer'].'&updatecustomer'."\n";
            }
        }
        die();
    }
    public function ajaxProductSearch()
    {
        $query = Tools::getValue('q', false);
        if (!$query OR $query == '' OR Tools::strlen($query) < 1 OR !Validate::isCleanHtml($query))
            die();
        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if($pos = strpos($query, ' (ref:'))
            $query = Tools::substr($query, 0, $pos);

        $excludeIds = Tools::getValue('excludeIds', false);
        if($excludeIds && !Validate::isCleanHtml($excludeIds))
            $excludeIds = false;
        $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool)Tools::getValue('exclude_packs', false);
        $items = Ybc_blog_defines::getProducts($query,$excludeIds,$excludeVirtuals,$exclude_packs);
        $acc = (bool)Tools::isSubmit('excludeIds');
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $type_image= ImageType::getFormattedName('home');
        else
            $type_image= ImageType::getFormatedName('home');
        if ($items && $acc)
            foreach ($items AS $item)
            {
                $link_product= $this->context->link->getProductLink($item['id_product'],null,null,null,null,null,Product::getDefaultAttribute($item['id_product']));
                echo trim(str_replace('|','-',$item['name'])).(!empty($item['reference']) ? ' (ref: '.str_replace('|','-',$item['reference']).')' : '').'|'.(int)($item['id_product']).'|'.str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)).'|'.$link_product."\n";
            }
        elseif ($items)
        {
            // packs
            $results = array();
            foreach ($items AS $item)
            {
                $product = array(
                    'id' => (int)($item['id_product']),
                    'name' => $item['name'],
                    'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                    'image' => str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $type_image)),
                );
                array_push($results, $product);
            }
            echo json_encode($results);
        }
        else
            json_encode(new stdClass);
        die;
    }
    public function renderListPost()
    {
        $show_reset = false;
        $fields_list = array(
            'id_post' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'thumb_link' => array(
                'title' => $this->l('Image'),
                'type' => 'text',
                'strip_tag' => false,
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'id_category' => array(
                'title' => $this->l('Categories'),
                'type' => 'select',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'id_category',
                    'value' => 'title',
                    'list' => Ybc_blog_category_class::getCategories()
                )
            ),
            'name_author' => (
            array(
                'title' => $this->l('Author'),
                'type' => 'text',
                'filter' => true,
                'strip_tag' => false,
            )
            ),
            'sort_order' => array(
                'title' => $this->l('Sort order'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'position' => array(
                'title' => $this->l('Sort order'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'click_number' => array(
                'title' => $this->l('Views'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'likes' => array(
                'title' => $this->l('Likes'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'enabled' => array(
                'title' => $this->l('Status'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->l('Published')
                        ),
                        1 => array(
                            'enabled' => -1,
                            'title' => $this->l('Pending')
                        ),
                        2 => array(
                            'enabled' => 0,
                            'title' => $this->l('Disabled')
                        ),
                        3 => array(
                            'enabled' => -2,
                            'title' => $this->l('Preview')
                        ),
                        4 => array(
                            'enabled' => 2,
                            'title' => $this->l('Schedule publish date')
                        )
                    )
                )
            ),
            'is_featured' => array(
                'title' => $this->l('Featured'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'is_featured',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'is_featured' => 1,
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'is_featured' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            ),
        );
        if (($idCategory = trim(Tools::getValue('id_category'))) != '' && Validate::isInt($idCategory))
            unset($fields_list['sort_order']);
        else
            unset($fields_list['position']);
        //Filter
        $filter = '';
        if (($idPost = trim(Tools::getValue('id_post'))) != '' && Validate::isCleanHtml($idPost))
            $filter .= " AND p.id_post = " . (int)$idPost;
        if (($sort_order = trim(Tools::getValue('sort_order'))) != '' && Validate::isCleanHtml($sort_order))
            $filter .= " AND p.sort_order = " . (int)$sort_order;
        if (($click_number = trim(Tools::getValue('click_number'))) != '' && Validate::isCleanHtml($click_number))
            $filter .= " AND p.click_number = " . (int)$click_number;
        if (($likes = trim(Tools::getValue('likes'))) != '' && Validate::isCleanHtml($likes))
            $filter .= " AND p.likes = " . (int)$likes;
        if (($title = trim(Tools::getValue('title'))) != '' && Validate::isCleanHtml($title))
            $filter .= " AND pl.title like '%" . pSQL($title) . "%'";
        if (($description = trim(Tools::getValue('description'))) != '' && Validate::isCleanHtml($description))
            $filter .= " AND pl.description like '%" . pSQL($description) . "%'";
        if (($id_category = trim(Tools::getValue('id_category'))) != '' && Validate::isCleanHtml($id_category))
            $filter .= " AND p.id_post IN (SELECT id_post FROM `" . _DB_PREFIX_ . "ybc_blog_post_category` WHERE id_category = " . (int)$id_category . ") ";
        if (($enabled = trim(Tools::getValue('enabled'))) != '' && Validate::isCleanHtml($enabled))
            $filter .= " AND p.enabled = " . (int)$enabled;
        if (($is_featured = trim(Tools::getValue('is_featured'))) != '' && Validate::isCleanHtml($is_featured))
            $filter .= " AND p.is_featured = " . (int)$is_featured;
        if (($name_author = trim(Tools::getValue('name_author'))) != '' && Validate::isCleanHtml($name_author))
            $filter .= " AND (CONCAT(e.firstname,' ', e.lastname) like '%" . pSQL($name_author) . "%' OR CONCAT(c.firstname,' ', c.lastname) like '%" . pSQL($name_author) . "%')";
        //Sort
        $sort = 'p.id_post DESC,';
        $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type', 'desc'));
        if ($sort_post && isset($fields_list[$sort_post])) {
            $sort = $sort_post . " " . ($sort_type == 'asc' ? ' ASC ' : ' DESC ') . " , ";
        }
        if ($filter)
            $show_reset = true;

        //Paggination
        $page = (int)Tools::getValue('page');
        if ($page <= 1)
            $page = 1;
        $totalRecords = (int)Ybc_blog_post_class::countPostsWithFilter($filter, false);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink . '&page=_page_' . $this->module->getUrlExtra($fields_list);
        $paggination->limit = (int)Tools::getValue('paginator_ybc_post_select_limit', 20);
        $paggination->name = 'ybc_post';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if ($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if ($start < 0)
            $start = 0;
        $posts = Ybc_blog_post_class::getPostsWithFilter($filter, $sort, $start, $paggination->limit, false);
        if ($posts) {
            foreach ($posts as &$post) {
                $post['id_category'] = $this->module->getCategoriesStrByIdPost($post['id_post']);
                $url = $this->module->getLink('blog', array('id_post' => $post['id_post']));
                if ($post['enabled'] == -2) {
                    if (Tools::strpos('?', $url) !== false)
                        $url .= '&preview=1';
                    else
                        $url .= '?preview=1';
                }
                $post['view_url'] = $url;
            }
        }
        $paggination->text = $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');

        $listData = array(
            'name' => 'ybc_post',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink . ($paggination->limit != 20 ? '&paginator_ybc_post_select_limit=' . $paggination->limit : ''),
            'identifier' => 'id_post',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Posts'),
            'fields_list' => $fields_list,
            'field_values' => $posts,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'preview_link' => $this->module->getLink('blog'),
            'sort' => $sort_post ?: 'id_post',
            'sort_type' => $sort_type,
        );
        return $this->module->renderList($listData);
    }

    public function renderPostForm()
    {
        $id_post = (int)Tools::getValue('id_post');
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manage posts'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Post title'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                        'tab' => 'basic',
                        'class' => 'title',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta title'),
                        'name' => 'meta_title',
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta description'),
                        'name' => 'meta_description',
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive. Meta description should be less than 300 characters.'),
                    ),
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Meta keywords'),
                        'name' => 'meta_keywords',
                        'lang' => true,
                        'tab' => 'seo',
                        'hint' => array(
                            $this->l('To add "keywords" click in the field, write something, and then press "Enter."'),
                        ),
                        'desc' => $this->l('Enter your focus keywords and minor keywords'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Url alias'),
                        'name' => 'url_alias',
                        'required' => true,
                        'lang' => true,
                        'tab' => 'seo',
                        'desc' => $this->l('Should be as short as possible and contain your focus keyword.') . ($id_post ? $this->module->displayText($this->l('View post'), 'a', 'ybc_link_view', null, $this->module->getLink('blog', array('id_post' => $id_post)), true) : ''),
                    ),
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Tags'),
                        'name' => 'tags',
                        'lang' => true,
                        'tab' => 'option',
                        'hint' => array(
                            $this->l('To add "tags" click in the field, write something, and then press "Enter."'),
                        ),
                        'desc' => $this->l('Tags are separated by a comma. Related posts are the posts in the same tag or in the same post categories.'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Short description'),
                        'name' => 'short_description',
                        'lang' => true,
                        'required' => true,
                        'autoload_rte' => true,
                        'tab' => 'basic',
                        'desc' => $this->l('Short description is displayed in post listing pages'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Post content'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => true,
                        'required' => true,
                        'tab' => 'basic',
                        'desc' => $this->l('Post content is displayed in post details page (single page).'),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Post thumbnail'),
                        'name' => 'thumb',
                        'imageType' => 'thumb',
                        'required' => true,
                        'tab' => 'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s. Post thumbnail image is required. You should adjust your image to the recommended size before uploading it.'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH', null, null, null, 260), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT', null, null, null, 180)),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Blog post main image'),
                        'name' => 'image',
                        'tab' => 'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'), Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH', null, null, null, 1920), Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT', null, null, null, 750))
                    ),
                    array(
                        'type' => 'blog_categories',
                        'label' => $this->l('Post categories'),
                        'html_content' => $this->module->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0), $this->module->getSelectedCategories($id_post)),
                        'categories' => Ybc_blog_category_class::getBlogCategoriesTree(0),
                        'name' => 'categories',
                        'required' => true,
                        'tab' => 'basic',
                        'selected_categories' => $this->module->getSelectedCategories($id_post)
                    ),
                    array(
                        'type' => 'categories2',
                        'label' => $this->l('Related product'),
                        'name' => 'product_categories',
                        'tab' => 'option',
                        'tree' => array(
                            'id' => 'product-categories-tree',
                            'selected_categories' => Ybc_blog_category_class::getSelectedRelatedProductCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
                        ),
                    ),
                    array(
                        'type' => 'products_search',
                        'label' => $this->l('Include products'),
                        'name' => 'products',
                        'selected_products' => $this->getSelectedProducts($id_post),
                        'tab' => 'option',
                    ),
                    array(
                        'type' => 'exclude_products',
                        'label' => $this->l('Exclude products'),
                        'name' => 'exclude_products',
                        'selected_products' => $this->getExcludeProducts($id_post),
                        'tab' => 'option',
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Related product categories'),
                        'name' => 'related_categories',
                        'tab' => 'option',
                        'tree' => array(
                            'id' => 'categories-tree',
                            'selected_categories' => Ybc_blog_category_class::getSelectedRelatedCategories($id_post),
                            'use_search' => true,
                            'use_checkbox' => true,
                        ),
                        'showRequired' => true,
                        'desc' => $this->l('Check on product categories that you want to display this post on their "Related posts" section on the front office'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Views'),
                        'name' => 'click_number',
                        'required' => true,
                        'tab' => 'option',
                        'desc' => $this->l('The number of post view will be increased from this number'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Likes'),
                        'name' => 'likes',
                        'required' => true,
                        'tab' => 'option',
                        'desc' => $this->l('The number of post likes will be increased from this number'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Is featured post'),
                        'name' => 'is_featured',
                        'is_bool' => true,
                        'tab' => 'option',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Enable this if you want to display this post in "Featured posts" section on the front office')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Status'),
                        'name' => 'enabled',
                        'tab' => 'basic',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Published')
                                ),
                                array(
                                    'id_option' => -1,
                                    'name' => $this->l('Pending')
                                ),
                                array(
                                    'id_option' => -2,
                                    'name' => $this->l('Preview'),
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('Disabled')
                                ),
                                array(
                                    'id_option' => 2,
                                    'name' => $this->l('Schedule publish date')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Publish date'),
                        'name' => 'datetime_added',
                        'tab' => 'basic',
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Schedule publish date'),
                        'name' => 'datetime_active',
                        'tab' => 'basic',
                        'required2' => true,
                        'desc' => $this->l('You can select the time to automatically publish this post. Leave blank to save this post as draft'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'name' => 'submitSaveAndPreview',
                        'title' => $this->l('Save and preview'),
                        'class' => $id_post && isset($post) && $post->enabled != -2 ? 'pull-right hide' : 'pull-right',
                        'icon' => 'process-icon-save',
                    )
                ),
            ),
        );
        if (version_compare(_PS_VERSION_, '8.0.4', '>=')) {
            $smarty = $this->context->smarty->_getSmartyObj();
            if (!isset($smarty->registered_plugins['modifier']['implode']))
                $this->context->smarty->registerPlugin('modifier', 'implode', 'implode');
        }
        if (isset($fields_form['form']['input'])) {
            foreach ($fields_form['form']['input'] as $key => &$params) {
                if ($params['type'] == 'categories2') {
                    if (!isset($params['tree']['id'])) {
                        throw new PrestaShopException('Id must be filled for categories tree');
                    }
                    $tree = new HelperTreeCategories($params['tree']['id'], isset($params['tree']['title']) ? $params['tree']['title'] : null);
                    if (isset($params['name'])) {
                        $tree->setInputName($params['name']);
                    }
                    if (isset($params['tree']['selected_categories'])) {
                        $tree->setSelectedCategories($params['tree']['selected_categories']);
                    }

                    if (isset($params['tree']['disabled_categories'])) {
                        $tree->setDisabledCategories($params['tree']['disabled_categories']);
                    }

                    if (isset($params['tree']['root_category'])) {
                        $tree->setRootCategory($params['tree']['root_category']);
                    }

                    if (isset($params['tree']['use_search'])) {
                        $tree->setUseSearch($params['tree']['use_search']);
                    }

                    if (isset($params['tree']['use_checkbox'])) {
                        $tree->setUseCheckBox($params['tree']['use_checkbox']);
                    }
                    if (isset($params['tree']['set_data'])) {
                        $tree->setData($params['tree']['set_data']);
                    }
                    $this->context->smarty->assign('categories_tree2', $tree->render());
                    break;
                }
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this->module;
        $helper->identifier = 'id_post';
        $helper->submit_action = 'savePost';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogPost', false);
        $helper->token = $this->context->employee->id ? Tools::getAdminTokenLite('AdminYbcBlogPost') : false;
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $chatgpt = Configuration::get('YBC_BLOG_ENABLED_GPT');
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getPostField(), 'id_post', 'Ybc_blog_post_class', 'savePost'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'post_key' => 'id_post',
            'tab_post' => true,
            'YBC_BLOG_ENABLED_GPT' => $chatgpt,
            'check_suspend' => Ybc_blog_post_class::checkPostSuspend($id_post),
            'form_author_post' => $this->getFormAuthorPost($id_post),
            'cancel_url' => $this->baseLink,
            'image_baseurl' => _PS_YBC_BLOG_IMG_ . 'post/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_ . 'post/thumb/',
            'addNewUrl' => $this->baseLink . '&addNew=1',
            'preview_link' => $id_post ? $this->module->getLink('blog', array('id_post' => $id_post)) : '',
        );
        if ($id_post && ($post = new Ybc_blog_post_class((int)$id_post)) && Validate::isLoadedObject($post)) {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_post');
            if ($post->image) {
                $helper->tpl_vars['img_del_link'] = $this->baseLink . '&id_post=' . $id_post . '&delpostimage=true&editybc_post=1';
            }
            if ($post->thumb) {
                $helper->tpl_vars['thumb_del_link'] = $this->baseLink . '&id_post=' . $id_post . '&delpostthumb=true&editybc_post=1';
            }
        }
        return $helper->generateForm(array($fields_form));
    }

    public function getSelectedProducts($id_post)
    {
        $products = array();
        $inputAccessories = Tools::getValue('inputAccessories');
        if (Tools::isSubmit('inputAccessories') && trim(trim($inputAccessories), ',') && Validate::isCleanHtml($inputAccessories)) {
            $products = explode('-', trim(trim($inputAccessories), '-'));
        } elseif ($id_post) {
            $post = new Ybc_blog_post_class($id_post);
            $products = explode('-', trim($post->products, '-'));
        }
        if ($products) {
            foreach ($products as $key => &$product) {
                $product = (int)$product;
            }
            return Ybc_blog_post_class::getProductsByIDs($products);
        }
        return false;
    }

    public function getExcludeProducts($id_post)
    {
        $products = array();
        $inputAccessories = Tools::getValue('exclude_products');
        if (Tools::isSubmit('exclude_products') && trim(trim($inputAccessories), ',') && Validate::isCleanHtml($inputAccessories)) {
            $products = explode('-', trim(trim($inputAccessories), '-'));
        } elseif ($id_post) {
            $post = new Ybc_blog_post_class($id_post);
            $products = explode('-', trim($post->exclude_products, '-'));
        }
        if ($products) {
            foreach ($products as $key => &$product) {
                $product = (int)$product;
            }
            return Ybc_blog_post_class::getProductsByIDs($products);
        }
        return false;
    }

    public function getFormAuthorPost($id_post)
    {
        if (!$this->module->isCached('form_author_post.tpl', $this->module->_getCacheId($id_post))) {
            if ($id_post && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post)) {
                $this->context->smarty->assign(
                    array(
                        'YBC_BLOG_ALLOW_CUSTOMER_AUTHOR' => Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR') && Ybc_blog_post_employee_class::countCustomersFilter(false),
                        'admin_authors' => Ybc_blog_post_employee_class::getAuthors(),
                        'post' => Ybc_blog_post_class::getPostByID($id_post),
                        'author' => Ybc_blog_post_class::getAuthorByIdPost($id_post)
                    )
                );
            }
            else
                return '';
        }
        return $this->module->display($this->module->getLocalPath(), 'form_author_post.tpl', $this->module->_getCacheId($id_post));
    }

    public function actionChangeStatus()
    {
        $status = (int)Tools::getValue('change_enabled') ? 1 : 0;
        $field = Tools::getValue('field');
        $id_post = (int)Tools::getValue('id_post');
        Hook::exec('actionUpdateBlog', array(
            'id_post' => (int)$id_post,
        ));
        if (($field == 'enabled' || $field == 'is_featured') && $id_post) {
            $post_class = new Ybc_blog_post_class($id_post);
            Hook::exec('actionUpdateBlog', array(
                'id_post' => (int)$id_post,
            ));
            Ybc_blog_defines::changeStatus('post', $field, $id_post, $status);
            $customer = new Customer($post_class->added_by);
            if (($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_blog_customer', $customer->id_lang)) && $field == 'enabled' && $status == 1 && $post_class->is_customer) {
                $template_customer_vars = array(
                    '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
                    '{post_title}' => $post_class->title[$this->context->language->id],
                    '{post_link}' => $this->module->getLink('blog', array('id_post' => $post_class->id)),
                    '{color_main}' => Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                    '{color_hover}' => Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                );
                Mail::Send(
                    $customer->id_lang,
                    'approved_blog_customer',
                    $subject,
                    $template_customer_vars,
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    _PS_MODULE_DIR_.'ybc_blog'.'/mails/'
                );
            }
            if ($field == 'enabled') {
                if ($status == 1)
                    $title = $this->l('Click to mark as draft');
                else
                    $title = $this->l('Click to mark as published');
            } else {
                if ($status == 1)
                    $title = $this->l('Click to unmark featured post');
                else
                    $title = $this->l('Click to mark as featured');
            }
            if (Tools::isSubmit('ajax')) {
                die(json_encode(array(
                    'listId' => $id_post,
                    'enabled' => $status,
                    'field' => $field,
                    'message' => $field == 'enabled' ? $this->module->displaySuccessMessage($this->l('The status has been successfully updated')) : $this->module->displaySuccessMessage($this->l('The featured post has been successfully updated')),
                    'messageType' => 'success',
                    'title' => $title,
                    'href' => $this->baseLink . '&change_enabled=' . ($status ? '0' : '1') . '&field=' . $field . '&id_post=' . $id_post,
                )));
            }
            Tools::redirectAdmin($this->baseLink . '&conf=4');
        }
    }

    private function actionUpdatePostOrdering($posts)
    {
        $page = (int)Tools::getValue('page',1);
        $id_category = (int)Tools::getValue('id_category');
        if(Ybc_blog_post_class::updatePostOrdering($posts,$page,$id_category))
        {
            die(
                json_encode(
                    array(
                        'page'=>$page,
                    )
                )
            );
        }
    }
    public function actionDeleteImagePost()
    {
        if(($id_post =(int)Tools::getValue('id_post')) && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post))
        {
            $post->datetime_modified = date('Y-m-d H:i:s');
            $post->modified_by = (int)$this->context->employee->id;
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$id_post,
            ));
            if(Tools::isSubmit('delpostthumb'))
            {
                $id_lang = (int)Tools::getValue('id_lang');
                if(isset($post->thumb[$id_lang]) && $post->thumb[$id_lang])
                {
                    $oldThumb = $post->thumb[$id_lang];
                    $post->thumb[$id_lang] = $post->thumb[(int)Configuration::get('PS_LANG_DEFAULT')];
                    $post->update();
                    if(!in_array($oldThumb,$post->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb);
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'success',
                                'message' => $this->module->displayConfirmation($this->l('Thumbnail image deleted')),
                            )
                        ));
                    }
                    Tools::redirectAdmin($this->baseLink.'editybc_post&id_post='.$id_post.'&control=post');
                }
                else
                {
                    $errors[] = $this->l('Thumbnail image does not exist');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'error',
                                'message' => $this->module->displayError($errors),
                            )
                        ));
                    }

                }

            }
            elseif(Tools::isSubmit('delpostimage'))
            {
                $id_lang = (int)Tools::getValue('id_lang');
                if(isset($post->image[$id_lang]) && $post->image[$id_lang])
                {
                    $oldImage = $post->image[$id_lang];
                    $post->image[$id_lang] = '';
                    $post->update();
                    if(!in_array($oldImage,$post->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage);
                    Hook::exec('actionUpdateBlog', array(
                        'id_post' =>(int)$id_post,
                    ));
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'success',
                                'message' => $this->module->displayConfirmation($this->l('Image deleted')),
                            )
                        ));
                    }
                    Tools::redirectAdmin($this->baseLink.'editybc_post&id_post='.$id_post);
                }
                else
                {
                    $errors[] = $this->l('Image does not exist');
                    if(Tools::isSubmit('ajax'))
                    {
                        die(json_encode(
                            array(
                                'messageType' => 'error',
                                'message' => $this->module->displayError($errors),
                            )
                        ));
                    }
                }
            }
        }

    }
    private function _postPost()
    {
        $errors = array();
        /**
         * Change status
         */
        if(Tools::isSubmit('change_enabled'))
        {
            $this->actionChangeStatus();
        }
        if(($action = Tools::getValue('action')) && $action=='updatePostOrdering' && ($posts=Tools::getValue('posts')) && Ybc_blog::validateArray($posts,'isInt'))
        {
            $this->actionUpdatePostOrdering($posts);
        }

        /**
         * Delete image
         */
        if(($id_post =(int)Tools::getValue('id_post')) && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post) && (Tools::isSubmit('delpostimage') || Tools::isSubmit('delpostthumb')))
        {
            $this->actionDeleteImagePost();
        }
        /**
         * Delete post
         */
        if(Tools::isSubmit('del'))
        {
            $id_post = (int)Tools::getValue('id_post');
            Hook::exec('actionUpdateBlog', array(
                'id_post' =>(int)$id_post,
            ));
            if(!Validate::isLoadedObject(new Ybc_blog_post_class($id_post)))
                $errors[] = $this->l('Post does not exist');
            elseif(Ybc_blog_post_class::_deletePost($id_post))
            {

                Tools::redirectAdmin($this->baseLink.'&conf=1');
            }
            else
                $errors[] = $this->l('Could not delete the post. Please try again');
        }
        /**
         * Save post
         */
        if(Tools::isSubmit('savePost'))
        {
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $datetime_added = Tools::getValue('datetime_added');
            if($id_post && ($post = new Ybc_blog_post_class($id_post)) && Validate::isLoadedObject($post))
            {
                if($datetime_added)
                    $post->datetime_added = $datetime_added;
                $post->datetime_modified = $datetime_added ?: date('Y-m-d H:i:s');
                $post->modified_by = (int)$this->context->employee->id;
                $post->is_customer= (int)Tools::getValue('is_customer');
                if($post->is_customer)
                {
                    $customer_author = (int)Tools::getValue('customer_author');
                    if(!$customer_author)
                        $errors[]=  $this->l('Community - Authors is required');
                    else
                        $post->added_by = (int)$customer_author;
                }
                else
                {
                    $admin_author = (int)Tools::getValue('admin_author');
                    if(!$admin_author)
                        $errors[]=  $this->l('Administrator - Author is required');
                    else
                        $post->added_by = (int)$admin_author;
                }

            }
            else
            {
                $post = new Ybc_blog_post_class();
                $post->datetime_added = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
                $post->datetime_modified = $datetime_added && Validate::isDate($datetime_added) ? $datetime_added : date('Y-m-d H:i:s');
                $post->modified_by = (int)$this->context->employee->id;
                $post->added_by = (int)$this->context->employee->id;
                $post->is_customer=0;
                $post->sort_order =1 + (int)Ybc_blog_post_class::getMaxOrder();
            }
            $inputAccessories = trim(trim(Tools::getValue('inputAccessories','')),'-');
            if($inputAccessories && Validate::isCleanHtml($inputAccessories))
                $post->products = $inputAccessories;
            else
                $post->products = '';
            $exclude_products = trim(trim(Tools::getValue('exclude_products','')),'-');
            if($exclude_products && Validate::isCleanHtml($exclude_products))
                $post->exclude_products = $exclude_products;
            else
                $post->exclude_products = '';
            $enabled = $post->enabled;
            if($post->id || !Tools::isSubmit('submitSaveAndPreview'))
                $post->enabled = (int)Tools::getValue('enabled');
            else
                $post->enabled = -2;
            if($enabled!=$post->enabled && $post->enabled==1)
                $updatestatus=true;
            else
                $updatestatus=false;
            $post->is_featured = (int)Tools::getValue('is_featured') ? 1 : 0;
            if($post->enabled==2)
            {
                $datetime_active = Tools::getValue('datetime_active');
                if($datetime_active=='')
                    $errors[]=$this->l('Publish date is required');
                elseif($datetime_active=='0000-00-00' || !Validate::isDate($datetime_active))
                    $errors[] = $this->l('Publish date is not valid');
                else
                    $post->datetime_active = $datetime_active;
            }
            elseif(!$post->id)
                $post->datetime_active = date('Y-m-d');
            $languages = Language::getLanguages(false);
            $post->click_number = (int)Tools::getValue('click_number');
            $post->likes = (int)Tools::getValue('likes');
            $tags = array();
            $categories = Tools::getValue('blog_categories',array());
            $title_default = trim(Tools::getValue('title_'.$id_lang_default));
            if($title_default=='')
                $errors[] = $this->l('You need to set blog post title');
            elseif(!Validate::isCatalogName($title_default))
                $errors[] = $this->l('Blog post title is not valid');
            $short_description_default = trim(Tools::getValue('short_description_'.$id_lang_default));
            if($short_description_default=='')
                $errors[] = $this->l('You need to set blog post short description');
            elseif(!Validate::isCleanHtml($short_description_default,true))
                $errors[] = $this->l('Blog post short description is not valid');
            $description_default = trim(Tools::getValue('description_'.$id_lang_default));
            if($description_default=='')
                $errors[] = $this->l('You need to set blog post content');
            elseif(!Validate::isCleanHtml($description_default,true))
                $errors[] = $this->l('Blog post content is not valid');
            $url_alias_default = Tools::getValue('url_alias_'.$id_lang_default);
            if($url_alias_default=='')
                $errors[] = $this->l('Url alias is required');
            elseif(!Validate::isLinkRewrite($url_alias_default))
                $errors[] = $this->l('Url alias is not valid');
            if(!$categories || !is_array($categories))
                $errors[] = $this->l('You need to choose at least 1 category');
            elseif(!Ybc_blog::validateArray($categories))
                $errors[] = $this->l('Categories is not valid');
            $main_category = (int)Tools::getValue('main_category');
            if(!$main_category)
                $errors[] = $this->l('Main category is required');
            elseif(!in_array($main_category,$categories))
                $errors[] = $this->l('Main category is not valid');
            else
                $post->id_category_default = (int)$main_category;
            $click_number = Tools::getValue('click_number');
            if($click_number=='')
                $errors[] = $this->l('Views are required');
            elseif(!Validate::isUnsignedInt($click_number))
                $errors[] = $this->l('Views are not valid');
            $likes = Tools::getValue('likes');
            if($likes=='')
                $errors[] = $this->l('Likes are required');
            elseif(!Validate::isUnsignedInt($likes))
                $errors[] = $this->l('Likes are not valid');
            if(!($post->thumb && isset($post->thumb[$id_lang_default]) && $post->thumb[$id_lang_default]) && !(isset($_FILES['thumb_'.$id_lang_default]['tmp_name']) && isset($_FILES['thumb_'.$id_lang_default]['name']) && $_FILES['thumb_'.$id_lang_default]['name']))
                $errors[]= $this->l('Post thumbnail image is required');
            $meta_title_default = trim(Tools::getValue('meta_title_'.$id_lang_default));
            if($meta_title_default && !Validate::isGenericName($meta_title_default))
                $errors[] = $this->l('Meta title is not valid');
            $meta_description_default = trim(Tools::getValue('meta_description_'.$id_lang_default));
            if($meta_description_default && !Validate::isGenericName($meta_description_default))
                $errors[] = $this->l('Meta description is not valid');
            $meta_keywords_default = trim(Tools::getValue('meta_keywords_'.$id_lang_default));
            if($meta_keywords_default && !Validate::isTagsList($meta_keywords_default))
                $errors[] = $this->l('Meta keyword is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
                {
                    $title = trim(Tools::getValue('title_'.$language['id_lang']));
                    $meta_title = trim(Tools::getValue('meta_title_'.$language['id_lang']));
                    $url_alias = trim(Tools::getValue('url_alias_'.$language['id_lang']));
                    if($title && !Validate::isCatalogName($title))
                        $errors[] = sprintf($this->l('Title in %s is not valid'),$language['name']);
                    else
                        $post->title[$language['id_lang']] = $title ? $title : $title_default;
                    if($meta_title && !Validate::isGenericName($meta_title))
                        $errors[] = sprintf($this->l('Meta title in %s is not valid'),$language['name']);
                    elseif($meta_title && Tools::strlen($meta_title) >700)
                        $errors[] = sprintf($this->l('Meta title in %s is too long. Length should not exceed 700 characters'),$language['name']);
                    else
                        $post->meta_title[$language['id_lang']] = $meta_title ? $meta_title: $meta_title_default;
                    if($url_alias && str_replace(array('0','1','2','3','4','5','6','7','8','9'),'',Tools::substr($url_alias,0,1))=='')
                        $errors[] = sprintf($this->l('Post alias in %s cannot have number on the start position because it will cause error when you enable "Remove post ID" option'),$language['name']);
                    elseif($url_alias && !Ybc_blog::checkIsLinkRewrite($url_alias))
                        $errors[] = sprintf($this->l('Url alias in %s is not valid'),$language['name']);
                    elseif($url_alias && Ybc_blog_post_class::checkUrlAliasExists($url_alias,$post->id))
                        $errors[] = sprintf($this->l('Url alias in %s has already existed'),$language['name']);
                    else
                        $post->url_alias[$language['id_lang']]= $url_alias ? $url_alias : $url_alias_default;
                    $meta_description = trim(Tools::getValue('meta_description_'.$language['id_lang']));
                    if($meta_description && !Validate::isGenericName($meta_description))
                        $errors[] = sprintf($this->l('Meta description in %s is not valid'),$language['name']);
                    else
                        $post->meta_description[$language['id_lang']] = $meta_description ? $meta_description :  $meta_description_default;
                    $meta_keywords = trim(Tools::getValue('meta_keywords_'.$language['id_lang']));
                    if($meta_keywords && !Validate::isTagsList($meta_keywords))
                        $errors[] = sprintf($this->l('Meta keywords in %s are not valid'),$language['name']);
                    else
                        $post->meta_keywords[$language['id_lang']] = $meta_keywords != '' ? $meta_keywords :  $meta_keywords_default;
                    $short_description = trim(Tools::getValue('short_description_'.$language['id_lang']));
                    if($short_description && !Validate::isCleanHtml($short_description, true) )
                        $errors[] = sprintf($this->l('Short description in %s is not valid'),$language['name']);
                    elseif($short_description && !Ybc_blog::checkIframeHTML($short_description))
                        $errors[] = sprintf($this->l('Short description in %s is not valid'),$language['name']).$this->displayErrorIframe();
                    else
                        $post->short_description[$language['id_lang']] = $short_description != '' ? $short_description :  $short_description_default;
                    $description = Tools::getValue('description_'.$language['id_lang']);
                    if(trim($description) && !Validate::isCleanHtml($description, true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                    elseif($description && !Ybc_blog::checkIframeHTML($description))
                        $errors[] = sprintf($this->l('Description in %s is not valid.'),$language['name']).' '.$this->displayErrorIframe();
                    else
                        $post->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                    if($post->products && !preg_match('/^[0-9]+([\-0-9])*$/', $post->products))
                    {
                        $errors[] = $this->l('Products are not valid');
                    }
                    $tagStr = trim(Tools::getValue('tags_'.$language['id_lang']));
                    if($tagStr && Validate::isTagsList($tagStr))
                        $tags[$language['id_lang']] = explode(',',$tagStr);
                    elseif($tagStr && !Validate::isTagsList($tagStr))
                    {
                        $tags[$language['id_lang']] = array();
                        $errors[] = $this->l('Tags in '.$language['name'].' are not valid');
                    }
                    else
                        $tags[$language['id_lang']] = array();
                }
            }
            $oldImages = array();
            $newImages = array();
            $oldThumbs = array();
            $newThumbs = array();
            foreach($languages as $language)
            {
                /**
                 * Upload image
                 */
                $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;

                if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
                {
                    $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/',$_FILES['image_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
                        $imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
                        if (isset($_FILES['image_'.$language['id_lang']]) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {

                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
                                $errors[] = $error;
                            elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920), Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750), $type))
                                $errors[] = $this->l('An error occurred during the image upload process in').' '.$language['iso_code'];
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
                            if( $post->image && isset($post->image[$language['id_lang']]) && $post->image[$language['id_lang']])
                                $oldImages[$language['id_lang']] = $post->image[$language['id_lang']];
                            $post->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $post->image[$language['id_lang']];
                        }
                        elseif(isset($_FILES['image_'.$language['id_lang']]) &&
                            !empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
                            !empty($imagesize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png')
                            ))
                            $errors[] = $this->l('Image is invalid in').' '.$language['iso_code'];
                    }
                }


                /**
                 * Upload thumbnail
                 */

                if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
                {
                    $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Thumbnail image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Thumbnail image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                        {
                            $_FILES['thumb_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['thumb_'.$language['id_lang']]['name'], '.'), 1));
                        $thumbsize = @getimagesize($_FILES['thumb_'.$language['id_lang']]['tmp_name']);
                        if (isset($_FILES['thumb_'.$language['id_lang']]) &&
                            !empty($_FILES['thumb_'.$language['id_lang']]['tmp_name']) &&
                            !empty($thumbsize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            if ($error = ImageManager::validateUpload($_FILES['thumb_'.$language['id_lang']]))
                                $errors[] = $error;
                            elseif (!$temp_name || !move_uploaded_file($_FILES['thumb_'.$language['id_lang']]['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260), Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180), $type))
                                $errors[] = $this->l('An error occurred during the thumbnail upload process in').' '.$language['iso_code'];
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
                            if($post->thumb && isset($post->thumb[$language['id_lang']]) && $post->thumb[$language['id_lang']])
                                $oldThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];
                            $post->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                            $newThumbs[$language['id_lang']] = $post->thumb[$language['id_lang']];
                        }
                        elseif(isset($_FILES['thumb_'.$language['id_lang']]) &&
                            !in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                            $errors[] = $this->l('Thumbnail image is invalid in').' '.$language['iso_code'];
                    }
                }
            }

            foreach($languages as $language)
            {
                if(!(isset($post->thumb[$language['id_lang']]) && $post->thumb[$language['id_lang']]) && (isset($post->thumb[$id_lang_default]) && $post->thumb[$id_lang_default]))
                    $post->thumb[$language['id_lang']] = $post->thumb[$id_lang_default];
                if(!(isset($post->image[$language['id_lang']]) && $post->image[$language['id_lang']]) &&  isset($post->image[$id_lang_default]) && $post->image[$id_lang_default])
                    $post->image[$language['id_lang']] = $post->image[$id_lang_default];
            }
            /**
             * Save
             */
            $changedImages = array();
            if(!$errors)
            {
                if (!$id_post)
                {
                    if (!$post->add())
                    {
                        $errors[] = $this->module->displayError($this->l('The post could not be added.'));
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                        }
                        if($newThumbs)
                            foreach($newThumbs as $newThumb)
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb);
                    }
                    else
                    {
                        $id_post = Ybc_blog_defines::getMaxId('post','id_post');
                        Ybc_blog_post_class::updateCategories($categories, $id_post);
                        $relatedCategories= Tools::getValue('related_categories');
                        if(Ybc_blog::validateArray($relatedCategories))
                            Ybc_blog_post_class::updateRelatedCategories($relatedCategories,$id_post);
                        $product_categories = Tools::getValue('product_categories');
                        if(Ybc_blog::validateArray($product_categories))
                        {
                            Ybc_blog_post_class::updateRelatedProductCategories($product_categories,$id_post);
                        }
                        Ybc_blog_post_class::updateTags($id_post, $tags);
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_post' =>(int)$post->id,
                            'image' => $newImages ? $post->image :false,
                            'thumb' => $newThumbs ? $post->thumb : false,
                        ));
                    }

                }
                elseif (!$post->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
                    }
                    if($newThumbs)
                        foreach($newThumbs as $newThumb)
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb);
                    $errors[] = $this->module->displayError($this->l('The post could not be updated.'));
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                        {
                            if(!in_array($oldImage,$post->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$oldImage);
                        }
                    }
                    if($oldThumbs)
                    {
                        foreach($oldThumbs as $oldThumb)
                        {
                            if(!in_array($oldThumb,$post->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$oldThumb);
                        }
                    }
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_post' =>(int)$post->id,
                        'image' => $newImages ? $post->image :false,
                        'thumb' => $newThumbs ? $post->thumb : false,
                    ));
                    Ybc_blog_post_class::updateCategories($categories, $id_post);
                    $relatedCategories= Tools::getValue('related_categories');
                    if(Ybc_blog::validateArray($relatedCategories))
                        Ybc_blog_post_class::updateRelatedCategories($relatedCategories,$id_post);
                    $product_categories = Tools::getValue('product_categories');
                    if(Ybc_blog::validateArray($product_categories))
                    {
                        Ybc_blog_post_class::updateRelatedProductCategories($product_categories,$id_post);
                    }
                    Ybc_blog_post_class::updateTags($id_post, $tags);
                    $customer= new Customer($post->added_by);
                    if(($subject = Ybc_blog_email_template_class::getSubjectByTemplate('approved_blog_customer',$customer->id_lang)) && $updatestatus &&  $post->is_customer)
                    {
                        $template_customer_vars=array(
                            '{customer_name}' => $customer->firstname .' '.$customer->lastname,
                            '{post_title}' => $post->title[$this->context->language->id],
                            '{post_link}'=> $this->module->getLink('blog',array('id_post'=>$post->id)),
                            '{color_main}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR'),
                            '{color_hover}'=>Configuration::get('YBC_BLOG_CUSTOM_COLOR_HOVER')
                        );
                        Mail::Send(
                            $customer->id_lang,
                            'approved_blog_customer',
                            $this->l('Your post has been approved'),
                            $template_customer_vars,
                            $customer->email,
                            $customer->firstname .' '.$customer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MODULE_DIR_.'ybc_blog'.'/mails/'
                        );
                    }
                }
                Hook::exec('actionUpdateBlog', array(
                    'id_post' =>(int)$post->id,
                ));
            }
        }
        if (count($errors))
        {
            if($newImages)
            {
                foreach($newImages as $newImage)
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/'.$newImage);
            }
            if($newThumbs)
                foreach($newThumbs as $newThumb)
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'post/thumb/'.$newThumb);
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        if(isset($newThumbs) && $newThumbs && !$errors && isset($post))
        {
            foreach($languages as $language)
            {
                $changedImages[] = array(
                    'name' => 'thumb_'.$language['id_lang'],
                    'url' => _PS_YBC_BLOG_IMG_.'post/thumb/'.$post->thumb[$language['id_lang']],
                    'delete_url' => false,
                );
            }
        }
        if(isset($newImages) && $newImages && !$errors && isset($post)){
            foreach($languages as $language)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$language['id_lang'],
                    'url' => _PS_YBC_BLOG_IMG_.'post/'.$post->image[$language['id_lang']],
                    'delete_url' => $this->baseLink.'&id_post='.$id_post.'&delpostimage=true&id_lang='.$language['id_lang'],
                );
            }

        }
        if(Tools::isSubmit('ajax'))
        {
            $itemId= !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? Ybc_blog_defines::getMaxId('post','id_post') : ((int)$id_post > 0 ? (int)$id_post : 0);
            $array = array(
                'messageType' => $errors ? 'error' : 'success',
                'message' => $errors ? $this->module->errorMessage : (isset($id_post) && $id_post ? $this->module->displaySuccessMessage($this->l('Post has been saved'),$this->l('View this post'),$this->module->getLink('blog',array('id_post'=>$id_post))) : $this->module->displayConfirmation($this->l('Post saved'))),
                'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                'postUrl' => !$errors && Tools::isSubmit('savePost') && !(int)$id_post ? $this->baseLink.'&id_post='.Ybc_blog_defines::getMaxId('post','id_post') : 0,
                'itemKey' => 'id_post',
                'itemId' => $itemId,
                'link_preview'=> Tools::isSubmit('submitSaveAndPreview') && !$errors  ? $this->module->getLink('blog',array('id_post'=>$post->id,'preview'=>1)):'',
            );
            if(!$errors)
                $array['form_author_post']= $this->getFormAuthorPost($itemId);
            die(json_encode(
                $array
            ));
        }

        if(!$errors)
        {
            if (Tools::isSubmit('savePost') && Tools::isSubmit('id_post'))
                Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_post&id_post='.$id_post.'&control=post');
            elseif (Tools::isSubmit('savePost'))
            {
                Tools::redirectAdmin($this->baseLink.'&conf=3&editybc_post&id_post='.Ybc_blog_defines::getMaxId('post','id_post'));
            }
        }
    }
    public function displayErrorIframe()
    {
        if(!$this->module->isCached('iframe.tpl',$this->module->_getCacheId()))
        {
            $this->context->smarty->assign(
                array(
                    'link' => $this->context->link,
                )
            );
        }
        return $this->module->display($this->module->getLocalPath(),'iframe.tpl',$this->module->_getCacheId());
    }
}
