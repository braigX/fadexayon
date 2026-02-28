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
 * Class AdminYbcBlogCategoryController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogCategoryController extends ModuleAdminController
{
    public $errorMessage;
    public $baseLink;
    private $depthLevel = false;
    private $prefix = '-';
    private $blogCategoryDropDown;
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogCategory');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog posts and blog categories'))
        {
            $this->checked = true;
            $this->_postCategory();
        }

    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('category'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }

    public function _getContent()
    {
        if(Tools::isSubmit('addNew') || Tools::isSubmit('editybc_category'))
            return $this->renderCategoryForm();
        else
            return $this->renderListCategories();
    }
    public function renderListCategories()
    {
        $fields_list = array(
            'id_category' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'thumb_link'=>array(
                'title'=> $this->l('Image'),
                'type' => 'text',
                'strip_tag'=>false,
            ),
            'title' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'sort_order' => array(
                'title' => $this->l('Sort order'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'update_position' => true,
            ),
            'enabled' => array(
                'title' => $this->l('Enabled'),
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
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            ),
        );
        //Filter
        $filter = "";
        if(($idCategory = trim(Tools::getValue('id_category')))!='' && Validate::isCleanHtml($idCategory))
            $filter .= " AND c.id_category = ".(int)$idCategory;
        if(($sort_order = trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
            $filter .= " AND c.sort_order = ".(int)$sort_order;
        if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            $filter .= " AND cl.title like '%".pSQL($title)."%'";
        if(($description =trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
            $filter .= " AND cl.description like '%".pSQL($description)."%'";
        if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
            $filter .= " AND c.enabled =".(int)$enabled;
        if($filter)
            $show_reset = true;
        else
            $show_reset =false;
        //Sort
        $sort = "";
        $sort_post = Tools::strtolower(trim(Tools::getValue('sort','id_category')));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type ='desc';
        if($sort_post && isset($fields_list[$sort_post]))
        {
            $sort .= ($sort_post =='id_category' ? 'c.id_category' : $sort_post)." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = "c.sort_order ASC,";

        //Paggination
        $id_parent = (int)Tools::getValue('id_parent');
        $page = (int)Tools::getValue('page');
        $totalRecords = (int)Ybc_blog_category_class::countCategoriesWithFilter($filter,$id_parent);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&id_parent='.(int)$id_parent.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_category_select_limit',20);
        $paggination->name ='ybc_category';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $categories = Ybc_blog_category_class::getCategoriesWithFilter($filter, $sort, $start, $paggination->limit,$id_parent);
        if($categories)
        {
            foreach($categories as &$cat)
            {
                $cat['view_url'] = $this->module->getLink('blog',array('id_category' => $cat['id_category']));
                if(Ybc_blog_category_class::getChildrenBlogCategories($cat['id_category'],false) )
                {
                    $cat['child_view_url'] = $this->baseLink.'&id_parent='.(int)$cat['id_category'];
                }
                if($cat['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$cat['thumb']))
                    $cat['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/thumb/'.$cat['thumb'].'" style="width:40px;"/>';
                elseif($cat['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$cat['image']))
                    $cat['thumb_link'] = '<img src="'._PS_YBC_BLOG_IMG_.'category/'.$cat['image'].'" style="width:40px;"/>';
                else
                    $cat['thumb_link']='';
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $thumb='';
        $lever=0;
        $listData = array(
            'name' => 'ybc_category',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink.'&id_parent='.(int)$id_parent.($paggination->limit!=20 ? '&paginator_ybc_category_select_limit='.$paggination->limit:''),
            'identifier' => 'id_category',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => ($id_parent ? $this->module->displayText($this->l('Categories'),'a',null,null,$this->baseLink):$this->l('Categories')). ( $id_parent ?  $this->module->getThumbCategory($id_parent,$thumb,$lever):''),
            'fields_list' => $fields_list,
            'field_values' => $categories,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_post,
            'sort_type' => $sort_type,
        );
        return $this->module->renderList($listData);
    }
    public function getBlogCategoriesDropdown($blogcategories, &$depth_level = -1,$selected_blog_category=0)
    {
        if($blogcategories)
        {
            $depth_level++;
            foreach($blogcategories as $category)
            {
                if((!$this->depthLevel || $this->depthLevel && (int)$depth_level <= $this->depthLevel))
                {
                    $levelSeparator = '';
                    if($depth_level >= 1)
                    {
                        for($i = 0; $i <= $depth_level-1; $i++)
                        {
                            $levelSeparator .= $this->prefix;
                        }
                    }
                    if($category['id_category'] >=0)
                        $this->blogCategoryDropDown .= $this->displayBlogOption((int)$selected_blog_category,(int)$category['id_category'],$depth_level,$levelSeparator,$category['title']);
                    if(isset($category['children']) && $category['children'])
                    {
                        $this->getBlogCategoriesDropdown($category['children'], $depth_level,$selected_blog_category);
                    }
                }
            }
            $depth_level--;
        }
    }
    public function displayBlogOption($selected_blog_category,$id_category,$depth_level,$levelSeparator,$title)
    {
        $this->context->smarty->assign(array(
            'selected_blog_category' => $selected_blog_category,
            'id_category' => $id_category,
            'depth_level' => $depth_level,
            'levelSeparator' => $levelSeparator,
            'title' => $title,
        ));
        return $this->module->display($this->module->getLocalPath(), 'blogoption.tpl');
    }
    public function renderCategoryForm()
    {
        if(($id_category =  (int)Tools::getValue('id_category')))
        {
            $blogCategory= new Ybc_blog_category_class($id_category);
        }
        else
            $blogCategory= new Ybc_blog_category_class();
        $blogcategoriesTree= Ybc_blog_category_class::getBlogCategoriesTree(0,true,$this->context->language->id,$id_category);
        $depth_level =-1;
        $this->getBlogCategoriesDropdown($blogcategoriesTree,$depth_level,$blogCategory->id_parent,$id_category);
        $blogCategoryotpionsHtml = $this->blogCategoryDropDown;
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Manage categories'),
                    'icon' => 'icon-AdminCatalog',
                ),
                'input' => array(
                    array(
                        'type'=>'select_category',
                        'label'=>$this->l('Parent category'),
                        'name'=>'id_parent',
                        'blogCategoryotpionsHtml'=>$blogCategoryotpionsHtml,
                        'form_group_class'=>'parent_category',
                        'tab'=>'basic',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Category title'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                        'class' => 'title',
                        'tab'=>'basic',
                        'desc' => $this->l('Invalid characters: <>;=#{}'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta title'),
                        'name' => 'meta_title',
                        'lang' => true,
                        'tab'=>'seo',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta description'),
                        'name' => 'meta_description',
                        'lang' => true,
                        'tab'=>'seo',
                        'desc' => $this->l('Should contain your focus keyword and be attractive. Meta description should be less than 300 characters.'),
                    ),
                    array(
                        'type' => 'tags',
                        'label' => $this->l('Meta keywords'),
                        'name' => 'meta_keywords',
                        'lang' => true,
                        'tab'=>'seo',
                        'hint' => array(
                            $this->l('To add "keywords" click in the field, write something, and then press "Enter."'),
                        ),
                        'desc'=>$this->l('Enter your focus keywords and minor keywords'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
                        'lang' => true,
                        'tab'=>'basic',
                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Url alias'),
                        'name' => 'url_alias',
                        'required' => true,
                        'lang'=>true,
                        'tab'=>'seo',
                        'hint' => $this->l('Only letters and the hyphen (-) character are allowed.'),
                        'desc' => $this->l('Should be as short as possible and contain your focus keyword.').($id_category ? $this->module->displayText($this->l('View category'),'a','ybc_link_view',null,$this->module->getLink('blog',array('id_category'=>$id_category)),true):''),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Category thumbnail image'),
                        'name' => 'thumb',
                        'imageType' => 'thumb',
                        'tab'=>'basic',
                        'desc' =>sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH',null,null,null,300),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT',null,null,null,170))
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Main category image'),
                        'name' => 'image',
                        'tab'=>'basic',
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_WIDTH',null,null,null,1920),Configuration::get('YBC_BLOG_IMAGE_CATEGORY_HEIGHT',null,null,null,750)),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'enabled',
                        'is_bool' => true,
                        'tab'=>'basic',
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
                        )
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'control'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = 'id_category';
        $helper->submit_action = 'saveCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogCategory', false);
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogCategory');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getCategoryField(),'id_category','Ybc_blog_category_class','saveCategory'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'cancel_url' => $this->baseLink,
            'post_key' => 'id_category',
            'tab_category'=>true,
            'image_baseurl' =>_PS_YBC_BLOG_IMG_.'category/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'category/thumb/',
            'addNewUrl' => $this->baseLink.'&addNew=1',
        );

        if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) )
        {
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_category');
            if($category->image)
            {
                $helper->tpl_vars['img_del_link'] = $this->baseLink.'&id_category='.$id_category.'&delcategoryimage=true';
            }
            if($category->thumb)
            {
                $helper->tpl_vars['thumb_del_link'] = $this->baseLink.'&id_category='.$id_category.'&delcategorythumb=true';
            }
        }
        return  $helper->generateForm(array($fields_form));
    }
    private function actionChangeStatus()
    {
        $id_category = (int)Tools::getValue('id_category');
        Hook::exec('actionUpdateBlog', array(
            'id_category' =>(int)$id_category,
        ));
        $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
        $field = Tools::getValue('field');
        if(($field == 'enabled' && $id_category))
        {
            Ybc_blog_defines::changeStatus('category',$field,$id_category,$status);
            if($status==1)
                $title= $this->l('Click to disabled');
            else
                $title=$this->l('Click to enabled');
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(array(
                    'listId' => $id_category,
                    'enabled' => $status,
                    'field' => $field,
                    'message' => $this->module->displaySuccessMessage($this->l('The status has been successfully updated')) ,
                    'messageType'=>'success',
                    'title'=>$title,
                    'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_category='.$id_category,
                )));
            }
            Tools::redirectAdmin($this->baseLink.'&conf=4');
        }
    }
    private function actionDeleteImageCategory($id_category)
    {
        if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category))
        {
            Hook::exec('actionUpdateBlog', array(
                'id_category' => (int)$id_category,
            ));
            $idLang = (int)Tools::getValue('id_lang');
            if(isset($category->image[$idLang]) && $category->image[$idLang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$category->image[$idLang]))
            {
                $oldImage = $category->image[$idLang];
                $category->image[$idLang] = '';
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if($category->update())
                {
                    if(!in_array($oldImage,$category->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage);
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->module->displayConfirmation($this->l('Category image deleted')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
    }
    private function actionDeleteThumbCategory($id_category)
    {
        if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category)) {
            Hook::exec('actionUpdateBlog', array(
                'id_category' => (int)$id_category,
            ));
            $idLang = (int)Tools::getValue('id_lang');
            if(isset($category->thumb[$idLang]) && $category->thumb[$idLang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$category->thumb[$idLang])) {
                $oldThumb = $category->thumb[$idLang];
                $category->thumb[$idLang] = '';
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if ($category->update()) {
                    if (!in_array($oldThumb, $category->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_ . 'category/thumb/' . $oldThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_ . 'category/thumb/' . $oldThumb);
                }
                if (Tools::isSubmit('ajax')) {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->module->displayConfirmation($this->l('Category thumbnail image deleted')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->baseLink. '&conf=4');
            }
        }
    }
    private function actionUpdateCategoryOrdering($categories)
    {
        $page = (int)Tools::getValue('page',1);
        if(Ybc_blog_category_class::updateCategoryOrdering($categories,$page))
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
    private function _postCategory()
    {
        $errors = array();
        $id_category = (int)Tools::getValue('id_category');
        if(Tools::isSubmit('editybc_category') && $id_category && !Validate::isLoadedObject(new Ybc_blog_category_class($id_category)))
            Tools::redirectAdmin($this->baseLink);
        if(Tools::isSubmit('change_enabled'))
        {
            $this->actionChangeStatus();
        }
        if($id_category && Tools::isSubmit('delcategoryimage') )
        {
            $this->actionDeleteImageCategory($id_category);
        }
        if($id_category  && Tools::isSubmit('delcategorythumb'))
        {
            $this->actionDeleteThumbCategory($id_category);
        }
        if(($action = Tools::getValue('action')) && $action=='updateCategoryOrdering' && ($categories=Tools::getValue('cateogires')) && Ybc_blog::validateArray($categories,'isInt'))
        {
            $this->actionUpdateCategoryOrdering($categories);
        }
        if(Tools::isSubmit('del'))
        {
            $id_category = (int)Tools::getValue('id_category');
            Hook::exec('actionUpdateBlog', array(
                'id_category' => (int)$id_category,
            ));
            $category = new Ybc_blog_category_class($id_category);
            if(!Validate::isLoadedObject($category))
                $errors[] = $this->l('Category does not exist');
            elseif($category->delete())
            {
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
            else
                $errors[] = $this->l('Could not delete the category. Please try again');
        }
        /**
         * Save category
         */
        if(Tools::isSubmit('saveCategory'))
        {
            $id_parent = (int)Tools::getValue('id_parent');
            if($id_category && ($category = new Ybc_blog_category_class($id_category)) && Validate::isLoadedObject($category) )
            {
                Hook::exec('actionUpdateBlog', array(
                    'id_category' => (int)$id_category,
                ));
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                if($id_parent!=$category->id_parent)
                {
                    $category->sort_order = 1+ (int)Ybc_blog_category_class::getMaxSortOrder($id_parent);
                }
            }
            else
            {
                $category = new Ybc_blog_category_class();
                $category->datetime_added = date('Y-m-d H:i:s');
                $category->datetime_modified = date('Y-m-d H:i:s');
                $category->modified_by = (int)$this->context->employee->id;
                $category->added_by = (int)$this->context->employee->id;
                $category->sort_order = 1+ (int)Ybc_blog_category_class::getMaxSortOrder($id_parent);
            }
            $category->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $category->id_parent = (int)$id_parent;
            $languages = Language::getLanguages(false);
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $title_default = trim(Tools::getValue('title_'.$id_lang_default));
            if(!$title_default)
                $errors[] = $this->l('Title is required');
            if($title_default && !Validate::isCleanHtml($title_default))
                $errors[] = $this->l('Title is not valid');
            $meta_title_default = Tools::getValue('meta_title_'.$id_lang_default);
            if($meta_title_default && !Validate::isCleanHtml($meta_title_default))
                $errors[] = $this->l('Meta title is not valid');
            $url_alias_default = Tools::getValue('url_alias_'.$id_lang_default);
            if(!$url_alias_default)
                $errors[] = $this->l('Url alias is required');
            if($url_alias_default && !Ybc_blog::checkIsLinkRewrite($url_alias_default))
                $errors[] = $this->l('Url alias is not valid');
            elseif($url_alias_default && Ybc_blog_category_class::checkUrlAliasExists($url_alias_default,$category->id) )
                $errors[] = $this->l('Url alias has already existed');
            $meta_description_default = Tools::getValue('meta_description_'.$id_lang_default);
            if($meta_description_default && !Validate::isCleanHtml($meta_description_default,true))
                $errors[] = $this->l('Meta description is not valid');
            $meta_keywords_default = Tools::getValue('meta_keywords_'.$id_lang_default);
            if($meta_keywords_default && !Validate::isTagsList($meta_keywords_default))
                $errors[] = $this->l('Meta keyword is not valid');
            $description_default = Tools::getValue('description_'.$id_lang_default);
            if($description_default && !Validate::isCleanHtml($description_default,true))
                $errors[] = $this->l('Description is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
                {
                    $id_lang = (int)$language['id_lang'];
                    $title = trim(Tools::getValue('title_'.$language['id_lang']));
                    if($title && !Validate::isCleanHtml($title))
                        $errors[] = sprintf($this->l('Title in %s is not valid'),$language['name']);
                    else
                        $category->title[$language['id_lang']] = $title != '' ?  $title:  $title_default;
                    $meta_title = trim(Tools::getValue('meta_title_'.$language['id_lang']));
                    if($meta_title && !Validate::isCleanHtml($meta_title))
                        $errors[] = sprintf($this->l('Meta title in %s is not valid'),$language['name']);
                    else
                        $category->meta_title[$language['id_lang']] = $meta_title != '' ? $meta_title :  $meta_title_default;
                    $url_alias = trim(Tools::getValue('url_alias_'.$language['id_lang']));
                    if($url_alias && !Ybc_blog::checkIsLinkRewrite($url_alias))
                        $errors[] = sprintf($this->l('Url alias in %s is not valid'),$language['name']);
                    elseif($url_alias && Ybc_blog_category_class::checkUrlAliasExists($url_alias,$category->id) )
                        $errors[] = sprintf($this->l('Url alias in %s has already existed'),$language['name']);
                    else
                        $category->url_alias[$language['id_lang']] = $url_alias != '' ? $url_alias :  $url_alias_default;
                    $meta_description = Tools::getValue('meta_description_'.$id_lang);
                    if($meta_description && !Validate::isCleanHtml($meta_description, true))
                        $errors[] = sprintf($this->l('Meta description in %s is not valid'),$language['name']);
                    else
                        $category->meta_description[$language['id_lang']] = $meta_description != '' ? $meta_description :  $meta_description_default;
                    $meta_keywords = Tools::getValue('meta_keywords_'.$id_lang);
                    if($meta_keywords && !Validate::isTagsList($meta_keywords))
                        $errors[] = sprintf($this->l('Meta keywords in %s are not valid'),$language['name']);
                    else
                        $category->meta_keywords[$language['id_lang']] = $meta_keywords != '' ? $meta_keywords : $meta_keywords_default;
                    $description = Tools::getValue('description_'.$id_lang);
                    if($description && !Validate::isCleanHtml($description, true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                    $category->description[$language['id_lang']] = $description != '' ? $description :  $description_default;

                }
            }

            /**
             * Upload image
             */
            $oldImages = array();
            $newImages = array();
            $oldThumbs = array();
            $newThumbs = array();
            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
            foreach($languages as $language)
            {
                if(isset($_FILES['image_'.$language['id_lang']]['tmp_name']) && isset($_FILES['image_'.$language['id_lang']]['name']) && $_FILES['image_'.$language['id_lang']]['name'])
                {
                    $_FILES['image_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['image_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['image_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['image_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'category/',$_FILES['image_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));
                        $imagesize = @getimagesize($_FILES['image_'.$language['id_lang']]['tmp_name']);
                        if (isset($_FILES['image_'.$language['id_lang']]) &&
                            !empty($_FILES['image_'.$language['id_lang']]['tmp_name']) &&
                            !empty($imagesize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']]))
                                $errors[] = $error;
                            elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.$language['id_lang']]['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'category/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_CATEGORY_WIDTH',null,null,null,1920), Configuration::get('YBC_BLOG_IMAGE_CATEGORY_HEIGHT',null,null,null,750), $type))
                                $errors[] = sprintf($this->l('An error occurred during the image upload process in %s'),$language['iso_code']);
                            if ( file_exists($temp_name))
                                @unlink($temp_name);
                            if(isset($category->image[$language['id_lang']]) &&  $category->image[$language['id_lang']])
                                $oldImages[$language['id_lang']] = $category->image[$language['id_lang']];
                            $category->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $category->image[$language['id_lang']];
                        }
                        else
                            $errors[] = sprintf($this->l('Image is not valid in %s'),$language['iso_code']);
                    }

                }
                if(isset($_FILES['thumb_'.$language['id_lang']]['tmp_name']) && isset($_FILES['thumb_'.$language['id_lang']]['name']) && $_FILES['thumb_'.$language['id_lang']]['name'])
                {
                    $_FILES['thumb_'.$language['id_lang']]['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['thumb_'.$language['id_lang']]['name']);
                    if(!Validate::isFileName($_FILES['thumb_'.$language['id_lang']]['name']))
                        $errors[] = sprintf($this->l('Thumbnail image name is not valid in %s'),$language['iso_code']);
                    elseif($_FILES['thumb_'.$language['id_lang']]['size'] > $max_file_size)
                        $errors[] = sprintf($this->l('Thumbnail image file is too large. Limit: %s'),Tools::ps_round($max_file_size/1048576,2).'Mb');
                    else
                    {
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                        {
                            $_FILES['thumb_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
                        }
                        $type = Tools::strtolower(Tools::substr(strrchr($_FILES['thumb_'.$language['id_lang']]['name'], '.'), 1));
                        $imagesize = @getimagesize($_FILES['thumb_'.$language['id_lang']]['tmp_name']);
                        if (isset($_FILES['thumb_'.$language['id_lang']]) &&
                            !empty($_FILES['thumb_'.$language['id_lang']]['tmp_name']) &&
                            !empty($imagesize) &&
                            in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                        )
                        {
                            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                            if ($error = ImageManager::validateUpload($_FILES['thumb_'.$language['id_lang']]))
                                $errors[] = $error;
                            elseif (!$temp_name || !move_uploaded_file($_FILES['thumb_'.$language['id_lang']]['tmp_name'], $temp_name))
                                $errors[] = $this->l('Cannot upload the file in').' '.$language['iso_code'];
                            elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_WIDTH',null,null,null,300), Configuration::get('YBC_BLOG_IMAGE_CATEGORY_THUMB_HEIGHT',null,null,null,170), $type))
                                $errors[] = sprintf($this->l('An error occurred during the image upload process in %s'),$language['iso_code']);
                            if (file_exists($temp_name))
                                @unlink($temp_name);
                            if(isset($category->thumb[$language['id_lang']]) &&  $category->thumb[$language['id_lang']])
                                $oldThumbs[$language['id_lang']] = $category->thumb[$language['id_lang']];
                            $category->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                            $newThumbs[] = $category->thumb[$language['id_lang']];
                        }
                        else
                            $errors[] = sprintf($this->l('Thumbnail image is not valid in %s'),$language['iso_code']);
                    }
                }
            }
            foreach($languages as $language)
            {
                if(!(isset($category->image[$language['id_lang']]) && $category->image[$language['id_lang']]) && isset($category->image[$id_lang_default]) && $category->image[$id_lang_default] )
                    $category->image[$language['id_lang']] = $category->image[$id_lang_default];
                if(!(isset($category->thumb[$language['id_lang']]) && $category->thumb[$language['id_lang']]) && isset($category->thumb[$id_lang_default]) && $category->thumb[$id_lang_default])
                    $category->thumb[$language['id_lang']] = $category->thumb[$id_lang_default];
            }
            /**
             * Save
             */

            if(!$errors)
            {
                if (!$id_category)
                {
                    if (!$category->add())
                    {
                        $errors[] = $this->l('The category could not be added.');
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                            }
                        }
                        if($newThumbs)
                        {
                            foreach($newThumbs as $newThumb)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                            }
                        }
                    }
                    else
                    {
                        $id_category = Ybc_blog_defines::getMaxId('category','id_category');
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_category' =>(int)$category->id,
                            'image' => $newImages ? $category->image :false,
                            'thumb' => $newThumbs ? $category->thumb : false,
                        ));
                    }
                }
                elseif (!$category->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                        }
                    }
                    if($newThumbs)
                    {
                        foreach($newThumbs as $newThumb)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                        }
                    }
                    $errors[] = $this->l('The category could not be updated.');
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                        {
                            if(!in_array($oldImage,$category->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$oldImage);
                        }
                    }
                    if($oldThumbs)
                    {
                        foreach($oldThumbs as $oldThumb)
                        {
                            if(!in_array($oldThumb,$category->thumb) &&  file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$oldThumb);
                        }
                    }
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_category' =>(int)$category->id,
                        'image' => $newImages ? $category->image :false,
                        'thumb' => $newThumbs ? $category->thumb : false,
                    ));
                }

            }
        }
        if (count($errors))
        {
            if($newImages)
            {
                foreach($newImages as $newImage)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/'.$newImage);
                }
            }
            if(isset($newThumbs) && $newThumbs)
            {
                foreach($newThumbs as $newThumb)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'category/thumb/'.$newThumb);
                }
            }
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        $changedImages = array();
        if(!$errors && isset($newImages) && $newImages && isset($category) && $category->id){
            foreach($newImages as $id_lang=> $newImage)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'category/'.$newImage,
                    'delete_url' => $this->baseLink.'&id_category='.$id_category.'&delcategoryimage=true&id_lang='.$id_lang,
                );
            }
        }
        if(!$errors && isset($newThumbs) && $newThumbs && isset($category) && $category->id){
            foreach($newThumbs as $id_lang => $newThumb)
            {
                $changedImages[] = array(
                    'name' => 'thumb_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'category/thumb/'.$newThumb,
                    'delete_url' => $this->baseLink.'&id_category='.$id_category.'&delcategorythumb=true&id_lang='.$id_lang,
                );
            }
        }
        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->module->errorMessage : (isset($id_category) && $id_category ? $this->module->displaySuccessMessage($this->l('Category updated'),$this->l('View category'),$this->module->getLink('blog',array('id_category'=>$id_category))) : $this->module->displayConfirmation($this->l('Category updated'))),
                    'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                    'postUrl' => !$errors && Tools::isSubmit('saveCategory') && !(int)$id_category ? $this->baseLink.'&id_category='.Ybc_blog_defines::getMaxId('category','id_category') : 0,
                    'itemKey' => 'id_category',
                    'itemId' => !$errors && Tools::isSubmit('saveCategory') && !(int)$id_category ? Ybc_blog_defines::getMaxId('category','id_category') : ((int)$id_category > 0 ? (int)$id_category : 0),
                )
            ));
        }
        if (Tools::isSubmit('saveCategory') && Tools::isSubmit('id_category'))
            Tools::redirectAdmin($this->baseLink.'&conf=4&id_category='.$id_category);
        elseif (Tools::isSubmit('saveCategory'))
        {
            Tools::redirectAdmin($this->baseLink.'&conf=3&id_category='.Ybc_blog_defines::getMaxId('category','id_category'));
        }
    }
}
