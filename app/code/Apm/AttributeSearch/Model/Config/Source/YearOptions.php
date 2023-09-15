<?php

namespace Apm\AttributeSearch\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class YearOptions extends AbstractSource implements OptionSourceInterface
{
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $startYear = 1985;
            $endYear = 2023;

            for ($year = $startYear; $year <= $endYear; $year++) {
                $this->_options[] = ['value' => $year, 'label' => $year];
            }
        }

        return $this->_options;
    }

    public function toListArray(): array
    {
        $result = [];

        $options = $this->getAllOptions();

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }
}
