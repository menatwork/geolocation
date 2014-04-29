<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_legend']                    = 'Geolocation settings';
$GLOBALS['TL_LANG']['tl_settings']['geo_cookie_legend']             = 'Geolocation cookie settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps_headline']      = array('Custom ip adresses','Here you can enter a comma separated list of custom ip addresses.');
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps']               = array('IP addresses','');
$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback']           = array('Country fallback', 'Please choose a country as fallback, when no mapping of the ip address is possible.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customOverride']            = array('Use custom ip adresses', 'Manual mapping of custom ip addresses to a country.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customCountryFallback']     = array('Custom country fallback', 'Choose a country as a fallback for the custom ip addresses.');
$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration']            = array('Cookie life time in days', '1. Cookies that were determined by W3C Gelocation, 2. Settings that have been made ​​by the user, 3. If no country could be determined.');
$GLOBALS['TL_LANG']['tl_settings']['geo_cookieName']                = array('Name of the cookie', 'Here you can add a name for the cookie. If blank the default name will be used e.g. geolocation.');

$GLOBALS['TL_LANG']['tl_settings']['geo_IPlookUpSettings']          = array('IP service', 'Here you can choose the service for mapping the ip address to a country.'); 
$GLOBALS['TL_LANG']['tl_settings']['geo_GeolookUpSettings']         = array('W3C service', 'Here you can choose the service for mapping the lat/long values to a country.'); 
$GLOBALS['TL_LANG']['tl_settings']['lookUpConfig']                  = array('Service URL', 'Please enter the configuration string.');
$GLOBALS['TL_LANG']['tl_settings']['lookUpClass']                   = array('Service class', 'Choose the lookup service.');
