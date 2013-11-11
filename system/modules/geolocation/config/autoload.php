<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Geolocation
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'GeoLookUpInterface'     => 'system/modules/geolocation/GeoLookUpInterface.php',
	// Cron
	'UpdateGeoData'          => 'system/modules/geolocation/cron/UpdateGeoData.php',
	'Geolocation'            => 'system/modules/geolocation/Geolocation.php',
	'GeolocationContainer'   => 'system/modules/geolocation/GeolocationContainer.php',
	'GeoLookUpWebsiteJson'   => 'system/modules/geolocation/GeoLookUpWebsiteJson.php',
	'GeoLookUpInternIP'      => 'system/modules/geolocation/GeoLookUpInternIP.php',
	'GeoLookUpFactory'       => 'system/modules/geolocation/GeoLookUpFactory.php',
	'GeolocationLatLng'      => 'system/modules/geolocation/GeolocationLatLng.php',
	'ModuleGeolocation'      => 'system/modules/geolocation/ModuleGeolocation.php',
	'GeoLookUpGeoplugin'     => 'system/modules/geolocation/GeoLookUpGeoplugin.php',
	'GeoLookUpInternIPDsr'   => 'system/modules/geolocation/GeoLookUpInternIPDsr.php',
	'GeoLookUpOpenStreetMap' => 'system/modules/geolocation/GeoLookUpOpenStreetMap.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_geo_default' => 'system/modules/geolocation/templates',
));
