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
 
require_once(_PS_MODULE_DIR_.'ets_contactform7/classes/ctf_paggination_class.php');
if (!defined('_PS_VERSION_'))
    	exit;
class AdminContactFormStatisticsController extends ModuleAdminController
{
    public function __construct()
    {
       parent::__construct();
       if(($ip=Tools::getValue('addtoblacklist')) && Validate::isCleanHtml($ip))
       {
            $black_list = explode("\n",Configuration::get('ETS_CTF7_IP_BLACK_LIST'));
            $black_list[]=$ip;
            Configuration::updateValue('ETS_CTF7_IP_BLACK_LIST',implode("\n",$black_list));
            if(Tools::isSubmit('ajax_ets'))
            {
                die(
                  json_encode(
                    array(
                        'ok'=>true,
                    )
                  )  
                );
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormStatistics').'&tab_ets=view-log');
       }
       if(Tools::isSubmit('clearLogSubmit'))
       {
            if(Ets_contact_class::deleteAllLog()) {
	            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormStatistics').'&tab_ets=view-log&conf=1');
            }
       }
       $this->bootstrap = true;
    }
    public function initContent()
    {
        parent::initContent();
    }
    public function renderList()
    {
        $id_contact = (int)Tools::getValue('id_contact');
	    $year = (int)Tools::getValue('years',date('Y'));
	    $month = (int)Tools::getValue('months',date('m'));
	    $page = (int)Tools::getValue('page',1);
	    $tab_ets = Tools::getValue('tab_ets','chart');
	    if(!Validate::isCleanHtml($tab_ets))
		    $tab_ets = 'chart';
	    $ctf_month = Validate::isCleanHtml((string)Tools::getValue('months',date('m'))) ? (string)Tools::getValue('months',date('m')) :date('m');

	    $cache_params_id = [$id_contact, $year, $month, $page, $tab_ets];
	    $cache_id = $this->module->_getCacheId($cache_params_id);
	    if (!$this->module->isCached('statistics.tpl', $cache_id)) {
		    $months=Tools::dateMonths();
		    $now_year = date('Y')+2;
		    $start_year = Ets_contact_class::getStartYear($id_contact);

		    $years = array();
		    if($start_year)
		    {
			    for($i=$start_year-2;$i<=$now_year;$i++)
			    {
				    $years[]=$i;
			    }
		    }
		    $messages=array();
		    $views=array();
		    $replies =array();
		    if(!$year)
		    {
			    if($years)
			    {
				    foreach($years as $y)
				    {
					    $messages[] =array(
						    0 => $y,
						    1 => Ets_contact_class::getCountMesssage($y,'','',$id_contact),
					    );
					    $views[] =array(
						    0 => $y,
						    1 => Ets_contact_class::getCountView($y,'','',$id_contact),
					    );
					    $replies[] =array(
						    0 => $y,
						    1 => Ets_contact_class::getCountReplies($y,'','',$id_contact),
					    );
				    }
			    }
		    }
		    else
		    {
			    if(!$month){
				    if($months)
				    {
					    foreach($months as $key=> $m)
					    {
						    $messages[] =array(
							    0 => $key,
							    1 => Ets_contact_class::getCountMesssage($year,$key,'',$id_contact),
						    );
						    $views[] =array(
							    0 => $key,
							    1 => Ets_contact_class::getCountView($year,$key,'',$id_contact),
						    );
						    $replies[] =array(
							    0 => $key,
							    1 => Ets_contact_class::getCountReplies($year,$key,'',$id_contact),
						    );
					    }
				    }
			    }
			    else
			    {
				    $days = function_exists('cal_days_in_month') ? cal_days_in_month(CAL_GREGORIAN, $month, $year) : (int)date('t', mktime(0, 0, 0, $month, 1, $year));
				    if($days)
				    {
					    for($day=1; $day<=$days;$day++)
					    {
						    $messages[] =array(
							    0 => $day,
							    1 => Ets_contact_class::getCountMesssage($year,$month,$day,$id_contact),
						    );
						    $views[] =array(
							    0 => $day,
							    1 => Ets_contact_class::getCountView($year,$month,$day,$id_contact),
						    );
						    $replies[] =array(
							    0 => $day,
							    1 => Ets_contact_class::getCountReplies($year,$month,$day,$id_contact),
						    );
					    }
				    }
			    }
		    }
		    $contacts = Ets_contact_class::getContacts();
		    $lineChart =array(
			    array(
				    'key'=> $this->l('Messages'),
				    'values'=>$messages,
				    'disables'=>1,
			    ),
			    array(
				    'key'=> $this->l('Views'),
				    'values'=>$views,
				    'disables'=>1,
			    ),
			    array(
				    'key'=> $this->l('Replies'),
				    'values'=>$replies,
				    'disables'=>1,
			    ),
		    );
		    $total = Ets_contact_class::getCountLog();
		    $limit=20;
		    if($page<=0)
			    $page=1;
		    $start= ($page-1)*$limit;
		    $pagination = new Ctf_paggination_class();
		    $pagination->url = $this->context->link->getAdminLink('AdminContactFormStatistics').'&tab_ets=view-log&page=_page_';
		    $pagination->limit=$limit;
		    $pagination->page= $page;
		    $pagination->total=$total;
		    $logs = Ets_contact_class::getLogs($start,$limit);
		    if($logs)
		    {
			    $black_list = explode("\n",Configuration::get('ETS_CTF7_IP_BLACK_LIST'));
			    foreach($logs as &$log)
			    {
				    if(in_array($log['ip'],$black_list))
					    $log['black_list']=true;
				    else
					    $log['black_list']=false;
				    $browser = explode(' ',$log['browser']);
				    if(isset($browser[0]))
					    $log['class'] = Tools::strtolower($browser[0]);
				    else
					    $log['class']='default';
			    }
		    }
		    $this->context->smarty->assign(
			    array(
				    'months' => $months,
				    'ctf_month' => $ctf_month,
				    'action'=> $this->context->link->getAdminLink('AdminContactFormStatistics'),
				    'contacts' => $contacts,
				    'years'=>$years,
				    'ctf_year' => $year,
				    'lineChart' => $lineChart,
				    'ctf_contact' => (int)$id_contact,
				    'js_dir_path' => $this->module->_path_module.'views/js/',
				    'logs'=>$logs,
				    'tab_ets' => $tab_ets,
				    'pagination_text' => $pagination->render(),
				    'show_reset' => Tools::isSubmit('submitFilterChart'),
			    )
		    );
	    }
        return  $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'statistics.tpl', $cache_id);
    }
}