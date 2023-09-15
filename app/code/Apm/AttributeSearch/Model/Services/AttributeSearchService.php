<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model\Services;

use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Model\Layer\Filter\Category;
use Magento\CatalogSearch\Model\Layer\Filter\Decimal;
use Magento\CatalogSearch\Model\Layer\Filter\Price;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\RequestInterface;
use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterface;
use Apm\AttributeSearch\Api\AttributeOptionCollectionInterface;
use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterfaceFactory;
use Apm\AttributeSearch\Api\AttributeOptionCollectionInterfaceFactory;
use Magento\CatalogSearch\Model\Layer\Filter\Attribute;
use Apm\AttributeSearch\Helper\Config as ConfigHelper;

class AttributeSearchService
{
    protected int|null $categoryId = null;

    protected string $searchAttributeCode = '';

    private FilterList $filterList;

    protected RequestInterface|null $request = null;

    public function __construct(
        protected ConfigHelper $configHelper,
        protected LayerResolver $layerResolver,
        protected ObjectManagerInterface $objectManager,
        protected FilterableAttributeList $filterableAttributeList,
        protected AttributeOptionItemInterfaceFactory $attributeOptionItemFactory,
        protected AttributeOptionCollectionInterfaceFactory $attributeOptionCollectionFactory
    ) {
        $this->filterList = $this->objectManager->create(FilterList::class, [
            'filterableAttributes' => $filterableAttributeList,
            'filters' => [
                FilterList::CATEGORY_FILTER  => Category::class,
                FilterList::ATTRIBUTE_FILTER => Attribute::class,
                FilterList::PRICE_FILTER => Price::class,
                FilterList::DECIMAL_FILTER => Decimal::class,
            ]
        ]);
    }

    /**
     * @param int $categoryId
     * @return self
     */
    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setSearchAttributeCode(string $code): self
    {
        $this->searchAttributeCode = $code;

        return $this;
    }

    public function setRequest(RequestInterface $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getAttributeFilterResult(): AttributeOptionCollectionInterface
    {
        $layer = $this->layerResolver->get();
        $layer->setCurrentCategory($this->categoryId);

        $filters = $this->applyFilters($this->filterList->getFilters($layer));

        /** @var AttributeOptionCollectionInterface $attributeOptionCollection */
        $attributeOptionCollection = $this->attributeOptionCollectionFactory->create();

        $this->setDefaultAttributeOptionToCollection($attributeOptionCollection);

        foreach ($filters as $filter) {

            foreach ($filter->getItems() as $item) {
                if ($filter->getRequestVar() === $this->searchAttributeCode) {
                    /** @var AttributeOptionItemInterface $attributeOption */
                    $attributeOption = $this->attributeOptionItemFactory->create();

                    $attributeOption
                        ->setValue((int)$item->getValue())
                        ->setLabel((string)$item->getLabel())
                        ->setCount((int)$item->getCount());

                    $attributeOptionCollection->addItem($attributeOption);
                }
            }
        }

        return $attributeOptionCollection;
    }

    private function applyFilters(array $filters): array
    {
        $appliedFilters = [];
        foreach ($filters as $filter) {
            /** @var AbstractFilter $filter */
            $appliedFilters[] = $filter->apply($this->request);
        }

        return $appliedFilters;
    }

    private function setDefaultAttributeOptionToCollection(AttributeOptionCollectionInterface $attributeOptionCollection)
    {

        $searchFieldTitle = $this->configHelper->getTitleFieldByAttributeCode($this->searchAttributeCode);

        /** @var AttributeOptionItemInterface $attributeOption */
        $attributeOption = $this->attributeOptionItemFactory->create();

        $attributeOption
            ->setLabel($searchFieldTitle);

        $attributeOptionCollection->addItem($attributeOption);
    }
}
