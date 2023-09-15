<?php

namespace Apm\AttributeSearch\Api;

interface AggregationInterface
{
    public function aggregate(AttributeOptionCollectionInterface $data): array;
}
