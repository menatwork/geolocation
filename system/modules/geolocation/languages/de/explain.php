<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][0] = array('Externer Service (JSON)', 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][1] = array('OpenStreetMap (JSON)', 'Es wird per OpenStreetMap der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die Lat/Lon Werte platziert werden.<br /><br /><strong>Beispiel:</strong><br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s'); 
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][0]  = array('Interne IP-Datenbank', 'Es wird über eine interne Datenbank die IP des Besuchers einem Land zugeordnet.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][1]  = array('Interne IP-Datenbank (3 Oktett)', 'Es wird über eine interne Datenbank die IP des Besuchers einem Land zugeordnet. Allerdings werden nur die ersten drei Oktett der IP Adresse dafür verwendet.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][2]  = array('geoplugin.net', 'Es wird für die Standortbestimmung des Besuchers die folgende Konfiguration benötigt:<br /> http://www.geoplugin.net/php.gp?ip=%s');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][3]  = array('Externer Service (JSON)', 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.');

?>