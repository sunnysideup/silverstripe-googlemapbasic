<?php

namespace Sunnysideup\GooglemapBasic\Control;

use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use Sunnysideup\GooglemapBasic\Model\GoogleMapBasic;

class GoogleMapBasicController extends Extension
{
    private static $js_location = '';

    private static $id_of_map_div = 'GoogleMapBasic';

    private static $api_key = '';

    public function HasGoogleMap()
    {
        return $this->owner->ShowMap && $this->owner->Address;
    }

    public function GoogleMapBasic()
    {
        if ($this->owner->HasGoogleMap()) {
            if ($this->owner->StaticMap) {
                return true;
            }
            $fileLocation = Config::inst()->get(GoogleMapBasicController::class, 'js_location');
            $idOfMapDiv = Config::inst()->get(GoogleMapBasicController::class, 'id_of_map_div');
            $apiKey = Config::inst()->get(GoogleMapBasicController::class, 'api_key');
            if (! $fileLocation) {
                $fileLocation = 'sunnysideup/googlemapbasic: client/javascript/GoogleMapBasic.js';
            }
            Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
            Requirements::javascript('//maps.googleapis.com/maps/api/js?key=' . $apiKey . '');
            Requirements::javascript($fileLocation);
            $infoWindow = '<div class="infoWindowContent typography">' . $this->owner->InfoWindowContent . $this->GoogleMapBasicExternalLinkHTML() . '</div>';
            Requirements::customScript(
                "
                    if(typeof GoogleMapBasicOptions === 'undefined') {
                        var GoogleMapBasicOptions = new Array();
                    }
                    GoogleMapBasicOptions.push(
                        {
                            idOfMapDiv: \"" . $this->cleanJS($idOfMapDiv) . '",
                            infoWindowContent: "' . $this->cleanJS($infoWindow) . '",
                            title: "' . $this->cleanJS($this->owner->Title) . '",
                            address: "' . $this->cleanJS($this->owner->Address) . '",
                            lat: ' . floatval($this->owner->Lat) . ',
                            lng: ' . floatval($this->owner->Lng) . ',
                            zoomLevel: ' . intval($this->owner->ZoomLevel) . '
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
        $src .= '&zoom=' . $this->owner->ZoomLevel;
        $src .= '&size=' . $width . 'x' . $height . '';
        $src .= '&maptype=roadmap';
        $src .= '&markers=color:red%7C' . $center;

        return $src;
    }

    public function GoogleMapBasicExternalLink()
    {
        if ($this->owner->HasGoogleMap()) {
            $center = $this->googleMapBasicCenterForLink();

            return Director::protocol() . 'maps.google.com/maps?q=' . $center . '&z=' . $this->owner->ZoomLevel;
        }
    }

    public function GoogleMapBasicExternalLinkHTML()
    {
        if ($this->owner->HasGoogleMap()) {
            return '<p id="GoogleMapBasicExternalLink"><a href="' . $this->GoogleMapBasicExternalLink() . '" target="_map">' . _t('GoogleMapBasic.OPENINGOOGLEMAPS', 'open in Google Maps') . '</a></p>';
        }
    }

    protected function googleMapBasicCenterForLink()
    {
        if ($this->owner->Lat && $this->owner->Lng) {
            $center = $this->owner->Lat . ',' . $this->owner->Lng;
        } elseif ($this->owner->Address) {
            $center = urlencode($this->owner->Address);
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
