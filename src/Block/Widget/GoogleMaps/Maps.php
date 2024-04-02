<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Infrangible\Core\Helper\Registry;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Maps
    extends Template
    implements BlockInterface
{
    /** @var Registry */
    protected $registryHelper;

    /**
     * @param Template\Context $context
     * @param Registry         $registryHelper
     * @param array            $data
     */
    public function __construct(Template\Context $context, Registry $registryHelper, array $data = [])
    {
        parent::__construct($context, $data);

        $this->registryHelper = $registryHelper;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->registryHelper->register('google_maps_api', true, true);

        return parent::_toHtml();
    }
}
