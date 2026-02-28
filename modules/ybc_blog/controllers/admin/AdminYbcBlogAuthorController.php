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
if(!class_exists('AdminYbcBlogController'))
    require_once dirname(__FILE__) . '/AdminYbcBlogController.php';
/**
 * Class AdminYbcBlogAuthorController
 * @property Ybc_blog $module;
 */
class AdminYbcBlogAuthorController extends AdminYbcBlogController
{
    public $baseLink;
    public $_html='';
    protected $checked = false;
    public function init()
    {
        parent::init();
        $this->baseLink = $this->context->link->getAdminLink('AdminYbcBlogAuthor');
        $this->bootstrap = true;
        if($this->module->checkProfileEmployee($this->context->employee->id,'Authors'))
        {
            $this->checked = true;
            if(($control=Tools::getValue('control')) && $control=='customer')
                $this->_postCustomer();
            elseif($control=='author')
                $this->_postCustomerSettingAuthor();
            else
                $this->_postEmployee();
        }
    }
    public function renderList()
    {
        if(!$this->checked)
            return $this->module->display($this->module->getLocalPath(),'error_access.tpl');
        $this->context->smarty->assign(array_merge($this->module->getAssign('employees'), array('ybc_blog_body_html' => $this->_getContent())));
        return $this->_html.$this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin.tpl');
    }
    public function _getContent()
    {
        return $this->displayTabAuthor().$this->renderEmployee().$this->renderCustomer().$this->renderSettingCustomer();
    }
    private function renderEmployee()
    {
        if(Tools::isSubmit('editybc_blog_employee') && ($id_employee = (int)Tools::getValue('id_employee')))
            return $this->renderEmployeeFrom($id_employee);
        else
            return $this->renderListEmployee();
    }
    private function renderListEmployee()
    {
        $fields_list = array(
            'id_employee' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'avata' => array(
                'title' => $this->l('Avatar'),
                'type' => 'text',
                'strip_tag' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'description' => array(
                'title' => $this->l('Introduction'),
                'type' => 'text',
                'filter'=>true,
            ),
            'profile_name'=>array(
                'title' => $this->l('Profile'),
                'type' => 'select',
                'filter'=>true,
                'filter_list'=>array(
                    'list'=> Profile::getProfiles((int)$this->context->language->id),
                    'id_option' => 'id_profile',
                    'value' => 'name',
                )
            ),
            'profile_employee' =>array(
                'title'=> $this->l('Accessible tabs'),
                'width'=>'140',
                'type'=>'select',
                'strip_tag' => false,
                'filter'=> true,
                'filter_list'=>array(
                    'list'=> array(
                        array(
                            'title'=>$this->l('All tabs'),
                            'id'=>'All tabs'
                        ),
                        array(
                            'title'=>$this->l('Blog posts and blog categories'),
                            'id'=>'Blog posts and blog categories'
                        ),
                        array(
                            'title'=>$this->l('Blog comments'),
                            'id'=>'Blog comments'
                        ),
                        array(
                            'title'=>$this->l('Blog slider'),
                            'id'=>'Blog slider'
                        ),
                        array(
                            'title'=>$this->l('Blog gallery'),
                            'id'=>'Blog gallery'
                        ),
                        array(
                            'title'=>$this->l('RSS feed'),
                            'id'=>'Rss feed'
                        ),
                        array(
                            'title'=>$this->l('SEO'),
                            'id'=>'Seo'
                        ),
                        array(
                            'title'=>$this->l('Socials'),
                            'id'=>'Socials'
                        ),
                        array(
                            'title'=>$this->l('Sitemap'),
                            'id'=>'Sitemap'
                        ),
                        array(
                            'title'=>$this->l('Email'),
                            'id'=>'Email'
                        ),
                        array(
                            'title'=>$this->l('Image'),
                            'id'=>'Image'
                        ),
                        array(
                            'title'=>$this->l('Sidebar'),
                            'id'=>'Sidebar'
                        ),
                        array(
                            'title'=>$this->l('Home page'),
                            'id'=>'Home page'
                        ),
                        array(
                            'title'=>$this->l('Post detail page'),
                            'id'=>'Post detail page'
                        ),
                        array(
                            'title'=>$this->l('Post listing pages'),
                            'id'=>'Post listing pages'
                        ),
                        array(
                            'title'=>$this->l('Category page'),
                            'id'=>'Category page'
                        ),
                        array(
                            'title'=>$this->l('Product detail page'),
                            'id'=>'Product detail page'
                        ),
                        array(
                            'title'=>$this->l('Authors'),
                            'id'=>'Authors'
                        ),
                        array(
                            'title'=>$this->l('Import/Export'),
                            'id'=>'Import/Export'
                        ),
                        array(
                            'title'=>$this->l('Statistics'),
                            'id'=>'Statistics'
                        ),
                        array(
                            'title'=>$this->l('Global settings'),
                            'id'=>'Global settings'
                        ),
                    ),
                    'id_option' => 'id',
                    'value' => 'title',
                )
            ),
            'total_post' =>array(
                'title'=> $this->l('Total posts'),
                'width'=>'140',
                'type'=>'int',
                'filter'=>true,
                'sort' => true,
            ),
            'status' => array(
                'title'=> $this->l('Status'),
                'type' => 'active',
                'strip_tag' => false,
                'filter'=>true,
                'sort' => true,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 0,
                            'title' => $this->l('Activated')
                        ),
                        1 => array(
                            'enabled' => 1,
                            'title' => $this->l('Suspended')
                        ),
                        2 => array(
                            'enabled' => -1,
                            'title' => $this->l('Suspended and hide posts')
                        )
                    )
                )
            )
        );
        //Filter
        $filter = "";
        $sort = "";
        $having="";
        if(Tools::isSubmit('post_filter') &&  !(($control = Tools::getValue('control')) && $control=='customer'))
        {
            if(($id = trim(Tools::getValue('id_employee')))!='' && Validate::isCleanHtml($id))
                $filter .= " AND e.id_employee = ".(int)$id;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(e.firstname,' ',e.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";
            if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                $filter .= " AND e.email like '%".pSQL($email)."%'";
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description))
                $filter .= " AND bel.description like '%".pSQL($description)."%'";
            if(($id_profile = trim(Tools::getValue('id_profile')))!='' && Validate::isCleanHtml($id_profile))
                $filter .= " AND pl.id_profile = '".(int)$id_profile."'";
            if(($profile_employee = trim(Tools::getValue('profile_employee')))!='' && Validate::isCleanHtml($profile_employee))
                $filter .= " AND (be.profile_employee like '%".pSQL($profile_employee)."%' OR p.id_profile=1 or be.profile_employee like '%All tabs%')  ";
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"';
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                $filter .= " AND (be.status= '".(int)$status."'".(!(int)$status ? ' or be.status is null':'' )." )";
            //Sort
            $sort_post = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type = 'desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {

                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')."";
            }
            else
                $sort = false;
        }
        //Paggination
        $page = (int)Tools::getValue('page');
        if($page<1)
            $page =1;
        $totalRecords = (int)Ybc_blog_post_employee_class::countEmployeesFilter($filter,$having);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_blog_employee_select_limit',20);
        $paggination->name ='ybc_blog_employee';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $employees = Ybc_blog_post_employee_class::getEmployeesFilter($filter, $sort, $start, $paggination->limit,$having);
        if($employees)
        {
            foreach($employees as &$employee)
            {
                if(!$employee['name'])
                    $employee['name']=$employee['employee'];
                if($employee['avata'])
                    $employee['avata'] = $this->module->displayText($this->module->displayText('','img',null,null,null,false,_PS_YBC_BLOG_IMG_.'avata/'.$employee['avata']),'div','avata_img');
                else
                    $employee['avata'] = $this->module->displayText($this->module->displayText('','img',null,null,null,false,_PS_YBC_BLOG_IMG_.'avata/default_customer.png'.$employee['avata']),'div','avata_img');
                if($employee['profile_employee'])
                {
                    if($employee['id_profile']==1 || Tools::strpos($employee['profile_employee'],'All tabs')!==false)
                        $employee['profile_employee'] = 'All tabs';
                    else
                        $employee['profile_employee'] = str_replace(',','<br/>',$employee['profile_employee']);
                }
                $employee['view_post_url'] = $this->module->getLink('blog',array('id_author'=> $employee['id_employee'],'alias'=> Tools::link_rewrite($employee['name'],true)));
                $employee['delete_post_url'] = $this->baseLink.'&list=true&deleteAllPostEmployee&id_author='.(int)$employee['id_employee'];
                $employee['name'] = $this->module->displayText($employee['name'],'a',null,null,$this->context->link->getAdminLink('AdminEmployees').'&updateemployee&id_employee='.(int)$employee['id_employee']);
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_blog_employee',
            'actions' => array('edit', 'view'),
            'class' =>'employee',
            'currentIndex' =>$this->baseLink.($paggination->limit!=20 ? '&paginator_ybc_blog_employee_select_limit='.$paggination->limit:''),
            'identifier' => 'id_employee',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => '',
            'fields_list' => $fields_list,
            'field_values' => $employees,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $filter || $having ? true : false,
            'show_add_new' => false,
            'sort' => $sort ? $sort_post:'',
            'sort_type' => $sort ? $sort_type:'',

        );
        return $this->module->renderList($listData);
    }
    private function _postEmployee()
    {
        $errors=array();
        if(Tools::isSubmit('deleteAllPostEmployee') && ($id_author=(int)Tools::getValue('id_author')) )
        {
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_author,
            ));
            if(Ybc_blog_post_class::deleteAllPostByIdAuthor($id_author,false))
            {
                Tools::redirectAdmin($this->baseLink.'&conf=1');
            }
        }
        if(Tools::isSubmit('delemployeeimage') && ($id_employee = (int)Tools::getValue('id_employee')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_employee,
            ));
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata))
                @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata);
            $employeePost->avata='';
            $employeePost->update();
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => 'success',
                        'message' => $this->module->displayConfirmation($this->l('Avatar image deleted')),
                    )
                ));
            }
            Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_blog_employee=1&id_employee='.(int)$id_employee);
        }
        if(Tools::isSubmit('change_enabled'))
        {
            $status=(int)Tools::getValue('change_enabled');
            $field = Tools::getValue('field');
            $id_employee = (int)Tools::getValue('id_employee');
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_employee,
            ));
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            if(($field == 'status' && $id_employee))
            {
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                    $employeePost->status=$status;
                    $employeePost->update();
                }
                else
                {
                    $employeePost = new Ybc_blog_post_employee_class();
                    $employeePost->status=$status;
                    $employee = new Employee($id_employee);
                    $employeePost->id_employee = $id_employee;
                    $employeePost->name = $employee->firstname.' '.$employee->lastname;
                    $employeePost->add();
                }
                if($status==1)
                    $title= $this->l('Click to suspend');
                else
                    $title= $this->l('Click to active');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_employee,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->module->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->baseLink.'&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_employee='.$id_employee,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4');
            }
        }
        if(Tools::isSubmit('saveBlogEmployee') && ($id_employee = (int)Tools::getValue('id_employee')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_employee,
            ));
            if($id_employee_post)
            {
                $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
            }
            else
                $employeePost = new Ybc_blog_post_employee_class();
            $employeePost->id_employee=$id_employee;
            $employeePost->is_customer=0;
            $name = Tools::getValue('name');
            if(!$name)
            {
                $errors[]=$this->l('Name is required');
            }
            elseif(!Validate::isCleanHtml($name))
                $errors[]=$this->l('Name is not valid');
            else
                $employeePost->name = $name;
            $description_default = Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT'));
            if($description_default && !Validate::isCleanHtml($description_default))
                $errors[] = $this->l('Introduction is not valid');
            $profile_employee = Tools::getValue('profile_employee');
            if($profile_employee && !Ybc_blog::validateArray($profile_employee))
                $errors[] = $this->l('Profile is not valid');
            else
                $employeePost->profile_employee = $profile_employee ? implode(',',$profile_employee):'';
            $employeePost->status = (int)Tools::getValue('status');
            $languages= Language::getLanguages(false);
            if(!$errors)
            {
                foreach($languages as $language)
                {
                    $description  = Tools::getValue('description_'.$language['id_lang']);
                    if($description && !Validate::isCleanHtml($description))
                        $errors[] = sprintf($this->l('Introduction in %s not valid'),$language['name']);
                    else
                        $employeePost->description[$language['id_lang']] = $description ? : $description_default;
                }
            }
            $oldImage = false;
            $newImage = false;
            $changedImages=array();
            if(isset($_FILES['avata']['tmp_name']) && isset($_FILES['avata']['name']) && $_FILES['avata']['name'])
            {
                $_FILES['avata']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['avata']['name']);
                if(!Validate::isFileName($_FILES['avata']['name']))
                {
                    $errors[] = $this->l('Avatar is invalid');
                }
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$_FILES['avata']['name']))
                    {
                        $file_name = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'avata/',$_FILES['avata']['name']);
                    }
                    else
                        $file_name = $_FILES['avata']['name'];
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['avata']['name'], '.'), 1));
                    $imagesize = @getimagesize($_FILES['avata']['tmp_name']);
                    if (isset($_FILES['avata']) &&
                        !empty($_FILES['avata']['tmp_name']) &&
                        !empty($imagesize) &&
                        in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                    )
                    {
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                        if($_FILES['avata']['size'] > $max_file_size*1024*1024)
                            $errors[] = sprintf($this->l('Avatar image file is too large. Limit: %sMb'),$max_file_size);
                        elseif (!$temp_name || !move_uploaded_file($_FILES['avata']['tmp_name'], $temp_name))
                            $errors[] = $this->l('Cannot upload the file');
                        elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'avata/'.$file_name, Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',null,null,null,300), Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',null,null,null,300), $type))
                            $errors[] = $this->l('An error occurred during the image upload process.');
                        if (isset($temp_name) && file_exists($temp_name))
                            @unlink($temp_name);
                        if($employeePost->avata)
                            $oldImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                        $employeePost->avata = $file_name;
                        $newImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                    }
                    else
                        $errors[] = $this->l('Avatar is invalid');
                }


            }
            if(!$errors)
            {
                if($id_employee_post)
                {
                    if(!$employeePost->update())
                        $errors[] = $this->l('The employee could not be updated.');
                }
                else
                    if(!$employeePost->add())
                        $errors[] = $this->l('The employee could not be updated.');
            }
            if (count($errors))
            {
                if($newImage && file_exists($newImage))
                    @unlink($newImage);
                $this->module->errorMessage = $this->module->displayError($errors);
            }
            elseif($oldImage && file_exists($oldImage))
                @unlink($oldImage);
            if(isset($newImage) && $newImage && file_exists($newImage) && !$errors && isset($employeePost)){
                $changedImages[] = array(
                    'name' => 'avata',
                    'url' => _PS_YBC_BLOG_IMG_.'avata/'.$employeePost->avata,
                    'delete_url' => $this->baseLink.'&id_employee='.$id_employee.'&delemployeeimage=true&control=employees',
                );
            }
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => $errors ? 'error' : 'success',
                        'message' => $errors ? $this->module->errorMessage :  $this->module->displaySuccessMessage($this->l('Administrator - Author has been saved')),
                        'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                        'postUrl' => !$errors && Tools::isSubmit('saveBlogEmployee') && $id_employee ? $this->baseLink.'&id_employee='.$id_employee : 0,
                        'itemKey' => 'id_employee',
                        'itemId' => !$errors ? $id_employee:0,
                    )
                ));
            }
            if(!$errors)
            {
                if (Tools::isSubmit('saveBlogEmployee') && Tools::isSubmit('id_employee'))
                    Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_blog_employee=1&id_employee='.$id_employee);
            }
        }
    }
    public function renderEmployeeFrom($id_employee)
    {
        $employee_class= new Employee($id_employee);
        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'name',
                        'required' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Introduction'),
                        'name' => 'description',
                        'lang'=>true,
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Avatar photo'),
                        'name' => 'avata',
                        'col' => 9,
                        'desc'=> sprintf($this->l('Avatar photo should be a square image. Accepted formats: jpg, jpeg, png, gif. Limit: %dMb. Recommended size: '),Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')).Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300).'x'.Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300),
                    ),
                    array(
                        'type'=>'select',
                        'label'=>$this->l('Status'),
                        'name'=>'status',
                        'form_group_class'=> 'status'.($employee_class->id_profile==1?' hide':''),
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Activated')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('Suspended')
                                ),
                                array(
                                    'id_option' => -1,
                                    'name' => $this->l('Suspended and hide posts')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'profile_employee',
                        'label' => $this->l('Accessible tabs'),
                        'form_group_class'=> 'profile'.($employee_class->id_profile==1?' hide':''),
                        'profiles' => array(
                            array(
                                'title'=>$this->l('All tabs'),
                                'id'=>'All tabs'
                            ),
                            array(
                                'title'=>$this->l('Blog posts and blog categories'),
                                'id'=>'Blog posts and blog categories'
                            ),
                            array(
                                'title'=>$this->l('Blog comments'),
                                'id'=>'Blog comments'
                            ),
                            array(
                                'title'=>$this->l('Blog slider'),
                                'id'=>'Blog slider'
                            ),
                            array(
                                'title'=>$this->l('Blog gallery'),
                                'id'=>'Blog gallery'
                            ),
                            array(
                                'title'=>$this->l('RSS feed'),
                                'id'=>'Rss feed'
                            ),
                            array(
                                'title'=>$this->l('SEO'),
                                'id'=>'Seo'
                            ),
                            array(
                                'title'=>$this->l('Socials'),
                                'id'=>'Socials'
                            ),
                            array(
                                'title'=>$this->l('Sitemap'),
                                'id'=>'Sitemap'
                            ),
                            array(
                                'title'=>$this->l('Email'),
                                'id'=>'Email'
                            ),
                            array(
                                'title'=>$this->l('Image'),
                                'id'=>'Image'
                            ),
                            array(
                                'title'=>$this->l('Sidebar'),
                                'id'=>'Sidebar'
                            ),
                            array(
                                'title'=>$this->l('Home page'),
                                'id'=>'Home page'
                            ),
                            array(
                                'title'=>$this->l('Post detail page'),
                                'id'=>'Post detail page'
                            ),
                            array(
                                'title'=>$this->l('Post listing pages'),
                                'id'=>'Post listing pages'
                            ),
                            array(
                                'title'=>$this->l('Category page'),
                                'id'=>'Category page'
                            ),
                            array(
                                'title'=>$this->l('Product detail page'),
                                'id'=>'Product detail page'
                            ),
                            array(
                                'title'=>$this->l('Authors'),
                                'id'=>'Authors'
                            ),
                            array(
                                'title'=>$this->l('Import/Export'),
                                'id'=>'Import/Export'
                            ),
                            array(
                                'title'=>$this->l('Statistics'),
                                'id'=>'Statistics'
                            ),
                            array(
                                'title'=>$this->l('Global settings'),
                                'id'=>'Global settings'
                            ),
                        ),
                        'name' => 'profile_employee',
                        'selected_profile' => $this->getProfileEmployee($employee_class->id)
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
        $helper->identifier = 'id_employee';
        $helper->submit_action = 'saveBlogEmployee';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogAuthor', false);
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogAuthor');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getFieldsEmployeeValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'post_key' => 'id_employee',
            'cancel_url' => $this->baseLink,
            'name_controller' => 'ybc-blog-panel-employee',
        );
        if($id_employee)
        {

            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_employee');
            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false)) && ($blog_employee = new Ybc_blog_post_employee_class($id)) && $blog_employee->avata)
            {
                $helper->tpl_vars['display_img'] = _PS_YBC_BLOG_IMG_.'avata/'.$blog_employee->avata;
                $helper->tpl_vars['img_del_link'] = $this->baseLink.'&id_employee='.$id_employee.'&delemployeeimage=true';
            }
        }
        return $helper->generateForm(array($fields_form));
    }
    private function displayTabAuthor()
    {
        $filter = "";
        $having="";
        $control = Tools::getValue('control','employees');
        if($control=='employees' && !Tools::isSubmit('editybc_blog_employee'))
        {
            if(($id_employee = trim(Tools::getValue('id_employee')))!='' && Validate::isCleanHtml($id_employee))
                $filter .= " AND e.id_employee = ".(int)$id_employee;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(e.firstname,' ',e.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";
            if(($email = trim(Tools::getValue('email'))) && Validate::isCleanHtml($email))
                $filter .= " AND e.email like '%".pSQL($email)."%'";
            if(($desc = trim(Tools::getValue('description'))) && Validate::isCleanHtml($desc))
                $filter .= " AND bel.description like '%".pSQL($desc)."%'";
            if(($id_profile = trim(Tools::getValue('id_profile'))) && Validate::isCleanHtml($id_profile))
                $filter .= " AND pl.id_profile = '".(int)$id_profile."'";
            if(($profile_employee = trim(Tools::getValue('profile_employee')))!='' && Validate::isCleanHtml($profile_employee))
                $filter .= " AND (be.profile_employee like '".pSQL($profile_employee)."' OR p.id_profile=1)  ";
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"';
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
        }
        $totalEmployee = (int)Ybc_blog_post_employee_class::countEmployeesFilter($filter,$having);
        $filter = "";
        $having="";
        if($control=='customer' && !Tools::isSubmit('editybc_blog_customer'))
        {
            if(($id_customer = trim(Tools::getValue('id_customer')))!='' && Validate::isCleanHtml($id_customer))
                $filter .= " AND c.id_customer = ".(int)$id_customer;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";
            if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                $filter .= " AND c.email like '".pSQL($email)."%'";
            if(($description = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($description) )
                $filter .= ' AND bel.description like "%'.pSQL($description).'%"';
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"';
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
        }
        $has_post = Tools::getValue('has_post');
        if(Tools::isSubmit('has_post') && $has_post==0)
            $having .= ' AND total_post <=0';
        else
            $having .= ' AND total_post >=1';
        $totalCustomer = (int)Ybc_blog_post_employee_class::countCustomersFilter($filter,$having);
        $this->context->smarty->assign(
            array(
                'totalCustomer' => $totalCustomer,
                'totalEmployee' => $totalEmployee,
                'control' => $control,
                'YBC_BLOG_ALLOW_CUSTOMER_AUTHOR' => Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'),
            )
        );
        return $this->module->display($this->module->getLocalPath(),'tab_author.tpl');
    }
    private function getProfileEmployee($id_employee)
    {
        if(($id = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false)) && ($employeePost = new Ybc_blog_post_employee_class($id)) && Validate::isLoadedObject($employeePost))
            return explode(',',$employeePost->profile_employee);
        return array();
    }
    private function getFieldsEmployeeValues()
    {
        $fields=array();
        $id_employee = (int)Tools::getValue('id_employee');
        if($id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_employee,false))
        {
            $blogEmployee = new Ybc_blog_post_employee_class($id_employee_post);
            $fields['status'] = $blogEmployee->status;
        }
        else
        {
            $blogEmployee = new Ybc_blog_post_employee_class();
            $fields['status'] = 1;
        }
        $employee = new Employee($id_employee);
        $fields['id_employee'] = $employee->id;
        $fields['name'] = Tools::getValue('name',$blogEmployee->name? $blogEmployee->name:$employee->firstname.' '.$employee->lastname);
        $languages= Language::getLanguages(false);
        $fields['profile_employee'] = Tools::getValue('profile_employee',$blogEmployee->profile_employee ? explode(',',$blogEmployee->profile_employee):array());
        foreach($languages as $language)
        {
            $fields['description'][$language['id_lang']] = Tools::getValue('description_'.$language['id_lang'],isset($blogEmployee->description[$language['id_lang']]) ? $blogEmployee->description[$language['id_lang']] :'');
        }
        $fields['control'] =trim(Tools::getValue('control')) ? : '';

        return $fields;
    }
    private function renderCustomer()
    {
        if(!Configuration::get('YBC_BLOG_ALLOW_CUSTOMER_AUTHOR'))
            return false;
        if(Tools::isSubmit('editybc_blog_customer') && ($id_customer=(int)Tools::getValue('id_customer')))
            return $this->renderCustomerForm($id_customer);
        else
            return $this->renderListCustomer();
    }
    private function renderListCustomer(){
        $fields_list = array(
            'id_customer' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'avata' => array(
                'title' => $this->l('Avatar'),
                'type' => 'text',
                'strip_tag' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'sort' => true,
                'strip_tag' => false,
                'filter' => true

            ),
            'email' => array(
                'title' => $this->l('Email'),
                'type' => 'text',
                'sort' => true,
                'filter' => true
            ),
            'description' => array(
                'title' => $this->l('Introduction'),
                'type' => 'text',
                'filter'=>true
            ),
            'has_post'=> array(
                'title' => $this->l('Have posts'),
                'type' => 'active',
                'filter'=>true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => '',
                            'title' => '--'
                        ),
                        1 => array(
                            'enabled' => 1,
                            'title' => $this->l('Yes')
                        ),
                        2 => array(
                            'enabled' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            ),
            'total_post'=> array(
                'title' => $this->l('Total posts'),
                'sort' => true,
                'type' => 'int',
                'filter'=>true,
            ),
            'status' => array(
                'title'=> $this->l('Status'),
                'type' => 'active',
                'filter'=>true,
                'sort' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'enabled',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'enabled' => 1,
                            'title' => $this->l('Activated')
                        ),
                        1 => array(
                            'enabled' => 0,
                            'title' => $this->l('Suspended')
                        ),
                        2 => array(
                            'enabled' => -1,
                            'title' => $this->l('Suspended and hide posts')
                        )
                    )
                )
            )
        );
        //Filter
        $filter = "";
        $sort = "";
        $having='';
        if(Tools::isSubmit('post_filter') && ($control = Tools::getValue('control')) && $control=='customer')
        {
            if(($id = trim(Tools::getValue('id_customer')))!='' && Validate::isCleanHtml($id))
                $filter .= " AND c.id_customer = ".(int)$id;
            if(($name = trim(Tools::getValue('name')))!='' && Validate::isCleanHtml($name))
                $filter .= " AND (CONCAT(c.firstname,' ',c.lastname) like '".pSQL($name)."%' OR be.name like'".pSQL($name)."%')";
            if(($email = trim(Tools::getValue('email')))!='' && Validate::isCleanHtml($email))
                $filter .= " AND c.email like '".pSQL($email)."%'";
            if(($desc = trim(Tools::getValue('description')))!='' && Validate::isCleanHtml($desc))
                $filter .= ' AND bel.description like "%'.pSQL($desc).'%"';
            if(($total_post_min = trim(Tools::getValue('total_post_min')))!='' && Validate::isCleanHtml($total_post_min))
                $having .= ' AND total_post >="'.(int)$total_post_min.'"';
            if(($total_post_max = trim(Tools::getValue('total_post_max')))!='' && Validate::isCleanHtml($total_post_max))
                $having .= ' AND total_post <="'.(int)$total_post_max.'"';
            if(Tools::isSubmit('status') && ($status = trim(Tools::getValue('status')))!='' && Validate::isCleanHtml($status))
                $filter .= " AND (be.status= '".(int)$status."'".((int)$status==1 ? ' or be.status is null':'' )." )";
            //Sort
            $sort_post  = Tools::strtolower(Tools::getValue('sort'));
            $sort_type = Tools::strtolower(Tools::getValue('sort_type','desc'));
            if(!in_array($sort_type,array('desc','asc')))
                $sort_type ='desc';
            if($sort_post && isset($fields_list[$sort_post]))
            {
                $sort .= $sort_post." ".($sort_type=='asc' ? ' ASC ' :' DESC ')."";
            }
            else
                $sort = false;
        }
        $has_post = Tools::getValue('has_post');
        if(!Tools::isSubmit('has_post') || $has_post==1)
            $having .= ' AND total_post >=1';
        elseif(Tools::isSubmit('has_post') && $has_post!='')
            $having .= ' AND total_post <=0';
        $page = (int)Tools::getValue('page');
        if($page <1)
            $page=1;
        $totalRecords = (int)Ybc_blog_post_employee_class::countCustomersFilter($filter,$having);
        $paggination = new Ybc_blog_paggination_class();
        $paggination->total = $totalRecords;
        $paggination->url = $this->baseLink.'&control=customer&page=_page_'.$this->module->getUrlExtra($fields_list);
        $paggination->limit =  (int)Tools::getValue('paginator_ybc_blog_customer_select_limit',20);
        $paggination->name ='ybc_blog_customer';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $customers = Ybc_blog_post_employee_class::getCustomersFilter($filter, $sort, $start, $paggination->limit,$having);
        if($customers)
        {
            foreach($customers as &$customer)
            {
                if(!$customer['name'])
                    $customer['name']=$customer['firstname'].' '.$customer['lastname'];
                if($customer['avata'])
                    $customer['avata'] = $this->module->displayText($this->module->displayText('','img',null,null,null,false,_PS_YBC_BLOG_IMG_.'avata/'.$customer['avata']),'div','avata_img');
                else
                    $customer['avata']= $this->module->displayText($this->module->displayText('','img',null,null,null,false,_PS_YBC_BLOG_IMG_.'avata/default_customer.png'),'div','avata_img');
                $customer['view_post_url'] = $this->module->getLink('blog',array('id_author'=> $customer['id_customer'],'is_customer'=>1,'alias'=> Tools::link_rewrite($customer['name'],true)));
                $customer['delete_post_url'] = $this->baseLink.'&control=customer&deleteAllPostCustomer&id_author='.(int)$customer['id_customer'];
                if($customer['total_post']==0)
                    $customer['has_post']=0;
                else
                    $customer['has_post']=1;
                $customer['name'] = $this->module->displayText($customer['name'],'a',null,null,$this->context->link->getAdminLink('AdminCustomers').'&updatecustomer&id_customer='.(int)$customer['id_customer']);
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_blog_customer',
            'class' =>'customer',
            'actions' => array('edit', 'view'),
            'currentIndex' => $this->baseLink.'&control=customer'.($paggination->limit!=20 ? '&paginator_billing_select_limit='.$paggination->limit:''),
            'identifier' => 'id_customer',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => '',
            'fields_list' => $fields_list,
            'field_values' => $customers,
            'paggination' => $paggination->render(),
            'filter_params' => $this->module->getFilterParams($fields_list),
            'show_reset' => $filter || Tools::isSubmit('total_post_min') || Tools::isSubmit('total_post_max') || Tools::isSubmit('has_post') ? true : false,
            'show_add_new' => false,
            'sort' => $sort ? $sort_post:'',
            'sort_type' => $sort ? $sort_type:'',
        );
        return $this->module->renderList($listData);
    }
    public function renderCustomerForm($id_customer)
    {
        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'name',
                        'required' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Introduction'),
                        'name' => 'description',
                        'lang'=>true,
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Avatar photo'),
                        'name' => 'avata',
                        'col'=>9,
                        'desc'=> $this->l('Avatar photo should be a square image. Recommended size: ').Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',300).'x'.Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',300),
                    ),
                    array(
                        'type'=>'select',
                        'label'=>$this->l('Status'),
                        'name'=>'status',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Activated')
                                ),
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('Suspended')
                                ),
                                array(
                                    'id_option' => -1,
                                    'name' => $this->l('Suspended and hide posts')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
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
        $helper->identifier = 'id_customer';
        $helper->submit_action = 'saveBlogEmployee';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogAuthor', false).'&control=customer&id_customer='.(int)$id_customer;
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogAuthor');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getFieldsCustomerValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'link' => $this->context->link,
            'post_key' => 'id_customer',
            'cancel_url' => $this->baseLink.'&control=customer',
            'name_controller' => 'ybc-blog-panel-customer',
        );
        if($id_customer)
        {

            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_customer');
            if(($id = Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer)) && ($blog_employee = new Ybc_blog_post_employee_class($id)) && $blog_employee->avata)
            {
                $helper->tpl_vars['display_img'] = _PS_YBC_BLOG_IMG_.'avata/'.$blog_employee->avata;
                $helper->tpl_vars['img_del_link'] = $this->baseLink.'&id_customer='.$id_customer.'&delemployeeimage=true&control=customer';
            }
        }
        return $helper->generateForm(array($fields_form));
    }
    public function getFieldsCustomerValues()
    {
        $fields=array();
        $id_customer = (int)Tools::getValue('id_customer');
        if($id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer))
        {
            $blogEmployee = new Ybc_blog_post_employee_class($id_employee_post);
            $fields['status'] =(int)$blogEmployee->status;
        }
        else
        {
            $blogEmployee = new Ybc_blog_post_employee_class();
            $fields['status'] = 1;
        }

        $customer = new Customer($id_customer);
        $fields['id_customer'] = $customer->id;
        $fields['name'] =$blogEmployee->name?$blogEmployee->name:$customer->firstname.' '.$customer->lastname;
        $languages= Language::getLanguages(false);
        foreach($languages as $language)
        {
            $fields['description'][$language['id_lang']] = $blogEmployee->description && isset($blogEmployee->description[$language['id_lang']]) && $blogEmployee->description[$language['id_lang']] ? $blogEmployee->description[$language['id_lang']] :'';
        }
        $fields['control'] =trim(Tools::getValue('control')) ? : '';
        return $fields;
    }
    private function _postCustomer()
    {
        $errors=array();
        if(Tools::isSubmit('deleteAllPostCustomer') && ($id_author = (int)Tools::getValue('id_author')))
        {
            Hook::exec('actionUpdateBlog', array(
                'id_author' => (int)$id_author,
            ));
            if(Ybc_blog_post_class::deleteAllPostCustomerByIdAuthor($id_author))
            {
                Tools::redirectAdmin($this->baseLink.'&conf=1&control=customer');
            }
        }
        if(Tools::isSubmit('delemployeeimage') && ($id_customer = (int)Tools::getValue('id_customer')))
        {
            $id_employee_post= Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_customer,
            ));
            $employeePost = new Ybc_blog_post_employee_class($id_employee_post);
            $employeePost->avata='';
            if($employeePost->update())
            {
                if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata))
                    @unlink(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata);
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(
                        array(
                            'messageType' => 'success',
                            'message' => $this->module->displayConfirmation($this->l('Avatar image deleted')),
                        )
                    ));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4&control=customer&editybc_blog_customer=1&id_customer='.(int)$id_customer);
            }
        }
        if(Tools::isSubmit('change_enabled'))
        {
            $status = (int)Tools::getValue('change_enabled');
            $field = Tools::getValue('field');
            $id_customer = (int)Tools::getValue('id_customer');
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_customer,
            ));
            $id_employee_post = (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            if(($field == 'status' && $id_customer))
            {
                if($id_employee_post)
                {
                    $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
                    $employeePost->status=$status;
                    $employeePost->update();
                }
                else
                {
                    $employeePost = new Ybc_blog_post_employee_class();
                    $employeePost->status=$status;
                    $customer = new Customer($id_customer);
                    $employeePost->id_employee = $id_customer;
                    $employeePost->is_customer=1;
                    $employeePost->name = $customer->firstname.' '.$customer->lastname;
                    $employeePost->add();
                }
                if($status==1)
                    $title= $this->l('Click to suspend');
                else
                    $title= $this->l('Click to active');
                if(Tools::isSubmit('ajax'))
                {
                    die(json_encode(array(
                        'listId' => $id_customer,
                        'enabled' => $status,
                        'field' => $field,
                        'message' => $this->module->displaySuccessMessage($this->l('The status has been successfully updated')),
                        'messageType'=>'success',
                        'title'=>$title,
                        'href' => $this->baseLink.'&control=customer&change_enabled='.($status ? '0' : '1').'&field='.$field.'&id_customer='.$id_customer,
                    )));
                }
                Tools::redirectAdmin($this->baseLink.'&conf=4&control=customer');
            }
        }
        if(Tools::isSubmit('saveBlogEmployee') && ($id_customer = (int)Tools::getValue('id_customer')))
        {
            $id_employee_post= (int)Ybc_blog_post_employee_class::getIdEmployeePostById($id_customer);
            Hook::exec('actionUpdateBlog', array(
                'id_author' =>(int)$id_customer,
            ));
            if($id_employee_post)
            {
                $employeePost= new Ybc_blog_post_employee_class($id_employee_post);
            }
            else
                $employeePost = new Ybc_blog_post_employee_class();
            $employeePost->id_employee= $id_customer;
            $employeePost->is_customer=1;
            $employeePost->status = (int)Tools::getValue('status');
            $name = Tools::getValue('name');
            if(!$name)
            {
                $errors[]=$this->l('Name is required');
            }
            elseif(!Validate::isCleanHtml($name))
                $errors[]=$this->l('Name is not valid');
            else
                $employeePost->name = $name;
            $description_default = Tools::getValue('description_'.Configuration::get('PS_LANG_DEFAULT'));
            if($description_default && !Validate::isCleanHtml($description_default))
                $errors[] = $this->l('Description is not valid');
            $employeePost->profile_employee = '';
            $languages= Language::getLanguages(false);
            if(!$errors)
            {
                foreach($languages as $language)
                {
                    $description = Tools::getValue('description_'.$language['id_lang']);
                    if($description && !Validate::isCleanHtml($description,true))
                        $errors[] = sprintf($this->l('Description in %s is not valid'),$language['name']);
                    $employeePost->description[$language['id_lang']] = $description ? : $description_default;
                }
            }
            $oldImage = false;
            $newImage = false;
            $changedImages=array();
            if(isset($_FILES['avata']['tmp_name']) && isset($_FILES['avata']['name']) && $_FILES['avata']['name'])
            {
                $_FILES['avata']['name'] = str_replace(array(' ','(',')','!','@','#','+'),'-',$_FILES['avata']['name']);
                if(!Validate::isFileName($_FILES['avata']['name']))
                {
                    $errors[] = $this->l('Avatar is invalid');
                }
                else
                {
                    if(file_exists(_PS_YBC_BLOG_IMG_DIR_.'avata/'.$_FILES['avata']['name']))
                    {
                        $file_name = $this->module->createNewFileName(_PS_YBC_BLOG_IMG_DIR_.'avata/',$_FILES['avata']['name']);
                    }
                    else
                        $file_name = $_FILES['avata']['name'];
                    $type = Tools::strtolower(Tools::substr(strrchr($_FILES['avata']['name'], '.'), 1));
                    $imagesize = @getimagesize($_FILES['avata']['tmp_name']);
                    if (isset($_FILES['avata']) &&
                        !empty($_FILES['avata']['tmp_name']) &&
                        !empty($imagesize) &&
                        in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                    )
                    {
                        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE');
                        if($_FILES['avata']['size'] > $max_file_size*1024*1024)
                            $errors[] = sprintf($this->l('Avatar image file is too large. Limit: %sMb'),$max_file_size);
                        elseif (!$temp_name || !move_uploaded_file($_FILES['avata']['tmp_name'], $temp_name))
                            $errors[] = $this->l('Cannot upload the file');
                        elseif (!ImageManager::resize($temp_name, _PS_YBC_BLOG_IMG_DIR_.'avata/'.$file_name, null, null, $type))
                            $errors[] = $this->l('An error occurred during the image upload process.');
                        if (isset($temp_name) && file_exists($temp_name))
                            @unlink($temp_name);
                        if($employeePost->avata)
                            $oldImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                        $employeePost->avata = $file_name;
                        $newImage = _PS_YBC_BLOG_IMG_DIR_.'avata/'.$employeePost->avata;
                    }
                    else
                        $errors[] = $this->l('Avatar is invalid');
                }
            }
            if(!$errors)
            {
                if($id_employee_post)
                {
                    if(!$employeePost->update())
                        $errors[] = $this->l('The employee could not be updated.');
                }
                else
                    if(!$employeePost->add())
                        $errors[] = $this->l('The employee could not be updated.');

            }
            if (count($errors))
            {
                if($newImage && file_exists($newImage))
                    @unlink($newImage);
                $this->module->errorMessage = $this->module->displayError($errors);
            }
            elseif($oldImage && file_exists($oldImage))
                @unlink($oldImage);
            if(isset($newImage) && $newImage && file_exists($newImage) && !$errors && isset($employeePost)){
                $changedImages[] = array(
                    'name' => 'avata',
                    'url' => _PS_YBC_BLOG_IMG_.'avata/'.$employeePost->avata,
                    'delete_url' => $this->baseLink.'&id_customer='.$id_customer.'&delemployeeimage=true&control=customer',
                );
            }
            if(Tools::isSubmit('ajax'))
            {
                die(json_encode(
                    array(
                        'messageType' => $errors ? 'error' : 'success',
                        'message' => $errors ? $this->module->errorMessage :  $this->module->displaySuccessMessage($this->l('Customer - Author has been saved')),
                        'images' => isset($changedImages) && $changedImages ? $changedImages : array(),
                        'postUrl' => !$errors && Tools::isSubmit('saveBlogEmployee') && (int)$id_customer ? $this->baseLink.'&id_customer='.(int)$id_customer.'&control=customer' : 0,
                        'itemKey' => 'id_employee',
                        'itemId' => !$errors ? $id_customer:0,
                    )
                ));
            }
            if(!$errors)
            {
                if (Tools::isSubmit('saveBlogEmployee') && Tools::isSubmit('id_customer'))
                    Tools::redirectAdmin($this->baseLink.'&conf=4&editybc_blog_customer=1&id_customer='.$id_customer.'&control=customer');
            }
        }
    }
    public function renderSettingCustomer()
    {
        $configs = Ybc_blog_defines::getInstance()->getCustomerSettings();
        $fields_form = array(
            'form' => array(
                'input' => array(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        if($configs)
        {
            foreach($configs as $key => $config)
            {
                $arg = array(
                    'name' => isset($config['multiple']) && $config['multiple']? $key.'[]' :$key,
                    'type' => $config['type'],
                    'label' => $config['label'],
                    'desc' => isset($config['desc']) ? $config['desc'] : false,
                    'required' => isset($config['required']) && $config['required'] ? true : false,
                    'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
                    'values' => isset($config['values']) ? $config['values']:false,
                    'multiple' => isset($config['multiple'])? $config['multiple'] : false,
                    'lang' => isset($config['lang']) ? $config['lang'] : false,
                    'class' => isset($config['class']) ? $config['class'] : '',
                    'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
                    'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
                    'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
                    'html_content' => isset($config['html_content']) ? $this->module->displayBlogCategoryTre(Ybc_blog_category_class::getBlogCategoriesTree(0),Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER') ? explode(',',Configuration::get('YBC_BLOG_CATEGOGY_CUSTOMER')):array(),$key) : false,
                    'selected_categories' => isset($config['selected_categories']) ? $config['selected_categories'] : false,
                    'categories' => isset($config['categories'])? Ybc_blog_category_class::getBlogCategoriesTree(0) :false,
                );
                if(isset($arg['suffix']) && !$arg['suffix'])
                    unset($arg['suffix']);
                $fields_form['form']['input'][] = $arg;
            }
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'module';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this->module;
        $helper->identifier = 'id_module';
        $helper->submit_action = 'saveCustomerAuthor';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminYbcBlogAuthor', false).'&control=author';
        $helper->token = Tools::getAdminTokenLite('AdminYbcBlogAuthor');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $fields = array();
        $languages = Language::getLanguages(false);
        if(Tools::isSubmit('saveCustomerAuthor'))
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Tools::getValue($key.'_'.$l['id_lang'],isset($config['default']) ? $config['default'] : '');
                        }
                    }
                    else
                        $fields[$key] = Tools::getValue($key,isset($config['default']) ? $config['default'] : '');
                }
            }
        }
        else
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        foreach($languages as $l)
                        {
                            $fields[$key][$l['id_lang']] = Configuration::get($key,$l['id_lang']);
                        }
                    }
                    elseif($config['type']=='checkbox')
                    {
                        $fields[$key] =Configuration::get($key)? explode(',',Configuration::get($key)):array();
                    }

                    else
                        $fields[$key] = Configuration::get($key);

                }
            }
        }
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'cancel_url' => $this->context->link->getAdminLink('AdminYbcBlogPost'),
            'isConfigForm' => true,
            'image_baseurl' => _PS_YBC_BLOG_IMG_,
            'name_controller' => 'ybc-blog-panel-settings',
        );
        return $helper->generateForm(array($fields_form));
    }
    private function _postCustomerSettingAuthor()
    {
        if(Tools::isSubmit('saveCustomerAuthor'))
        {
            if($this->_saveConfiguration('author'))
            {
                Hook::exec('actionUpdateBlog', array());
                Tools::redirectAdmin($this->baseLink.'&control=author&conf=4');
            }
        }
    }
}
