<?php

declare(strict_types=1);

namespace Apm\Catalog\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CategoryGridBlockViewModel extends Template implements ArgumentInterface
{
    public function __construct(
        Template\Context $context,
        private CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $category = $this->categoryRepository->get(3);

        return $category->getChildrenCategories()->getItems();
    }

    public function getFullCategoryImageUrl(string $categoryImage)
    {
        return $this->_storeManager->getStore()->getBaseUrl() . $categoryImage;
    }
}
