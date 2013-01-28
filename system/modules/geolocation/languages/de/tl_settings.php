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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_legend']                    = 'Geolokalisierungs-Einstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps_headline']      = array('Benutzerdefinierte IP-Adressen','Hier können Sie eine kommagetrennte Liste von benutzerdefinierten IP-Adressen eingeben.');
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps']               = array('IP-Adressen','');
$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback']           = array('Länder-Fallback', 'Wählen Sie hier das Fallback-Land, wenn die IP-Adresse keinem Land zugeordnet werden kann.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customOverride']            = array('Benutzerdefinierte IP-Adressen verwenden', 'Klicken Sie hier um benutzerdefinierte Einstellungen zu konfigurieren.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customCountryFallback']     = array('Benutzerdefiniertes Länder-Fallback', 'Wählen Sie hier das Fallback-Land aus, das für die benutzerdefinierte IP-Adresse gelten soll.');
$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration']            = array('Cookie Lebenszeit in Tagen', '1. Cookies die per W3C Gelocation ermittelt wurden, 2. Einstellungen, die vom Benutzer getroffen wurden, 3. Wenn kein Land ermittelt werden konnte.');

$GLOBALS['TL_LANG']['tl_settings']['geo_IPlookUpSettings']          = array('IP-Service', 'Hier können Sie einstellen, welcher Dienst benutzt werden soll, um die IP-Adresse in einen Standort aufzulösen.'); 
$GLOBALS['TL_LANG']['tl_settings']['geo_GeolookUpSettings']         = array('W3C-Service', 'Hier können Sie einstellen, welcher Dienst benutzt werden soll, um Lat/Lon in einen Standort aufzulösen.'); 
$GLOBALS['TL_LANG']['tl_settings']['lookUpConfig']                  = array('Service-URL', 'Geben Sie hier einen String für die Konfiguration ein.');
$GLOBALS['TL_LANG']['tl_settings']['lookUpClass']                   = array('Klasse', 'Wählen Sie den Service aus, der ausgeführt werden soll.');
