<?php

declare(strict_types=1);

namespace Apm\Catalog\Plugin\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Category;

class CategoryPlugin
{
    public function aroundGetChildrenCategories(Category $subject, callable $proceed, $category)
    {
        $collection = $category->getCollection();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection->addAttributeToSelect(
            'url_key'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'all_children'
        )->addAttributeToSelect(
            'is_anchor'
        )->addAttributeToSelect(
            'image'
        )->addAttributeToFilter(
            'is_active',
            1
        )->addIdFilter(
            $category->getChildren()
        )->setOrder(
            'position',
            \Magento\Framework\DB\Select::SQL_ASC
        )->joinUrlRewrite();

        return $collection;
    }
}
