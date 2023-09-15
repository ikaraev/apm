<?php

namespace Apm\AttributeSearch\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Eav\Model\Config as EavConfig;
use Apm\AttributeSearch\Helper\Config as ApmHelperConfig;

class AttributeSearchViewModel extends Template implements ArgumentInterface
{
    public const MAKER_ATTRIBUTE = 'maker';

    public function __construct(
        Template\Context $context,
        protected EavConfig $eavConfig,
        protected ApmHelperConfig $apmHelperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getAttributeSearchBlock()
    {
        $result = [];
        $productAttributes = $this->apmHelperConfig->getProductAttributeMap();

        $first = false;
        foreach ($productAttributes as $productAttribute) {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $productAttribute['attribute_field']);

            if (!$first) {
                $result[] = [
                    'attribute_code' => $attribute->getAttributeCode(),
                    'title' => $productAttribute['title_field'],
                    'options' => $this->getAttributeOptionsByAttribute($attribute)
                ];
            } else {
                $result[] = [
                    'attribute_code' => $attribute->getAttributeCode(),
                    'title' => $productAttribute['title_field'],
                ];
            }

            $first = true;
        }

        return $result;
    }

    /**
     * @param $attribute
     * @return array
     */
    public function getAttributeOptionsByAttribute($attribute): array
    {
        $result = [];

        foreach ($attribute->getSource()->getAllOptions() as $option) {
            $result[] = $option;
        }

        return $result;
    }
}
