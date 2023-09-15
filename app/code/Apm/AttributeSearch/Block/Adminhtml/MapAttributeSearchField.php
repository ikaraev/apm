<?php

declare(strict_types=1);

namespace Apm\AttributeSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;
use Apm\AttributeSearch\Block\Adminhtml\Form\Field\AttributeColumn;

class MapAttributeSearchField extends AbstractFieldArray
{
    private BlockInterface $dropdownRenderer;

    protected function _prepareToRender(): void
    {
        $this->dropdownRenderer = $this->getLayout()->createBlock(
            AttributeColumn::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );

        $this->addColumn(
            'attribute_field',
            [
                'label'    => __('Product Attributes'),
                'renderer' => $this->dropdownRenderer,
                'class'    => 'required-entry'
            ]
        );
        $this->addColumn(
            'title_field',
            [
                'label' => __('Attribute Title'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options      = [];
        $companyField = $row->getCompanyField();
        if ($companyField !== null) {
            $options['option_' . $this->dropdownRenderer->calcOptionHash($companyField)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}
