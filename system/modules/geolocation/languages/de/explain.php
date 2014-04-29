<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/geolocation/language/de/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2013-11-13T10:54:42+01:00
 */


$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo']['0']['0'] = 'Externer Service (JSON)';
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo']['0']['1'] = 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.';
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo']['1']['0'] = 'OpenStreetMap (JSON)';
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo']['1']['1'] = 'Es wird per OpenStreetMap der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die Lat/Lon Werte platziert werden.<br /><br /><strong>Beispiel:</strong><br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['0']['0']  = 'Interne IP-Datenbank';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['0']['1']  = 'Es wird über eine interne Datenbank die IP des Besuchers einem Land zugeordnet.';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['1']['0']  = 'Interne IP-Datenbank (3 Oktett)';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['1']['1']  = 'Es wird über eine interne Datenbank die IP des Besuchers einem Land zugeordnet. Allerdings werden nur die ersten drei Oktett der IP Adresse dafür verwendet.';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['2']['0']  = 'geoplugin.net';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['2']['1']  = 'Es wird für die Standortbestimmung des Besuchers die folgende Konfiguration benötigt:<br /> http://www.geoplugin.net/php.gp?ip=%s';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['3']['0']  = 'Externer Service (JSON)';
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP']['3']['1']  = 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.';

