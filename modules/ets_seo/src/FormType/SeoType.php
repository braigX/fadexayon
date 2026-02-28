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

namespace Ets\Seo\FormType;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

require_once __DIR__ . '/../../classes/traits/EtsSeoTranslationTrait.php';
require_once __DIR__ . '/SeoSocialType.php';
require_once __DIR__ . '/SeoAdvanceType.php';

/**
 * Class SeoType
 */
class SeoType extends \PrestaShopBundle\Form\Admin\Sell\Product\SEO\SEOType
{
    use \EtsSeoTranslationTrait;

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => $this->l('Seo Settings', __FILE__),
                'label_tab' => $this->trans('Search engine optimization', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('Improve your ranking and how your product page will appear in search engines results.', 'Admin.Catalog.Feature'),
                'required' => false,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product_seo.html.twig',
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('seo_social', SeoSocialType::class)
            ->add('seo_advance', SeoAdvanceType::class);
    }
}
