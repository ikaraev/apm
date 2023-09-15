<?php

namespace Apm\AttributeSearch\Api\Data;

interface AttributeOptionItemInterface
{
    public const LABEL = 'label';
    public const VALUE = 'value';
    public const COUNT = 'count';

    public function getLabel(): string;

    public function setLabel(string $label): self;

    public function getValue(): int;

    public function setValue(int $value): self;

    public function getCount(): int;

    public function setCount(int $count): self;
}
