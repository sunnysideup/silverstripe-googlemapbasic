<?php

namespace Sunnysideup\GooglemapBasic\Control;

use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class GoogleMapBasicController extends Extension
{
    private static $js_location = '';

    private static $id_of_map_div = 'GoogleMapBasic';

    private static $api_key = '';

    public function HasGoogleMap()
    {
        return $this->getOwner()->ShowMap && $this->getOwner()->Address;
    }

    public function GoogleMapBasic()
    {
        if ($this->getOwner()->HasGoogleMap()) {
            if ($this->getOwner()->StaticMap) {
                return true;
            }
            $fileLocation = Config::inst()->get(GoogleMapBasicController::class, 'js_location');
            $idOfMapDiv = Config::inst()->get(GoogleMapBasicController::class, 'id_of_map_div');
            $apiKey = Config::inst()->get(GoogleMapBasicController::class, 'api_key');
            if (!$fileLocation) {
                $fileLocation = 'sunnysideup/googlemapbasic: client/javascript/GoogleMapBasic.js';
            }
            Requirements::javascript('//maps.googleapis.com/maps/api/js?key=' . $apiKey . '&callback=kickstartGoogleMaps');
            Requirements::javascript($fileLocation);
            $infoWindow = '<div class="infoWindowContent typography">' . $this->getOwner()->InfoWindowContent . $this->GoogleMapBasicExternalLinkHTML() . '</div>';
            Requirements::customScript(
                "
                    if(typeof GoogleMapBasicOptions === 'undefined') {
                        var GoogleMapBasicOptions = new Array();
                    }
                    GoogleMapBasicOptions.push(
                        {
                            idOfMapDiv: \"" . $this->cleanJS($idOfMapDiv) . '",
                            infoWindowContent: "' . $this->cleanJS($infoWindow) . '",
                            title: "' . $this->cleanJS($this->getOwner()->Title) . '",
                            address: "' . $this->cleanJS($this->getOwner()->Address) . '",
                            lat: ' . floatval($this->getOwner()->Lat) . ',
                            lng: ' . floatval($this->getOwner()->Lng) . ',
                            zoomLevel: ' . intval($this->getOwner()->ZoomLevel) . '
                        }
                    );
                    ',
                'GoogleMapBasicData'
            );
            Requirements::themedCSS('client/css/GoogleMapBasic');

            return _t('GoolgeMapBasic.MAPLOADING', 'map loading...');
        }

        return false;
    }

    public function GoogleMapBasicStaticMapSource($width = 512, $height = 512)
    {
        $center = $this->googleMapBasicCenterForLink();
        $apiKey = Config::inst()->get(GoogleMapBasicController::class, 'api_key');
        $src = '//maps.googleapis.com/maps/api/staticmap?';
        $src .= 'key=' . $apiKey;
        $src .= '&center=' . $center;
        $src .= '&zoom=' . $this->getOwner()->ZoomLevel;
        $src .= '&size=' . $width . 'x' . $height . '';
        $src .= '&maptype=roadmap';
        $src .= '&markers=color:red%7C' . $center;

        return $src;
    }

    public function GoogleMapBasicExternalLink()
    {
        if ($this->getOwner()->HasGoogleMap()) {
            $center = $this->googleMapBasicCenterForLink();

            return Director::protocol() . 'maps.google.com/maps?q=' . $center . '&z=' . $this->getOwner()->ZoomLevel;
        }
    }

    public function GoogleMapBasicExternalLinkHTML()
    {
        if ($this->getOwner()->HasGoogleMap()) {
            return '<p id="GoogleMapBasicExternalLink"><a href="' . $this->GoogleMapBasicExternalLink() . '" target="_map">' . _t('GoogleMapBasic.OPENINGOOGLEMAPS', 'open in Google Maps') . '</a></p>';
        }
    }

    protected function googleMapBasicCenterForLink()
    {
        if ($this->getOwner()->Lat && $this->getOwner()->Lng) {
            $center = $this->getOwner()->Lat . ',' . $this->getOwner()->Lng;
        } elseif ($this->getOwner()->Address) {
            $center = urlencode($this->getOwner()->Address);
        } else {
            $center = '';
        }

        return $center;
    }

    protected function cleanJS(string $s): string
    {
        $s = Convert::raw2js($s);
        $s = str_replace("\r\n", ' ', $s);
        $s = str_replace("\n", ' ', $s);

        return str_replace('/', '\/', $s);
    }
}
