<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Model\Config\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ProductAttribute extends AbstractSource implements OptionSourceInterface
{
    public function __construct(protected ResourceConnection $resourceConnection) {}

    public function getAllOptions()
    {
        if ($this->_options === null) {
            $attributeOptions = $this->getProductAttributes();

            foreach ($attributeOptions as $attributeOption) {
                $this->_options[] = ['value' => $attributeOption['attribute_code'], 'label' => $attributeOption['frontend_label']];
            }
        }

        return $this->_options;
    }

    public function getProductAttributes(): array
    {
        $resourceConnection = $this->resourceConnection->getConnection();

        $catalogProductType = $resourceConnection
            ->select()
            ->from('eav_entity_type', ['entity_type_id'])
            ->where('`entity_type_code` = \'catalog_product\'')
            ->query()
            ->fetch();

        $catalogProductTypeId = current($catalogProductType) ?? '4';

        return $resourceConnection
            ->select()
            ->from($resourceConnection->getTableName('eav_attribute'))
            ->where("entity_type_id = '$catalogProductTypeId'")
            ->query()
            ->fetchAll();
    }
}
