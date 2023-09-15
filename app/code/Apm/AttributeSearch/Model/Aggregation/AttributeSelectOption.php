<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model\Aggregation;

use Apm\AttributeSearch\Api\AggregationInterface;
use Apm\AttributeSearch\Api\AttributeOptionCollectionInterface;
use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterface;

class AttributeSelectOption implements AggregationInterface
{
    private const ID = 'id';
    private const TEXT = 'text';

    public function aggregate(AttributeOptionCollectionInterface $data): array
    {
        $result = [];

        foreach ($data->getItems() as $item) {
            $value = $item->getValue() ?: '';
            $label = $item->getCount() ?
                $item->getLabel() . ' (' . $item->getCount() . ')' :
                $item->getLabel();
            $result[] = "<option value=\"$value\">$label</option>";
        }

        return $result;
    }
}
