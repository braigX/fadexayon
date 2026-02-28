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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

require_once __DIR__ . '/../../classes/traits/EtsSeoTranslationTrait.php';

/**
 * Class AnalysisType
 */
class AnalysisType extends \PrestaShopBundle\Form\Admin\Type\TranslatorAwareType
{
    use \EtsSeoTranslationTrait;
    public $translator;
    public $locales;
    public function __construct(TranslatorInterface $translator = null, array $locales=[])
    {
        $this->translator = $translator;
        $this->locales = $locales;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => $this->l('Seo Analysis', __FILE__),
                'required' => false,
                'form_theme' => '@PrestaShop/Modules/EtsSeo/FormTheme/custom_form_type.html.twig',
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }
}
