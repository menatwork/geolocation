<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
$GLOBALS['TL_LANG']['GEO']['internalIPDatabase']                = 'Interne IP-Datenbank';
$GLOBALS['TL_LANG']['GEO']['internalIPDatabaseDsr']             = 'Interne IP-Datenbank (3 Oktett)';
$GLOBALS['TL_LANG']['GEO']['openStreetMap']                     = 'OpenStreetMap (JSON)';
$GLOBALS['TL_LANG']['GEO']['websiteJSON']                       = 'Externer Service (JSON)';
$GLOBALS['TL_LANG']['GEO']['geoplugin']                         = 'geoplugin.net';
$GLOBALS['TL_LANG']['GEO']['your_country']                      = 'Ihr Land:';
$GLOBALS['TL_LANG']['GEO']['unknown_country']                   = 'Ihr Land konnte nicht ermittelt werden.';

/**
 * Text
 */
$GLOBALS['TL_LANG']['MSC']['GEO']['start']                     = 'Ihr Herkunftsland wird ermittelt.';
$GLOBALS['TL_LANG']['MSC']['GEO']['finished']                  = 'Ihr Herkunftsland wurde erfolgreich ermittelt und wird verarbeitet.';
$GLOBALS['TL_LANG']['MSC']['GEO']['changing']                  = 'Ihr Herkunftsland wird geändert.';
$GLOBALS['TL_LANG']['MSC']['GEO']['xx']                  	   = 'Kein Land';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['GEO']['noConnection']              = 'Es ist ein Fehler bei der Serververbindung aufgetreten.';
$GLOBALS['TL_LANG']['ERR']['GEO']['permissionDenied']          = 'Sie haben die Bestimmung Ihres Herkunftslandes verweigert.';
$GLOBALS['TL_LANG']['ERR']['GEO']['positionUnavailable']       = 'Ihr Herkunftsland konnte nicht ermittelt werden.';
$GLOBALS['TL_LANG']['ERR']['GEO']['timeOut']                   = 'Zeitüberschreitung.';
$GLOBALS['TL_LANG']['ERR']['GEO']['unsupportedBrowser']        = 'Ihr Browser unterstützt nicht die Standortbestimmung Ihres Herkunftslandes.';
$GLOBALS['TL_LANG']['ERR']['GEO']['unknownError']              = 'Unbekannter Fehler.';
$GLOBALS['TL_LANG']['ERR']['GEO']['includeCache']              = 'Ein aktivierter Cache kann die Geolokalisierung negativ beeinträchtigen.';