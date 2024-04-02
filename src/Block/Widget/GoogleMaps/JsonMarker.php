<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class JsonMarker
    extends Marker
{
    /** @var Json */
    protected $json;

    /** @var Arrays */
    protected $arrays;

    /** @var Variables */
    protected $variables;

    /**
     * @param Template\Context $context
     * @param Registry         $registryHelper
     * @param Json             $json
     * @param Arrays           $arrays
     * @param Variables        $variables
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Json $json,
        Arrays $arrays,
        Variables $variables,
        array $data = []
    ) {
        parent::__construct($context, $registryHelper, $data);

        $this->json = $json;
        $this->arrays = $arrays;
        $this->variables = $variables;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_GoogleMaps::json_marker.phtml');

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getMarkerJson(): string
    {
        $markerJson = $this->getData('marker_json');

        $markerJson = str_replace(
            '<<<',
            '{',
            str_replace('>>>', '}', $markerJson === null ? '' : htmlspecialchars_decode($markerJson))
        );

        $locations = $this->json->decode($markerJson);

        if ($locations === null) {
            $markerJson = stripslashes($markerJson);
        }

        return $markerJson;
    }

    /**
     * @return float[]
     */
    public function getMapCenter(): array
    {
        $markerJson = $this->getMarkerJson();

        $locations = $this->json->decode($markerJson);

        if ($locations === null) {
            $markerJson = stripslashes($markerJson);

            $locations = $this->json->decode($markerJson);
        }

        if ($this->variables->isEmpty($locations)) {
            return [0, 0];
        }

        $coordinates = [];

        if (is_array($locations)) {
            foreach ($locations as $location) {
                $latitude = $this->arrays->getValue($location, 'lat');
                $longitude = $this->arrays->getValue($location, 'lng');

                if (!$this->variables->isEmpty($latitude) && !$this->variables->isEmpty($longitude)) {
                    $coordinates[] = [$latitude, $longitude];
                }
            }
        }

        $x = $y = $z = 0;

        $n = count($coordinates);

        foreach ($coordinates as $point) {
            $lt = $point[0] * pi() / 180;
            $lg = $point[1] * pi() / 180;
            $x += cos($lt) * cos($lg);
            $y += cos($lt) * sin($lg);
            $z += sin($lt);
        }

        $x /= $n;
        $y /= $n;

        return [atan2(($z / $n), sqrt($x * $x + $y * $y)) * 180 / pi(), atan2($y, $x) * 180 / pi()];
    }
}
