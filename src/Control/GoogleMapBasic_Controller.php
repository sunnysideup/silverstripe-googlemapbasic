<?php

namespace Sunnysideup\GooglemapBasic\Control;

use Sunnysideup\GooglemapBasic\Model\GoogleMapBasic;
use SilverStripe\Core\Config\Config;
use Sunnysideup\GooglemapBasic\Control\GoogleMapBasic_Controller;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Director;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;

class GoogleMapBasic_Controller extends Extension
{
    private static $js_location = '';

    private static $id_of_map_div = GoogleMapBasic::class;

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
            } else {
                $fileLocation = Config::inst()->get(GoogleMapBasic_Controller::class, "js_location");
                $idOfMapDiv = Config::inst()->get(GoogleMapBasic_Controller::class, "id_of_map_div");
                $apiKey = Config::inst()->get(GoogleMapBasic_Controller::class, "api_key");
                if (! $fileLocation) {
                    $fileLocation = 'googlemapbasic/javascript/GoogleMapBasic.js';
                }
                Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
                Requirements::javascript(Director::protocol() . 'maps.googleapis.com/maps/api/js?key='.$apiKey .'');
                Requirements::javascript($fileLocation);
                $infoWindow = '<div class="infoWindowContent typography">'.$this->owner->InfoWindowContent.$this->GoogleMapBasicExternalLinkHTML().'</div>';
                Requirements::customScript(
                    "
                    if(typeof GoogleMapBasicOptions === 'undefined') {
                        var GoogleMapBasicOptions = new Array();
                    }
                    GoogleMapBasicOptions.push(
                        {
                            idOfMapDiv: \"".$this->cleanJS($idOfMapDiv)."\",
                            infoWindowContent: \"".$this->cleanJS($infoWindow)."\",
                            title: \"".$this->cleanJS($this->owner->Title)."\",
                            address: \"".$this->cleanJS($this->owner->Address)."\",
                            lat: ".floatval($this->owner->Lat).",
                            lng: ".floatval($this->owner->Lng).",
                            zoomLevel: ".intval($this->owner->ZoomLevel)."
                        }
                    );
                    ",
                    'GoogleMapBasicData'
                );
                Requirements::themedCSS('sunnysideup/googlemapbasic: GoogleMapBasic', "googlemapbasic");
                return _t("GoolgeMapBasic.MAPLOADING", "map loading...");
            }
        }
        return false;
    }

    public function GoogleMapBasicStaticMapSource($width = 512, $height = 512)
    {
        $center = $this->googleMapBasicCenterForLink();
        $src = Director::protocol() . 'maps.googleapis.com/maps/api/staticmap?';
        $src .= 'center='.$center;
        $src .= '&zoom='.$this->owner->ZoomLevel;
        $src .= '&size='.$width.'x'.$height.'';
        $src .= '&maptype=roadmap';
        $src .= '&markers=color:red%7C'.$center;

        return $src;
    }


    public function GoogleMapBasicExternalLink()
    {
        if ($this->owner->HasGoogleMap()) {
            $center = $this->googleMapBasicCenterForLink();
            return Director::protocol() . 'maps.google.com/maps?q='.$center.'&z='.$this->owner->ZoomLevel;
        }
    }

    public function GoogleMapBasicExternalLinkHTML()
    {
        if ($this->owner->HasGoogleMap()) {
            return '<p id="GoogleMapBasicExternalLink"><a href="'.$this->GoogleMapBasicExternalLink().'" target="_map">'._t("GoogleMapBasic.OPENINGOOGLEMAPS", "open in Google Maps").'</a></p>';
        }
    }

    protected function googleMapBasicCenterForLink()
    {
        if ($this->owner->Lat && $this->owner->Lng) {
            $center = $this->owner->Lat.','.$this->owner->Lng;
        } elseif ($this->owner->Address) {
            $center = urlencode($this->owner->Address);
        } else {
            $center = '';
        }

        return $center;
    }

    protected function cleanJS($s)
    {
        $s = Convert::raw2js($s);
        $s = str_replace("\r\n", " ", $s);
        $s = str_replace("\n", " ", $s);
        $s = str_replace('/', '\/', $s);
        return $s;
    }
}
