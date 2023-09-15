<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Controller\Search;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Apm\AttributeSearch\Model\Services\AttributeSearchService;
use Apm\AttributeSearch\Api\AggregationInterface;
use Apm\AttributeSearch\Model\Aggregation\AttributeSelectOption;

class Index extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    private const SEARCH_ATTRIBUTE_CODE = 'search_attr_code';

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AttributeSelectOption $aggregation
     * @param StoreManagerInterface $storeManager
     * @param AttributeSearchService $attributeSearchService
     */
    public function __construct(
        Context $context,
        protected JsonFactory $resultJsonFactory,
        protected AttributeSelectOption $aggregation,
        protected StoreManagerInterface $storeManager,
        protected AttributeSearchService $attributeSearchService
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $categoryId = $this->storeManager->getStore()->getRootCategoryId();

        $attributeOptionCollectionResult = $this->attributeSearchService
            ->setCategoryId((int)$categoryId)
            ->setSearchAttributeCode((string)$this->getRequest()->getParam(self::SEARCH_ATTRIBUTE_CODE))
            ->setRequest($this->getRequest())
            ->getAttributeFilterResult();

        /** @var AggregationInterface $aggregatedData */
        $aggregatedData = $this->aggregation->aggregate($attributeOptionCollectionResult);

        return $resultJson->setData(['content' => implode('\n', $aggregatedData)]);
    }
}
