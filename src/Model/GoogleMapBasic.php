<?php

namespace Sunnysideup\GooglemapBasic\Model;

use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

/**
 * Class \Sunnysideup\GooglemapBasic\Model\GoogleMapBasic
 *
 * @property bool $ShowMap
 * @property bool $StaticMap
 * @property string $Address
 * @property float $Lat
 * @property float $Lng
 * @property int $ZoomLevel
 * @property string $InfoWindowContent
 */
class GoogleMapBasic extends SiteTreeExtension
{
    private static $db = [
        'ShowMap' => 'Boolean',
        'StaticMap' => 'Boolean',
        'Address' => 'Text',
        'Lat' => 'Decimal(12,9)',
        'Lng' => 'Decimal(12,9)',
        'ZoomLevel' => 'Int',
        'InfoWindowContent' => 'HTMLText',
    ];

    private static $include_in_classes = [];

    private static $exclude_from_classes = [];

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->canHaveMap()) {
            $reloadMessage = ' ';
            if (! $this->getOwner()->ShowMap) {
                $reloadMessage = ' (save (and publish) to see additional options)';
            }
            $fields->addFieldToTab('Root.Map', new CheckboxField('ShowMap', "Show map {$reloadMessage}"));
            if ($this->getOwner()->ShowMap) {
                $fields->addFieldsToTab(
                    'Root.Map',
                    [
                        CheckboxField::create('StaticMap', 'Show map as picture only'),
                        TextField::create('Address'),
                        NumericField::create('ZoomLevel', 'Zoom (1 = world, 20 = too close)'),
                        NumericField::create('Lat', 'Latitude')
                            ->setDescription('Optional, use in conjunction with Longitude if address is not accurate enough.')
                            ->setScale(9),
                        NumericField::create('Lng', 'Longitude')
                            ->setDescription('Optional, use in conjunction with Latitude if address is not accurate enough.')
                            ->setScale(9),
                        HTMLEditorField::create('InfoWindowContent', 'Info Window Content')->setRows(5),
                    ]
                );
            }
        }
    }

    protected function canHaveMap()
    {
        $include = Config::inst()->get(GoogleMapBasic::class, 'include_in_classes');
        $exclude = Config::inst()->get(GoogleMapBasic::class, 'exclude_from_classes');
        if (! is_array($exclude) || ! is_array($include)) {
            user_error('include or exclude classes is NOT an array', E_USER_NOTICE);

            return true;
        }
        if (! count($include) && ! count($exclude)) {
            return true;
        }
        if (count($include) && in_array($this->getOwner()->ClassName, $include, true)) {
            return true;
        }
        if (count($exclude) && ! in_array($this->getOwner()->ClassName, $exclude, true)) {
            return true;
        }
    }
}
