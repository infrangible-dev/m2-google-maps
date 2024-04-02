<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Adminhtml\Widget;

use Infrangible\Core\Helper\Registry;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Editor
    extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Factory */
    protected $elementFactory;

    /** @var Config */
    protected $wysiwygConfig;

    /**
     * @param Registry $registryHelper
     * @param Context  $context
     * @param Factory  $elementFactory
     * @param Config   $wysiwygConfig
     * @param array    $data
     */
    public function __construct(
        Registry $registryHelper,
        Context $context,
        Factory $elementFactory,
        Config $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registryHelper = $registryHelper;

        $this->elementFactory = $elementFactory;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $this->registryHelper->register('wysiwyg_config_add_images', false, true);

        /** @var \Magento\Framework\Data\Form\Element\Editor $input */
        $input = $this->elementFactory->create('editor', ['data' => $element->getData()]);

        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setDataUsingMethod('wysiwyg', true);
        $input->setDataUsingMethod(
            'config',
            $this->wysiwygConfig->getConfig([
                                                'add_variables'  => false,
                                                'add_widgets'    => false,
                                                'add_directives' => false
                                            ])
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
