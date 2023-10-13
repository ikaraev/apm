<?php

declare(strict_types=1);

namespace Apm\Carousel\ViewModel;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Catalog\Helper\Image as ImageHelper;

class CarouselItemViewModel extends Template implements ArgumentInterface
{
    public function __construct(
        Template\Context $context,
        private CategoryRepositoryInterface $categoryRepository,
        private ProductRepositoryInterface $productRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderBuilder $sortOrderBuilder,
        private ImageHelper $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getCategoryItems()
    {
        $category = $this->categoryRepository->get(3);

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('category_id', 4);
        $searchCriteria->setPageSize(10);

        $sortOrder = $this->sortOrderBuilder->setField('updated_at')->setDirection('DESC')->create();

        $searchCriteria->setSortOrders([$sortOrder]);

        return $this->productRepository->getList($searchCriteria->create());
    }

    public function getFullImgUrl($product)
    {
//        $default = $this->imageHelper->getDefaultPlaceholderUrl('image');

        $imageUrl = $this->imageHelper->init($product, 'product_page_image_large')->getUrl();

        return $imageUrl;
    }
}
