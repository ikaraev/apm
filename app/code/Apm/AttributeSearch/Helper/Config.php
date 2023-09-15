<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

final class Config extends AbstractHelper
{
    public const CONFIG_ATTRIBUTE_SEARCH_FILE_PATH = 'apm_attribute_search/general/map_attribute_search';

    protected $serializer;

    public function __construct(Context $context)
    {
        parent::__construct($context);

        $this->serializer = ObjectManager::getInstance()->get(Json::class);
    }

    public function getProductAttributeMap(): array
    {
        $unSerializedData = $this->scopeConfig->getValue(self::CONFIG_ATTRIBUTE_SEARCH_FILE_PATH, ScopeInterface::SCOPE_WEBSITE);
        $serializedData = $this->serializer->unserialize($unSerializedData);
        $result = [];

        foreach ($serializedData as $hash => $fields) {
            $result[] = [
                'attribute_field' => $fields['attribute_field'],
                'title_field' => $fields['title_field']
            ];
        }

        return $result;
    }

    public function getTitleFieldByAttributeCode(string $code): string
    {
        $productAttributeCodes = $this->getProductAttributeMap();

        foreach ($productAttributeCodes as $productAttributeCode) {
            if ($productAttributeCode['attribute_field'] === $code) {
                return $productAttributeCode['title_field'];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public function getProductAttributeCodesMap(): array
    {
        $productAttributeMap = $this->getProductAttributeMap();

        return array_column($productAttributeMap, 'attribute_field');
    }
}
