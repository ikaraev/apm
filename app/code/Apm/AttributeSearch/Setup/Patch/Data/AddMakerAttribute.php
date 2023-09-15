<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Setup\Patch\Data;

use Apm\AttributeSearch\Model\Config\Source\YearOptions;
use Apm\AttributeSearch\Model\Config\Source\MarkOptions;
use Magento\Catalog\Api\AttributeSetManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class AddMakerAttribute implements DataPatchInterface, PatchRevertableInterface
{
    public const MAKER_ATTRIBUTE = 'maker';
    public const MODEL_ATTRIBUTE = 'model';
    public const YEAR_ATTRIBUTE = 'year';

    /**
     * @param YearOptions $yearOptions
     * @param MarkOptions $markOptions
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Product $product
     * @param AttributeSetInterfaceFactory $attributeSetInterfaceFactory
     * @param AttributeSetManagementInterface $attributeSetManagement
     * @param AttributeGroupInterfaceFactory $attributeGroupFactory
     * @param AttributeGroupRepositoryInterface $attributeGroupRepository
     */
    public function __construct(
        protected YearOptions                       $yearOptions,
        protected MarkOptions                       $markOptions,
        protected ModuleDataSetupInterface          $moduleDataSetup,
        protected EavSetupFactory                   $eavSetupFactory,
        protected Product                           $product,
        protected AttributeSetInterfaceFactory      $attributeSetInterfaceFactory,
        protected AttributeSetManagementInterface   $attributeSetManagement,
        protected AttributeGroupInterfaceFactory    $attributeGroupFactory,
        protected AttributeGroupRepositoryInterface $attributeGroupRepository
    ) {}

    /**
     * @return void
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ([self::MAKER_ATTRIBUTE, self::MODEL_ATTRIBUTE, self::YEAR_ATTRIBUTE] as $attributeCode) {
            $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        }

        $attributes = [
            self::MAKER_ATTRIBUTE => [
                'type' => 'varchar',
                'label' => 'Maker',
                'input' => 'select',
                'source' => '',
                'visible' => true,
                'required' => true,
                'filterable' => true,
                'user_defined' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'in_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'attribute_group_name' => 'General',
                'backend' => ArrayBackend::class,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable,grouped,giftcard',
                'options' => $this->markOptions->toListArray()
            ],
            self::MODEL_ATTRIBUTE => [
                'type' => 'varchar',
                'label' => 'Model',
                'input' => 'select',
                'source' => '',
                'visible' => true,
                'required' => true,
                'filterable' => true,
                'user_defined' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'in_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'attribute_group_name' => 'General',
                'backend' => ArrayBackend::class,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable,grouped,giftcard',
                'options' => []
            ],
            self::YEAR_ATTRIBUTE => [
                'type' => 'int',
                'label' => 'Year',
                'input' => 'select',
                'visible' => true,
                'required' => true,
                'filterable' => true,
                'user_defined' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'in_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'attribute_group_name' => 'General',
                'source' => '',
                'backend' => '',
                'global' => Attribute::SCOPE_GLOBAL,
                'option' => $this->yearOptions->toListArray(),
            ]
        ];

        $this->createProductAttribute($attributes);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return void
     */
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Product::ENTITY, self::MAKER_ATTRIBUTE);

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function createProductAttribute($attributes)
    {
        foreach ($attributes as $attribute => $data) {

            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create();
            $productEntity = Product::ENTITY;
            $attrSetName = null;
            $attributeGroupId = null;

            /**
             * Initialise Attribute Set Id
             */
            if (isset($data['attribute_set_name'])) {
                $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $data['attribute_set_name']);

                /**
                 * If our attribute set name does not exist, we create it.
                 * By default if Magento does not find an attribute set Id, it returns the default attribute set Id
                 */
                if ($attributeSetId == $eavSetup->getDefaultAttributeSetId($productEntity)
                    && $data['attribute_set_name'] != 'Default') {
                    $attrSetName = $data['attribute_set_name'];
                    $this->createAttributeSet($attrSetName);
                    $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $attrSetName);
                }
            } else {
                $attributeSetId = $this->product->getDefaultAttributeSetId();
            }

            /**
             * Initialise Attribute Group Id
             */
            if (isset($data['attribute_group_name'])) {
                $attributeGroupId = $eavSetup->getAttributeGroupId(
                    $productEntity,
                    $attributeSetId,
                    $data['attribute_group_name']
                );

                /**
                 * If our attribute group name does not exist, we create it
                 */
                if ($attributeGroupId == $eavSetup->getDefaultAttributeGroupId($productEntity)
                    && $data['attribute_group_name'] != 'General') {
                    $attributeGroupName = $data['attribute_group_name'];
                    $this->createAttributeGroup($attributeGroupName, $attrSetName);
                    $attributeGroupId = $eavSetup->getAttributeGroupId(
                        $productEntity,
                        $attributeSetId,
                        $attributeGroupName
                    );
                }
            }

            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                $productEntity,
                $attribute,
                [
                    'group' => $attributeGroupId ? '' : 'General', // Let empty, if we want to set an attribute group id
                    'type' => $data['type'],
                    'backend' => $data['backend'],
                    'frontend' => '',
                    'label' => $data['label'],
                    'input' => $data['input'],
                    'class' => '',
                    'source' => $data['source'],
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'searchable' => true,
                    'filterable' => $data['filterable'],
                    'is_filterable_in_search' => true,
                    'comparable' => false,
                    'visible_on_front' => $data['visible_on_front'],
                    'used_in_product_listing' => $data['used_in_product_listing'],
                    'unique' => false
                ]
            );


            /**
             * Set attribute group Id if needed
             */
            if (!is_null($attributeGroupId)) {
                /**
                 * Set the attribute in the right attribute group in the right attribute set
                 */
                $eavSetup->addAttributeToGroup($productEntity, $attributeSetId, $attributeGroupId, $attribute);
            }


            /**
             * Add options if needed
             */
            if (isset($data['options'])) {
                $options = [
                    'attribute_id' => $eavSetup->getAttributeId($productEntity, $attribute),
                    'values' => $data['options']
                ];
                $eavSetup->addAttributeOption($options);
            }
        }
    }

    private function createAttributeSet($attrSetName)
    {
        $defaultAttributeSetId = $this->product->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetInterfaceFactory->create();
        $attributeSet->setAttributeSetName($attrSetName);
        $this->attributeSetManagement->create($attributeSet, $defaultAttributeSetId);
    }

    /**
     * @param $attributeGroupName
     * @param null $attrSetName
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAttributeGroup($attributeGroupName, $attrSetName = null)
    {

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $productEntity = Product::ENTITY;

        if ($attrSetName) {
            $this->createAttributeSet($attrSetName);
            $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $attrSetName);
        } else {
            $attributeSetId = $this->product->getDefaultAttributeSetId();
        }


        $attributeGroup = $this->attributeGroupFactory->create();

        $attributeGroup->setAttributeSetId($attributeSetId);
        $attributeGroup->setAttributeGroupName($attributeGroupName);
        $this->attributeGroupRepository->save($attributeGroup);
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
