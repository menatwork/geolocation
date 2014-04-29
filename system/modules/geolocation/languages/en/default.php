<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Reference
 */
$GLOBALS['TL_LANG']['GEO']['internalIPDatabase']                = 'Internal IP-Database';
$GLOBALS['TL_LANG']['GEO']['internalIPDatabaseDsr']             = 'Internal IP-Database (3 octet)';
$GLOBALS['TL_LANG']['GEO']['openStreetMap']                     = 'OpenStreetMap (JSON)';
$GLOBALS['TL_LANG']['GEO']['websiteJSON']                       = 'External service (JSON)';
$GLOBALS['TL_LANG']['GEO']['geoplugin']                         = 'geoplugin.net';
$GLOBALS['TL_LANG']['GEO']['your_country']                      = 'Your country of origin:';
$GLOBALS['TL_LANG']['GEO']['unknown_country']                   = 'Your country of origin could not be detected.';

/**
 * Text
 */
$GLOBALS['TL_LANG']['MSC']['GEO']['start']                     = 'Detecting your country.';
$GLOBALS['TL_LANG']['MSC']['GEO']['finished']                  = 'Your country of origin was successfully detected and is now being processed.';
$GLOBALS['TL_LANG']['MSC']['GEO']['changing']                  = 'Changing your country of origin.';
$GLOBALS['TL_LANG']['MSC']['GEO']['xx']                        = 'No country';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['GEO']['noConnection']              = 'The connnection to the server could not be established.';
$GLOBALS['TL_LANG']['ERR']['GEO']['permissionDenied']          = 'You have refused to identify your country of origin.';
$GLOBALS['TL_LANG']['ERR']['GEO']['positionUnavailable']       = 'Your country could not be detected.';
$GLOBALS['TL_LANG']['ERR']['GEO']['timeOut']                   = 'Connection timeout.';
$GLOBALS['TL_LANG']['ERR']['GEO']['unsupportedBrowser']        = 'Your browser does not support the geolocation API.';
$GLOBALS['TL_LANG']['ERR']['GEO']['unknownError']              = 'Unknown error.';
$GLOBALS['TL_LANG']['ERR']['GEO']['includeCache']              = 'Enabling the cache may affect negatively the geolocation.';