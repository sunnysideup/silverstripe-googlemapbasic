<?php

/**
 *@author nicolaas[at] sunnysideup.co.nz
 *
 *
 **/


class GoogleMapBasic extends SiteTreeExtension {

	static $db = array(
		'ShowMap' => 'Boolean',
		'StaticMap' => 'Boolean',
		'Address' => 'Text',
		'ZoomLevel' => 'Int',
		'InfoWindowContent' => 'HTMLText'
	);

	protected static $key_lookup = array();
		static function set_key($s, $url = 0) {self::$key_lookup[$url] = $s;}
		static function get_key($url = 0) {
			if(!isset(self::$key_lookup[$url])) {

				user_error("No Google Map API key set for &quot;".$url."&quot;, existing ones are: ".implode(", ", array_flip(self::$key_lookup)), E_USER_NOTICE);
				if(count(self::$key_lookup)) {
					return array_pop(self::$key_lookup);
				}
			}
			return self::$key_lookup[$url];
		}
		static function add_key($key, $url) {self::$key_lookup[$url] = $key;}

	protected static $js_location = '';
		static function set_js_location($s) {self::$js_location = $s;}
		static function get_js_location() {return self::$js_location;}

	protected static $include_in_classes = array();
		static function set_include_in_classes($a) {self::$include_in_classes = $a;}
		static function get_include_in_classes() {return self::$include_in_classes;}

	protected static $exclude_from_classes = array();
		static function set_exclude_from_classes($a) {self::$exclude_from_classes = $a;}
		static function get_exclude_from_classes() {return self::$exclude_from_classes;}

	function updateCMSFields(FieldList $fields) {
		if($this->canHaveMap()) {
			$fields->addFieldToTab("Root.Map", new CheckboxField("ShowMap", "Show map (reload to see additional options)"));
			if($this->owner->ShowMap) {
				$fields->addFieldToTab("Root.Map", new CheckboxField("StaticMap", "Show map as picture only"));
				$fields->addFieldToTab("Root.Map", new TextField("Address"));
				$fields->addFieldToTab("Root.Map", new NumericField("ZoomLevel", "Zoom (1 = world, 20 = too close)"));
				$fields->addFieldToTab("Root.Map", $htmlEditorField = new HtmlEditorField("InfoWindowContent", "Info Window Content"));
				$htmlEditorField->setRows(5);
			}
		}
	}

	protected function canHaveMap() {
		$include = self::get_include_in_classes();
		$exclude = self::get_exclude_from_classes();
		if(!is_array($exclude) || !is_array($include)) {
			user_error("include or exclude classes is NOT an array", E_USER_NOTICE);
			return true;
		}
		if(!count($include) && !count($exclude)) {
			return true;
		}
		if(count($include) && in_array($this->owner->ClassName, $include)) {
			return true;
		}
		if(count($exclude) && !in_array($this->owner->ClassName, $exclude)) {
			return true;
		}
	}


}

class GoogleMapBasic_Controller extends Extension {

	function GoogleMapBasic() {
		if($this->owner->ShowMap && $this->owner->Address) {
			if($this->owner->StaticMap) {
				return true;
			}
			else {
				$fileLocation = GoogleMapBasic::get_js_location();
				if(! $fileLocation) {
					$fileLocation = 'googlemapbasic/javascript/GoogleMapBasic.js';
				}
				Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
				Requirements::javascript(Director::protocol() . 'maps.googleapis.com/maps/api/js?sensor=false');
				Requirements::javascript($fileLocation);
				$infoWindow = '<div id="InfoWindowContent">'.$this->owner->InfoWindowContent.$this->GoogleMapBasicExternalLinkHTML().'</div>';
				Requirements::customScript("
					GoogleMapBasic.SET_infoWindowContent(\"".$this->cleanJS($infoWindow)."\");
					GoogleMapBasic.SET_title(\"".$this->cleanJS($this->owner->Title)."\");
					GoogleMapBasic.SET_address(\"".$this->cleanJS($this->owner->Address)."\");
					GoogleMapBasic.SET_zoomLevel(".intval($this->owner->ZoomLevel).");"
					, 'GoogleMapBasicData'
				);
				Requirements::themedCSS('GoogleMapBasic');
				return _t("GoolgeMapBasic.MAPLOADING", "map loading...");
			}
		}
		return false;
	}

	function GoogleMapBasicStaticMapSource($width = 512, $height = 512) {
		$src = Director::protocol() . 'maps.googleapis.com/maps/api/staticmap?';
		$src .= 'center='.urlencode($this->owner->Address);
		$src .= '&amp;zoom='.$this->owner->ZoomLevel;
		$src .= '&amp;size='.$width.'x'.$height.'';
		$src .= '&amp;maptype=roadmap';
		$src .= '&amp;markers=color:red%7C'.urlencode(urlencode($this->owner->Address));
		$src .= '&amp;sensor=false';
		return $src;
	}

	function GoogleMapBasicExternalLink () {
		if($this->owner->ShowMap && $this->owner->Address) {
			return Director::protocol() . 'maps.google.com/maps?q='.urlencode($this->owner->Address).'&amp;z='.$this->owner->ZoomLevel;
		}
	}

	function GoogleMapBasicExternalLinkHTML () {
		if($this->owner->ShowMap && $this->owner->Address) {
			return '<p id="GoogleMapBasicExternalLink"><a href="'.$this->GoogleMapBasicExternalLink().'" target="_map">'._t("GoogleMapBasic.OPENINGOOGLEMAPS", "open in Google Maps").'</a></p>';
		}
	}

	function cleanJS($s) {
		$s = Convert::raw2js($s);
		$s = str_replace("\r\n", " ", $s);
		$s = str_replace("\n", " ", $s);
		$s = str_replace('/', '\/', $s);
		return $s;
	}



}
