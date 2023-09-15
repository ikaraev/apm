<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Apm\AttributeSearch\Model\Config\Source\ProductAttribute;

class AttributeColumn extends Select
{
    public function __construct(
        Context                    $context,
        protected ProductAttribute $attribute,
        array                      $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value): self
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        return $this->attribute->getAllOptions();
    }
}
