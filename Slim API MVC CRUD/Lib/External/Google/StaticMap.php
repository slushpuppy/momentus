<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 9/10/2018
 * Time: 6:28 PM
 */

namespace Lib\External\Google;


class StaticMap
{
    private $ch,$params = [],$path;
    private const URL = 'https://maps.googleapis.com/maps/api/staticmap';
    public function __construct()
    {
        $this->ch = \curl_init();
    }
    public function setSize(int $horizontal,int $vertical) {
        $this->params['size'] = $horizontal.'x'.$vertical;
        return $this;

    }
    public function addPath() {

    }

    public function setScale(int $scale) {
        if ($scale == 1 || $scale == 2 || $scale == 4) {
            $this->params['size'] = $scale;
            return $this;
        } else {
            throw new \InvalidArgumentException("Scale must only be 1,2 or 4");
        }

    }

    public function setMapType(StaticMapType $type) {
        $this->params['maptype'] = $type;
    }
    public function setLanguage($language) {
        $this->params['language'] = $language;
    }

}
class StaticMapType {
    const ROADMAP = 'roadmap', SATELLITE='satellite', HYBRID='hybrid', TERRAIN='terrain';
}