<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Textarea
    extends Template
{
    /** @var Factory */
    protected $elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->elementFactory = $elementFactory;
    }

    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        /** @var \Magento\Framework\Data\Form\Element\Textarea $input */
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
