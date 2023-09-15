<?php

namespace Apm\AttributeSearch\Api;

use Apm\AttributeSearch\Api\Data\AttributeOptionItemInterface;

interface AttributeOptionCollectionInterface
{
    /**
     * @param AttributeOptionItemInterface $item
     * @return self
     */
    public function addItem(AttributeOptionItemInterface $item): self;

    /**
     * @return AttributeOptionItemInterface[]
     */
    public function getItems(): array;

    /**
     * @return array
     */
    public function toArray(): array;
}
