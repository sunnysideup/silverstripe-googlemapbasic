<?php

/**
 *@author nicolaas[at] sunnysideup.co.nz
 *
 *
 **/


class GoogleMapBasic extends DataObjectDecorator {

	public function extraStatics() {
		return array (
			'db' => array(
				'ShowMap' => 'Boolean',
				'Address' => 'Text',
				'InfoWindowContent' => 'HTMLText'
			)
		);
	}


	protected static $key = '';
		static function set_key($v) {self::$key = $v;}
		static function get_key() {return self::$key;}

	protected static $js_location = '';
		static function set_js_location($v) {self::$js_location = $v;}
		static function get_js_location() {return self::$js_location;}

	protected static $include_in_classes = array();
		static function set_include_in_classes($a) {self::$include_in_classes = $a;}
		static function get_include_in_classes() {return self::$include_in_classes;}

	protected static $exclude_from_classes = array();
		static function set_exclude_from_classes($a) {self::$exclude_from_classes = $a;}
		static function get_exclude_from_classes() {return self::$exclude_from_classes;}

	function updateCMSFields(FieldSet &$fields) {
		if($this->canHaveMap()) {
			$fields->addFieldToTab("Root.Content.Map", new CheckboxField("ShowMap", "Show map (reload to see additional options)"));
			if($this->owner->ShowMap) {
				$fields->addFieldToTab("Root.Content.Map", new TextField("Address"));
				$fields->addFieldToTab("Root.Content.Map", new HtmlEditorField("InfoWindowContent", "Info Window Content", 5));
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
			$fileLocation = GoogleMapBasic::get_js_location();
			if(!$fileLocation) {
				$fileLocation = 'googlemapbasic/javascript/GoogleMapBasic.js';
			}
			$infoWindow = '<div id="InfoWindowContent">'.$this->owner->InfoWindowContent.'</div>';
			$infoWindow = str_replace("\r\n", " ", $infoWindow);
			$infoWindow = str_replace("\n", " ", $infoWindow);
			Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
			Requirements::customScript("var GoogleMapBasicInfoWindow = \"".Convert::raw2js($infoWindow)."\"", 'GoogleMapBasicInfoWindow');
			Requirements::customScript("var GoogleMapBasicAddress = \"".Convert::raw2js($this->owner->Address)."\"", 'GoogleMapBasicAddress');
			Requirements::javascript('http://maps.google.com/maps?file=api&v=2&sensor=false&key='.GoogleMapBasic::get_key());
			Requirements::javascript($fileLocation);
			Requirements::themedCSS('GoogleMapBasic');
			return $this->owner->renderWith("GoogleMapBasic");
		}
	}

}
