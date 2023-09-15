<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model;

use Apm\AttributeSearch\Api\AttributeOptionCollectionInterface;
use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterface;

class AttributeOptionCollection implements AttributeOptionCollectionInterface
{
    private array $items = [];

    public function addItem(AttributeOptionItemInterface $item): AttributeOptionCollectionInterface
    {
        $this->items[] = $item;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = [
                AttributeOptionItemInterface::LABEL => $item->getLabel() . ' (' . $item->getCount() . ')',
                AttributeOptionItemInterface::VALUE => $item->getValue()
            ];
        }

        return $result;
    }
}
