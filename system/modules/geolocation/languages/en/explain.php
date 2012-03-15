<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2012
 * @package    Language
 * @license    GNU/LGPL 
 * @filesource
 */

$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][0]         = array('Externer Service (JSON)', 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][1]         = array('OpenStreetMap (JSON)', 'Es wird per OpenStreetMap der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die Lat/Lon Werte platziert werden.<br /><br /><strong>Beispiel:</strong><br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s'); 
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][0]          = array('Interne IP-Datenbank', 'Es wird über eine interne Datenbank die IP des Besuchers einem Land zugeordnet.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][1]          = array('geoplugin.net', 'Es wird für die Standortbestimmung des Besuchers die folgende Konfiguration benötigt:<br /> http://www.geoplugin.net/php.gp?ip=%s');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][2]          = array('Externer Service (JSON)', 'Es wird über einen externen Service der Standort des Besuchers ermittelt. Als Konfiguration muss die Adresse des externen Services mit Platzhaltern "%s" versehen werden, in der die IP-Adresse oder Lat/Lon Werte platziert werden.');

?>