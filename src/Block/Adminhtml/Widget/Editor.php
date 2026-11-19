<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Adminhtml\Widget;

use Infrangible\Core\Helper\Registry;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * WYSIWYG editor of a widget parameter (the info window of a marker), without images, variables and widgets.
 */
class Editor extends Template
{
    /**
     * @param Registry $registryHelper
     * @param Context $context
     * @param Factory $elementFactory
     * @param Config $wysiwygConfig
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        protected Registry $registryHelper,
        Context $context,
        protected Factory $elementFactory,
        protected Config $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Replaces the field of the parameter with the editor.
     *
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $this->registryHelper->register('wysiwyg_config_add_images', false, true);

        $input = $this->elementFactory->create('editor', ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setDataUsingMethod('wysiwyg', true);
        $input->setDataUsingMethod(
            'config',
            $this->wysiwygConfig->getConfig(
                ['add_variables' => false, 'add_widgets' => false, 'add_directives' => false]
            )
        );
        $input->addClass('widget-option');
        $input->addClass('input-textarea');
        $input->addClass('admin__control-text');

        if ($element->getDataUsingMethod('required')) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml());
        $element->setDataUsingMethod('value', '');

        $this->registryHelper->unregister('wysiwyg_config_add_images');

        return $element;
    }
}
