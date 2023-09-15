<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model;

use Magento\Framework\DataObject;
use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterface;

class AttributeOptionItem extends DataObject implements AttributeOptionItemInterface
{

    public function getLabel(): string
    {
        return (string)$this->getData(self::LABEL);
    }

    public function setLabel(string $label): AttributeOptionItemInterface
    {
        $this->setData(self::LABEL, $label);

        return $this;
    }

    public function getValue(): int
    {
        return (int)$this->getData(self::VALUE);
    }

    public function setValue(int $value): AttributeOptionItemInterface
    {
        $this->setData(self::VALUE, $value);

        return $this;
    }

    public function getCount(): int
    {
        return (int)$this->getData(self::COUNT);
    }

    public function setCount(int $count): AttributeOptionItemInterface
    {
        return $this->setData(self::COUNT, $count);
    }
}
