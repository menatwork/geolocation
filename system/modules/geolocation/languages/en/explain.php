<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][0] = array('External service (JSON)', 'An external service will be used to detect the location of the user. To configure this service, please enter the URL of the service and use the placeholder "%s" for the ip address or lat/long values.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][1] = array('OpenStreetMap (JSON)', 'The location of the user will be detected by OpenStreetMap. To configure this service, please enter the URL and use "%s" as a placeholder for the lat/long values.<br /><br /><strong>Example:</strong><br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s'); 
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][0]  = array('Internal IP-Database', 'The internal database will be used to map the user\'s ip to a country.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][1]  = array('Internal IP-Database (3 octet)', 'The internal database will be used to map the user\'s ip to a country. However, only the first three octets of the IP address are used.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][2]  = array('geoplugin.net', 'The following configuration is necessary for this service:<br /> http://www.geoplugin.net/php.gp?ip=%s');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][3]  = array('External service (JSON)', 'Geolocation will use an external service to detect the location of the user. To configure the service, please enter the URL and use the placeholder "%s" for the IP adress to look up.');
