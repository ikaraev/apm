<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model\Layer\Attribute;

use Magento\Catalog\Model\Config\LayerCategoryConfig;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterList
{
    const CATEGORY_FILTER   = 'category';
    const ATTRIBUTE_FILTER  = 'attribute';
    const PRICE_FILTER      = 'price';
    const DECIMAL_FILTER    = 'decimal';

    /**
     * Filter factory
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var FilterableAttributeListInterface
     */
    protected $filterableAttributes;

    /**
     * @var string[]
     */
    protected $filterTypes = [
        self::ATTRIBUTE_FILTER => \Magento\Catalog\Model\Layer\Filter\Attribute::class
    ];

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    protected $filters = [];

    /**
     * @var LayerCategoryConfig
     */
    private $layerCategoryConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param LayerCategoryConfig $layerCategoryConfig
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        LayerCategoryConfig $layerCategoryConfig,
        array $filters = []
    ) {
        $this->objectManager = $objectManager;
        $this->filterableAttributes = $filterableAttributes;
        $this->layerCategoryConfig = $layerCategoryConfig;

        /** Override default filter type models */
        $this->filterTypes = array_merge($this->filterTypes, $filters);
    }

    /**
     * Retrieve list of filters
     *
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array|Filter\AbstractFilter[]
     */
    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {
        if (!count($this->filters)) {
            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
            }
        }
        return $this->filters;
    }

    /**
     * Create filter
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected function createAttributeFilter(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $filterClassName = $this->getAttributeFilterClass($attribute);

        $filter = $this->objectManager->create(
            $filterClassName,
            ['data' => ['attribute_model' => $attribute], 'layer' => $layer]
        );
        return $filter;
    }

    /**
     * Get Attribute Filter Class Name
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return string
     */
    protected function getAttributeFilterClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        return $this->filterTypes[self::ATTRIBUTE_FILTER];
    }
}
