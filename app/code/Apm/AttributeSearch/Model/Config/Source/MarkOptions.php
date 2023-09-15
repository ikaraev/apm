<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class MarkOptions extends AbstractSource implements OptionSourceInterface
{
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => 'alfa_romeo', 'label' => 'Alfa Romeo'],
                ['value' => 'audi', 'label' => 'Audi'],
                ['value' => 'bmw', 'label' => 'BMW'],
                ['value' => 'buick', 'label' => 'Buick'],
                ['value' => 'cadillac', 'label' => 'Cadillac'],
                ['value' => 'chevrolet', 'label' => 'Chevrolet'],
                ['value' => 'chrysler', 'label' => 'Chrysler'],
                ['value' => 'dodge', 'label' => 'Dodge'],
                ['value' => 'ferrari', 'label' => 'Ferrari'],
                ['value' => 'fiat', 'label' => 'Fiat'],
                ['value' => 'ford', 'label' => 'Ford'],
                ['value' => 'gmc', 'label' => 'GMC'],
                ['value' => 'honda', 'label' => 'Honda'],
                ['value' => 'hyundai', 'label' => 'Hyundai'],
                ['value' => 'infiniti', 'label' => 'Infiniti'],
                ['value' => 'jaguar', 'label' => 'Jaguar'],
                ['value' => 'jeep', 'label' => 'Jeep'],
                ['value' => 'kia', 'label' => 'Kia'],
                ['value' => 'lamborghini', 'label' => 'Lamborghini'],
                ['value' => 'land_rover', 'label' => 'Land Rover'],
                ['value' => 'lexus', 'label' => 'Lexus'],
                ['value' => 'lincoln', 'label' => 'Lincoln'],
                ['value' => 'mazda', 'label' => 'Mazda'],
                ['value' => 'mercedes_benz', 'label' => 'Mercedes-Benz'],
                ['value' => 'mini', 'label' => 'MINI'],
                ['value' => 'mitsubishi', 'label' => 'Mitsubishi'],
                ['value' => 'nissan', 'label' => 'Nissan'],
                ['value' => 'porsche', 'label' => 'Porsche'],
                ['value' => 'ram', 'label' => 'RAM'],
                ['value' => 'subaru', 'label' => 'Subaru'],
                ['value' => 'tesla', 'label' => 'Tesla'],
                ['value' => 'toyota', 'label' => 'Toyota'],
                ['value' => 'volkswagen', 'label' => 'Volkswagen'],
                ['value' => 'volvo', 'label' => 'Volvo'],
                ['value' => 'acura', 'label' => 'Acura'],
                ['value' => 'aston_martin', 'label' => 'Aston Martin'],
                ['value' => 'bentley', 'label' => 'Bentley'],
                ['value' => 'bugatti', 'label' => 'Bugatti'],
                ['value' => 'genesis', 'label' => 'Genesis'],
                ['value' => 'hennessey', 'label' => 'Hennessey'],
                ['value' => 'jaguar_land_rover', 'label' => 'Jaguar Land Rover'],
                ['value' => 'koenigsegg', 'label' => 'Koenigsegg'],
                ['value' => 'lamborghini', 'label' => 'Lamborghini'],
                ['value' => 'lotus', 'label' => 'Lotus'],
                ['value' => 'maserati', 'label' => 'Maserati'],
                ['value' => 'mclaren', 'label' => 'McLaren'],
                ['value' => 'pagani', 'label' => 'Pagani'],
                ['value' => 'rolls_royce', 'label' => 'Rolls-Royce'],
            ];
        }

        return $this->_options;
    }

    /**
     * @return array
     */
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
