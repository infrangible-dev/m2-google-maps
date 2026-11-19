<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * Text area of a widget parameter (the JSON of markers, the restrictions of the autocomplete).
 */
class Textarea extends Template
{
    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(Context $context, protected Factory $elementFactory, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Replaces the field of the parameter with the text area.
     *
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $input = $this->elementFactory->create('textarea', ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->addClass('widget-option');
        $input->addClass('input-textarea');
        $input->addClass('admin__control-text');

        if ($element->getDataUsingMethod('required')) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml());

        return $element;
    }
}
