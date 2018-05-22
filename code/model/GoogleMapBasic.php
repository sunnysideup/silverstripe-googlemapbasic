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
        'Lat' => 'Float',
        'Lng' => 'Float',
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
                $fields->addFieldsToTab(
                    "Root.Map",
                    [
                        CheckboxField::create("StaticMap", "Show map as picture only"),
                        TextField::create("Address"),
                        NumericField::create("ZoomLevel", "Zoom (1 = world, 20 = too close)"),
                        NumericField::create("Lat", "Latitude")
                            ->setRightTitle('Optional, use in conjunction with Longitude if address is not accurate enough.'),
                        NumericField::create("Lng", "Longitude")
                            ->setRightTitle('Optional, use in conjunction with Latitude if address is not accurate enough.'),
                        HtmlEditorField::create("InfoWindowContent", "Info Window Content")->setRows(5)
                    ]
                );
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
