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
 * Class AdminYbcBlogGalleryController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogGalleryController extends ModuleAdminController
{
    public $baseLink;
    public $_html='';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogGallery');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Blog gallery'))
        {
            $this->checked = true;
            $this->_postGallery();
        }

    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('gallery'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getContent()
    {
        if(Tools::isSubmit('editybc_gallery') || Tools::isSubmit('addNew'))
        {
            return $this->renderGalleryForm();
        }
        else
            return $this->renderListGalleries();
    }
    private function renderListGalleries()
    {
        $fields_list = array(
            'id_gallery' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'thumb' => array(
                'title' => $this->l('Thumbnail'),
                'type' => 'text',
                'required' => true
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
            'is_featured' => array(
                'title' => $this->l('Featured'),
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
        if(($id = trim(Tools::getValue('id_gallery')))!='' && Validate::isCleanHtml($id))
            $filter .= " AND g.id_gallery = ".(int)$id;
        if(($sort_order =trim(Tools::getValue('sort_order')))!='' && Validate::isCleanHtml($sort_order))
            $filter .= " AND g.sort_order = ".(int)$sort_order;
        if(($title = trim(Tools::getValue('title')))!='' && Validate::isCleanHtml($title))
            $filter .= " AND gl.title like '%".pSQL($title)."%'";
        if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
            $filter .= " AND gl.description like '%".pSQL($description)."%'";
        if(($enabled = trim(Tools::getValue('enabled')))!='' && Validate::isCleanHtml($enabled))
            $filter .= " AND g.enabled =".(int)$enabled;
        if(($is_featured = trim(Tools::getValue('is_featured')))!='' && Validate::isCleanHtml($is_featured))
            $filter .= " AND g.is_featured =".(int)$is_featured;
        if($filter)
            $show_reset = true;
        else
            $show_reset = false;
        //Sort
        $sort = "";
        $sort_post = Tools::strtolower(trim(Tools::getValue('sort')));
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
        if(!in_array($sort_type,array('desc','asc')))
            $sort_type ='desc';
        if($sort_post && isset($fields_list[$sort_post]))
        {
            $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')." , ";
        }
        else
            $sort = 'g.sort_order asc,';

        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<=1)
            $page =1;
        $totalRecords = (int)Ybc_blog_gallery_class::countGalleriesWithFilter($filter);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_gallery_select_limit',20);
        $paggination->name ='ybc_gallery';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $galleries = Ybc_blog_gallery_class::getGalleriesWithFilter($filter, $sort, $start, $paggination->limit);
        if($galleries)
        {
            foreach($galleries as &$gallery)
            {
                if($gallery['thumb'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$gallery['thumb']))
                {
                    $gallery['thumb'] = array(
                        'image_field' => true,
                        'img_url' =>  _PS_YBC_BLOG_IMG_.'gallery/thumb/'.$gallery['thumb'],
                    );
                }
                elseif($gallery['image'] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$gallery['image']))
                {
                    $gallery['thumb'] = array(
                        'image_field' => true,
                        'img_url' =>  _PS_YBC_BLOG_IMG_.'gallery/'.$gallery['image'],
                    );
                }
                else
                    $gallery['thumb']=array();
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_gallery',
            'actions' => array('edit', 'delete', 'view'),
            'currentIndex' => $this->baseLink.($paggination->limit!=20 ? '&paginator_ybc_gallery_select_limit='.$paggination->limit:''),
            'identifier' => 'id_gallery',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Blog gallery'),
            'fields_list' => $fields_list,
            'field_values' => $galleries,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $show_reset,
            'totalRecords' => $totalRecords,
            'preview_link' => $this->module->getLink('gallery'),
            'sort' => $sort_post ? : 'sort_order',
            'sort_type'=>$sort_type,
        );
        return $this->module->renderList($listData);
    }
    private function renderGalleryForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Blog gallery'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Caption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Thumbnail image'),
                        'name' => 'thumb',
                        'imageType' => 'thumb',
                        'required' => true,
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH',null,null,null,180),Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT',null,null,null,180)),
                    ),
                    array(
                        'type' => 'file_lang',
                        'label' => $this->l('Large Image'),
                        'name' => 'image',
                        'required' => true,
                        'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: %sx%s.'),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),Configuration::get('YBC_BLOG_IMAGE_GALLERY_WIDTH',null,null,null,600),Configuration::get('YBC_BLOG_IMAGE_GALLERY_HEIGHT',null,null,null,600)),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Featured'),
                        'name' => 'is_featured',
                        'is_bool' => true,
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
                        'desc' => $this->l('Enable if you want to display this image in the featured gallery block on the front office')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'enabled',
                        'is_bool' => true,
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
        $helper->identifier = 'id_gallery';
        $helper->submit_action = 'saveGallery';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogGallery', false);
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogGallery');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->module->getFieldsValues(Ybc_blog_defines::getGalleryField(),'id_gallery','Ybc_blog_gallery_class','saveGallery'),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'link' => $this->context->link,
            'cancel_url' => $this->baseLink,
            'post_key' => 'id_gallery',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'gallery/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'gallery/thumb/',
            'addNewUrl' => $this->baseLink,
        );

        if(Tools::isSubmit('id_gallery') && ($id_gallery = (int)Tools::getValue('id_gallery')) && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery) )
        {

            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_gallery');
            if($gallery->image)
            {
                $helper->tpl_vars['img_del_link'] = $this->baseLink.'&editybc_gallery=1&id_gallery='.$id_gallery.'&delgalleryimage=true';
            }
            if($gallery->thumb)
            {
                $helper->tpl_vars['thumb_del_link'] = $this->baseLink.'&editybc_gallery=1&id_gallery='.$id_gallery.'&delgallerythumb=true';
            }
        }
        return $helper->generateForm(array($fields_form));
    }
    private function _postGallery()
    {
        $errors = array();
        $id_gallery = (int)Tools::getValue('id_gallery');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        if(Tools::isSubmit('editybc_gallery') &&  $id_gallery && !Validate::isLoadedObject(new Ybc_blog_gallery_class($id_gallery)) && !Tools::isSubmit('list'))
            Tools::redirectAdmin($this->baseLink);
        /**
         * Change status
         */
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled') ?  1 : 0;
            $field = Tools::getValue('field');
            $id_gallery = (int)Tools::getValue('id_gallery');
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            ));
            if(($field == 'enabled' || $field=='is_featured') && $id_gallery)
            {
                Ybc_blog_defines::changeStatus('gallery',$field,$id_gallery,$status);
                if($field=='enabled')
                {
                    if($status==1)
                        $title = $this->l('Click to unmark featured');
                    else
                        $title = $this->l('Click to mark as featured');
                }
                else
                {
                    if($status==1)
                        $title = $this->l('Click to unmark disabled');
                    else
                        $title = $this->l('Click to mark as enabled');
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_gallery,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $field=='enabled' ? $this->module->displaySuccessMessage($this->l('The status has been successfully updated')) : $this->module->displaySuccessMessage($this->l('The feature has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_gallery='.$id_gallery,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
        /**
         * Delete image
         */
        if($id_gallery && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery) && Tools::isSubmit('delgalleryimage'))
        {
            $id_lang = (int)Tools::getValue('id_lang');
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            ));
            if(isset($gallery->image[$id_lang]) && $gallery->image[$id_lang] && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$gallery->image[$id_lang]))
            {
                $oldImage = $gallery->image[$id_lang];
                $gallery->image[$id_lang] = $gallery->image[$id_lang_default];
                if($gallery->update())
                {
                    if(!in_array($oldImage,$gallery->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage);

                }
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->module->displayConfirmation($this->l('Image has been deleted')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
            else
                $errors[] = $this->l('Image is empty');

        }
        /**
         * Delete gallery
         */
        if(Tools::isSubmit('del'))
        {
            $id_gallery = (int)Tools::getValue('id_gallery');
            Hook::exec('actionUpdateBlog', array(
                'id_gallery' =>(int)$id_gallery,
            ));
            if(!(($gallery = new Ybc_blog_gallery_class($id_gallery)) &&  Validate::isLoadedObject($gallery)))
                $errors[] = $this->l('Item does not exist');
            elseif($gallery->delete())
            {
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
            else
                $errors[] = $this->l('Could not delete the item. Please try again');
        }
        // update sort_order
        if(($action = Tools::getValue('action')) && $action=='updateGalleryOrdering' && ($galleries=Tools::getValue('galleries')))
        {
            $page = (int)Tools::getValue('page',1);
            if(Ybc_blog_gallery_class::updateGalleryOrdering($galleries,$page))
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
        /**
         * Save gallery
         */
        if(Tools::isSubmit('saveGallery'))
        {
            if(!($id_gallery && ($gallery = new Ybc_blog_gallery_class($id_gallery)) && Validate::isLoadedObject($gallery)))
            {
                $gallery = new Ybc_blog_gallery_class();
                $gallery->sort_order = 1 + (int)Ybc_blog_gallery_class::getMaxSortOrder();
            }
            $gallery->enabled = (int)trim(Tools::getValue('enabled',1)) ? 1 : 0;
            $gallery->is_featured = (int)trim(Tools::getValue('is_featured',1)) ? 1 : 0;
            $languages = Language::getLanguages(false);
            $title_default = trim(Tools::getValue('title_'.Configuration::get('PS_LANG_DEFAULT')));
            if($title_default=='')
                $errors[] = $this->l('Name is required');
            elseif($title_default && !Validate::isCleanHtml($title_default))
                $errors[] = $this->l('Name is not valid');
            $description_default = trim(Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT')));
            if($description_default && !Validate::isCleanHtml($description_default,true))
                $errors[] = $this->l('Description is not valid');
            if(!$errors)
            {
                foreach ($languages as $language)
                {
                    $title = trim(Tools::getValue('title_'.$language['id_lang']));
                    if($title && !Validate::isCleanHtml($title))
                        $errors[] = sprintf($this->l('Name in %s is not valid'),$language['name']);
                    else
                        $gallery->title[$language['id_lang']] = $title != '' ? $title : $title_default;
                    $description = trim(Tools::getValue('description_'.$language['id_lang']));
                    if($description && !Validate::isCleanHtml($description,true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                    else
                        $gallery->description[$language['id_lang']] = $description != '' ? $description :  $description_default;
                }
            }
            /**
             * Upload image
             */
            $oldImages = array();
            $newImages = array();
            $newThumbs = array();
            $oldThumbs = array();
            if(!$id_gallery && (!isset($_FILES['image_'.$id_lang_default]['name']) || !$_FILES['image_'.$id_lang_default]['name']))
                $errors[] = $this->l('Image is required');
            if(!$id_gallery && (!isset($_FILES['thumb_'.$id_lang_default]['name']) || !$_FILES['thumb_'.$id_lang_default]['name']))
                $errors[] = $this->l('Thumbnail is required');
            foreach($languages as $language)
            {
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
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$_FILES['image_'.$language['id_lang']]['name']))
                        {
                            $_FILES['image_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'gallery/',$_FILES['image_'.$language['id_lang']]['name']);
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
                            elseif(!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'gallery/'.$_FILES['image_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_GALLERY_WIDTH',null,null,null,600), Configuration::get('YBC_BLOG_IMAGE_GALLERY_HEIGHT',null,null,null,600), $type))
                                $errors[] = $this->l('An error occurred during the image upload process in').' '.$language['iso_code'];
                            if($gallery->image && isset($gallery->image[$language['id_lang']]) && $gallery->image[$language['id_lang']])
                            {
                                $oldImages[$language['id_lang']] =$gallery->image[$language['id_lang']];
                            }
                            $gallery->image[$language['id_lang']] = $_FILES['image_'.$language['id_lang']]['name'];
                            $newImages[$language['id_lang']] = $gallery->image[$language['id_lang']];
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
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
                        if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name']))
                        {
                            $_FILES['thumb_'.$language['id_lang']]['name'] = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/',$_FILES['thumb_'.$language['id_lang']]['name']);
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
                            elseif(!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$_FILES['thumb_'.$language['id_lang']]['name'], Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH',null,null,null,180), Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT',null,null,null,180), $type))
                                $errors[] = $this->l('An error occurred during the image thumbnail upload process in').' '.$language['iso_code'];
                            if($gallery->thumb && isset($gallery->thumb[$language['id_lang']]) && $gallery->thumb[$language['id_lang']])
                            {
                                $oldThumbs[$language['id_lang']] = $gallery->thumb[$language['id_lang']];
                            }
                            $gallery->thumb[$language['id_lang']] = $_FILES['thumb_'.$language['id_lang']]['name'];
                            $newThumbs[$language['id_lang']] = $gallery->thumb[$language['id_lang']];
                            if (isset($temp_name) && file_exists($temp_name))
                                @unlink($temp_name);
                        }
                        else
                            $errors[] = sprintf($this->l('Thumbnail image is not valid in %s'),$language['iso_code']);
                    }

                }
            }
            foreach($languages as $language)
            {
                if(!($gallery->image && isset($gallery->image[$language['id_lang']]) && $gallery->image[$language['id_lang']]) && $gallery->image && isset($gallery->image[$id_lang_default]) && $gallery->image[$id_lang_default] )
                    $gallery->image[$language['id_lang']] = $gallery->image[$id_lang_default];
                if(!($gallery->thumb && isset($gallery->thumb[$language['id_lang']]) && $gallery->thumb[$language['id_lang']]) && $gallery->thumb && isset($gallery->thumb[$id_lang_default]) && $gallery->thumb[$id_lang_default])
                    $gallery->thumb[$language['id_lang']] = $gallery->thumb[$id_lang_default];
            }
            /**
             * Save
             */
            if(!$errors)
            {
                if (!$id_gallery)
                {
                    if (!$gallery->add())
                    {
                        $errors[] = $this->l('The item could not be added.');
                        if($newImages)
                        {
                            foreach($newImages as $newImage)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                            }
                        }
                        if($newThumbs)
                            foreach($newThumbs as $newThumb)
                            {
                                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                            }
                    }
                    else
                    {
                        Hook::exec('actionUpdateBlogImage', array(
                            'id_gallery' =>(int)$gallery->id,
                            'image' => $newImages ? $gallery->image :false,
                            'thumb' => $newThumbs ? $gallery->thumb : false,
                        ));
                    }
                }
                elseif (!$gallery->update())
                {
                    if($newImages)
                    {
                        foreach($newImages as $newImage)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                        }
                    }
                    if($newThumbs)
                    {
                        foreach($newThumbs as $newThumb)
                        {
                            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                        }
                    }
                    $errors[] = $this->l('The item could not be updated.');
                }
                else
                {
                    if($oldImages)
                    {
                        foreach($oldImages as $oldImage)
                            if(!in_array($oldImage,$gallery->image) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$oldImage);
                    }
                    if($oldThumbs)
                        foreach($oldThumbs as $oldThumb)
                            if(!in_array($oldThumb,$gallery->thumb) && file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$oldThumb))
                                @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$oldThumb);
                    Hook::exec('actionUpdateBlogImage', array(
                        'id_gallery' =>(int)$gallery->id,
                        'image' => $newImages ? $gallery->image :false,
                        'thumb' => $newThumbs ? $gallery->thumb : false,
                    ));
                }
                Hook::exec('actionUpdateBlog', array(
                    'id_gallery' =>(int)$gallery->id,
                ));
            }
        }
        $changedImages = array();
        if(isset($newImages) && $newImages && !$errors && isset($gallery)){

            foreach($newImages as $id_lang=>$newImage)
            {
                $changedImages[] = array(
                    'name' => 'image_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'gallery/'.$newImage,
                );
            }
        }
        if(isset($newThumbs) && $newThumbs && !$errors && isset($gallery)){
            foreach($newThumbs as $id_lang=> $newThumb)
            {
                $changedImages[] = array(
                    'name' => 'thumb_'.$id_lang,
                    'url' => _PS_YBC_BLOG_IMG_.'gallery/thumb/'.$newThumb,
                );
            }
        }
        if(Tools::isSubmit('ajax'))
        {
            die(json_encode(
                array(
                    'messageType' => $errors ? 'error' : 'success',
                    'message' => $errors ? $this->module->displayError($errors) : $this->module->displaySuccessMessage($this->l('Gallery image saved'),$this->l('View blog gallery'),$this->module->getLink('gallery')),
                    'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                    'postUrl' => !$errors && Tools::isSubmit('saveGallery') && !(int)$id_gallery ? $this->baseLink.'&editybc_gallery&id_gallery='.Ybc_blog_defines::getMaxId('gallery','id_gallery') : 0,
                    'itemKey' => 'id_gallery',
                    'itemId' => !$errors && Tools::isSubmit('saveGallery') && !(int)$id_gallery ? Ybc_blog_defines::getMaxId('gallery','id_gallery') : ((int)$id_gallery > 0 ? (int)$id_gallery : 0),
                )
            ));
        }
        if(count($errors))
        {
            if($newImages)
            {
                foreach($newImages as $newImage)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/'.$newImage);
                }
            }
            if($newThumbs)
                foreach($newThumbs as $newThumb)
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb))
                        @unlink(_PS_YBC_BLOG_IMG_DIR_.'gallery/thumb/'.$newThumb);
                }
            $this->module->errorMessage = $this->module->displayError($errors);
        }
        elseif (Tools::isSubmit('saveGallery') && $id_gallery)
            Tools::redirectAdmin($this->baseLink.'&conf=4editybc_gallery&id_gallery='.$id_gallery);
        elseif (Tools::isSubmit('saveGallery'))
        {
            Tools::redirectAdmin($this->baseLink.'&conf=3&editybc_gallery=1&id_gallery='.Ybc_blog_defines::getMaxId('gallery','id_gallery'));
        }
    }
}

