<?php

/**
 *@author nicolaas[at] sunnysideup.co.nz
 *
 *
 **/


class GoogleMapBasic extends SiteTreeExtension
{
    private static $db = array(
        'ShowMap' => 'Boolean',
        'StaticMap' => 'Boolean',
        'Address' => 'Text',
        'ZoomLevel' => 'Int',
        'InfoWindowContent' => 'HTMLText'
    );


    private static $include_in_classes = array();

    private static $exclude_from_classes = array();

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->canHaveMap()) {
            $reloadMessage = " ";
            if (!$this->owner->ShowMap) {
                $reloadMessage = " (save (and publish) to see additional options)";
            }
            $fields->addFieldToTab("Root.Map", new CheckboxField("ShowMap", "Show map $reloadMessage"));
            if ($this->owner->ShowMap) {
                $fields->addFieldToTab("Root.Map", new CheckboxField("StaticMap", "Show map as picture only"));
                $fields->addFieldToTab("Root.Map", new TextField("Address"));
                $fields->addFieldToTab("Root.Map", new NumericField("ZoomLevel", "Zoom (1 = world, 20 = too close)"));
                $fields->addFieldToTab("Root.Map", $htmlEditorField = new HtmlEditorField("InfoWindowContent", "Info Window Content"));
                $htmlEditorField->setRows(5);
            }
        }
    }

    protected function canHaveMap()
    {
        $include = Config::inst()->get("GoogleMapBasic", "include_in_classes");
        $exclude = Config::inst()->get("GoogleMapBasic", "exclude_from_classes");
        if (!is_array($exclude) || !is_array($include)) {
            user_error("include or exclude classes is NOT an array", E_USER_NOTICE);
            return true;
        }
        if (!count($include) && !count($exclude)) {
            return true;
        }
        if (count($include) && in_array($this->owner->ClassName, $include)) {
            return true;
        }
        if (count($exclude) && !in_array($this->owner->ClassName, $exclude)) {
            return true;
        }
    }
}

class GoogleMapBasic_Controller extends Extension
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
            } else {
                $fileLocation = Config::inst()->get("GoogleMapBasic_Controller", "js_location");
                $idOfMapDiv = Config::inst()->get("GoogleMapBasic_Controller", "id_of_map_div");
                $apiKey = Config::inst()->get("GoogleMapBasic_Controller", "api_key");
                if (! $fileLocation) {
                    $fileLocation = 'googlemapbasic/javascript/GoogleMapBasic.js';
                }
                Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
                Requirements::javascript(Director::protocol() . 'maps.googleapis.com/maps/api/js?key='.$apiKey .'&amp;sensor=false');
                Requirements::javascript($fileLocation);
                $infoWindow = '<div class="infoWindowContent typography">'.$this->owner->InfoWindowContent.$this->GoogleMapBasicExternalLinkHTML().'</div>';
                Requirements::customScript("
                    if(typeof GoogleMapBasicOptions === 'undefined') {
                        var GoogleMapBasicOptions = new Array();
                    }
                    GoogleMapBasicOptions.push(
                        {
                            idOfMapDiv: \"".$this->cleanJS($idOfMapDiv)."\",
                            infoWindowContent: \"".$this->cleanJS($infoWindow)."\",
                            title: \"".$this->cleanJS($this->owner->Title)."\",
                            address: \"".$this->cleanJS($this->owner->Address)."\",
                            zoomLevel: ".intval($this->owner->ZoomLevel)."
                        }
                    );
                    ",
                    'GoogleMapBasicData'
                );
                Requirements::themedCSS('GoogleMapBasic', "googlemapbasic");
                return _t("GoolgeMapBasic.MAPLOADING", "map loading...");
            }
        }
        return false;
    }

    public function GoogleMapBasicStaticMapSource($width = 512, $height = 512)
    {
        $src = Director::protocol() . 'maps.googleapis.com/maps/api/staticmap?';
        $src .= 'center='.urlencode($this->owner->Address);
        $src .= '&amp;zoom='.$this->owner->ZoomLevel;
        $src .= '&amp;size='.$width.'x'.$height.'';
        $src .= '&amp;maptype=roadmap';
        $src .= '&amp;markers=color:red%7C'.urlencode(urlencode($this->owner->Address));
        $src .= '&amp;sensor=false';
        return $src;
    }

    public function GoogleMapBasicExternalLink()
    {
        if ($this->owner->HasGoogleMap()) {
            return Director::protocol() . 'maps.google.com/maps?q='.urlencode($this->owner->Address).'&amp;z='.$this->owner->ZoomLevel;
        }
    }

    public function GoogleMapBasicExternalLinkHTML()
    {
        if ($this->owner->HasGoogleMap()) {
            return '<p id="GoogleMapBasicExternalLink"><a href="'.$this->GoogleMapBasicExternalLink().'" target="_map">'._t("GoogleMapBasic.OPENINGOOGLEMAPS", "open in Google Maps").'</a></p>';
        }
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
