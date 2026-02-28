<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class FrontController extends FrontControllerCore
{
    
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    protected $redirectionExtraExcludedKeys = ['rewrite', 'category'];
    /*
    * module: ets_seo
    * date: 2026-01-29 18:07:28
    * version: 3.1.3
    */
    protected function redirect()
    {
        if (Module::isEnabled('ets_seo')) {
            Hook::exec('actionFrontControllerRedirectBefore', ['redirect_after' => $this->redirect_after, 'controller' => $this]);
        }
        parent::redirect();
    }
}
