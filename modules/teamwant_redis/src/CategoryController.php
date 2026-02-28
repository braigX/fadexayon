<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class RedisCategoryController extends \CategoryController
{
    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getTemplateVarCategoryPublic()
    {
        return $this->getTemplateVarCategory();
    }

    public function getTemplateVarSubCategoriesPublic()
    {
        return $this->getTemplateVarSubCategories();
    }

    /**
     * {@inheritdoc}
     */
    public function publicDoSearch()
    {
        $categoryVar = $this->getTemplateVarCategory();

        $filteredCategory = Hook::exec(
            'filterCategoryContent',
            ['object' => $categoryVar],
            $id_module = null,
            $array_return = false,
            $check_exceptions = true,
            $use_push = false,
            $id_shop = null,
            $chain = true
        );
        if (!empty($filteredCategory['object'])) {
            $categoryVar = $filteredCategory['object'];
        }

        $this->context->smarty->assign([
            'category' => $categoryVar,
            'subcategories' => $this->getTemplateVarSubCategories(),
        ]);

        $variables = $this->getProductSearchVariables();
        $this->context->smarty->assign([
            'listing' => $variables,
        ]);
        $this->setTemplate(
            'catalog/listing/category',
            [
                'entity' => 'category',
                'id' => $this->category->id,
            ],
            null
        );
    }
}
