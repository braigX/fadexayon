<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;
use CrazyElements\Widget_Base;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_ProductSingleReassurance extends Widget_Base
{


    /**
     * Get widget name.
     *
     * Retrieve accordion widget name.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'product_single_reassurance';
    }

    /**
     * Get widget title.
     *
     * Retrieve accordion widget title.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return PrestaHelper::__('Product Reassurance', 'elementor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve accordion widget icon.
     *
     * @since  1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'ceicon-product-reassure-widget';
    }

    public function get_categories()
    {
        return array('products_layout');
    }

    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_title',
            array(
                'label' => PrestaHelper::__('General', 'elementor'),
            )
        );

        $this->add_control(
            'layout',
            array(
                'label'   => PrestaHelper::__('Select Style', 'elementor'),
                'type'    => Controls_Manager::SELECT,
                'options' => array(
                    'default'   => PrestaHelper::__('Default', 'elementor'),
                    'style_one' => PrestaHelper::__('Style One', 'elementor')
                ),
                'default' => 'default',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings     = $this->get_settings_for_display();
        $layout        = $settings['layout'];
        
        $controller_name = \Tools::getValue('controller');
        if ($controller_name == "product") {

            if($layout == 'default'){
                $this->get_default_layout();
            }else{
                $mod_ins = \Module::getInstanceByName( 'blockreassurance' );
    
                if ( ! is_object( $mod_ins ) ) {
                    return;
                }
                if(method_exists($mod_ins,'hookActionFrontControllerSetMedia')){
                    $mod_ins->hookActionFrontControllerSetMedia();
                }
                echo $mod_ins->renderWidget();
            }

            
        } else {

            if ( PrestaHelper::is_admin() ) {
                return;
            }

            if ( \Module::isInstalled( 'blockreassurance' ) && \Module::isEnabled( 'blockreassurance' ) ) {
                $mod_ins = \Module::getInstanceByName( 'blockreassurance' );
    
                if ( ! is_object( $mod_ins ) ) {
                    return;
                }
                if(method_exists($mod_ins,'hookActionFrontControllerSetMedia')){
                    $mod_ins->hookActionFrontControllerSetMedia();
                }
                $id_lang = \Context::getcontext()->language->id;
                if($layout == 'default'){
                    \Context::getcontext()->smarty->assign([
                        'blocks' => \ReassuranceActivity::getAllBlockByStatus($id_lang, \Context::getcontext()->shop->id),
                        'iconColor' => \Configuration::get('PSR_ICON_COLOR'),
                        'textColor' => \Configuration::get('PSR_TEXT_COLOR'),
                        // constants
                        'LINK_TYPE_NONE' => \ReassuranceActivity::TYPE_LINK_NONE,
                        'LINK_TYPE_CMS' => \ReassuranceActivity::TYPE_LINK_CMS_PAGE,
                        'LINK_TYPE_URL' => \ReassuranceActivity::TYPE_LINK_URL,
                    ]);
            
                    echo \Context::getcontext()->smarty->fetch('module:blockreassurance/views/templates/hook/displayBlockProduct.tpl' );
                }else{
                    echo $mod_ins->renderWidget();
                }
            }
        }
    }

    private function get_default_layout(){
        $out_put = '';
        $id_product = (int)\Tools::getValue('id_product');
        $out_put .= \Context::getcontext()->smarty->fetch(_PS_MODULE_DIR_ . 'crazyelements/views/templates/front/single-product/crazy_single_product_reassurance.tpl');
        echo $out_put;
    }

    /**
     * Render accordion widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function _content_template()
    {
    }
}