<?php

declare(strict_types=1);

namespace Apm\Store\Setup\Patch\Data;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Store\Model\ResourceModel\Group as GroupResourceModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;

class AddStores implements DataPatchInterface
{
    public const LOCALS = [
        'en' => [
            'lang' => 'en_US',
            'weight_unit' => '',
            'currency_default' => 'USD',
            'currency_allow' => 'USD',
        ],
        'default' => [
            'lang' => 'ka_GE',
            'weight_unit' => 'kgs',
            'currency_default' => 'GEL',
            'currency_allow' => 'GEL,USD'
        ],
        'ru' => [
            'lang' => 'ru_RU',
            'weight_unit' => 'kgs',
            'currency_default' => 'RUB',
            'currency_allow' => 'RUB,USD',
        ]
    ];

    /**
     * @param LoggerInterface $logger
     * @param StoreFactory $storeFactory
     * @param GroupFactory $groupFactory
     * @param StoreManagerInterface $storeManager
     * @param StoreResourceModel $storeResourceModel
     * @param GroupResourceModel $groupResourceModel
     * @param ScopeConfigInterface $scopeConfig
     * @param MutableScopeConfigInterface $mutableScopeConfig
     */
    public function __construct(
        private LoggerInterface $logger,
        private StoreFactory $storeFactory,
        private GroupFactory $groupFactory,
        private StoreManagerInterface $storeManager,
        private StoreResourceModel $storeResourceModel,
        private GroupResourceModel $groupResourceModel,
        private ScopeConfigInterface $scopeConfig,
        private MutableScopeConfigInterface $mutableScopeConfig
    ) {}

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(): void
    {
        $defaultStore = $this->storeManager->getStore();

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $website = $this->storeManager->getWebsite();
        $rootCategoryId = $defaultStore->getRootCategoryId();

        foreach (self::LOCALS as $local => $values) {
            try {
                $store = $this->storeFactory->create();
                $store->load($local);

                if(!$store->getId()){
                    $store->setCode($local);
                    $store->setName(mb_strtoupper($local));
                    $store->setWebsite($website);
                    $store->setGroupId($defaultStore->getStoreGroupId());
                    $store->setData('is_active', 1);
                    $store->setBaseUrl($baseUrl . DIRECTORY_SEPARATOR . $local . DIRECTORY_SEPARATOR);
                    $this->storeResourceModel->save($store);
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    private function setStoreConfigSettings($store, $values)
    {
        $this->mutableScopeConfig->setValue(
            'general/locale/code',
            $values['lang'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );

        $this->mutableScopeConfig->setValue(
            'general/locale/weight_unit',
            $values['weight_unit'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );

        $this->mutableScopeConfig->setValue(
            'currency/options/default',
            $values['currency_default'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );

        $this->mutableScopeConfig->setValue(
            'currency/options/allow',
            $values['currency_allow'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }
}
