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

$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][0] = array('External service (JSON)', 'An external service will be used to detect the location of the user. To configure this service, please enter the URL of the service and use the placeholder "%s" for the ip address or lat/long values.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][1] = array('OpenStreetMap (JSON)', 'The location of the user will be detected by OpenStreetMap. To configure this service, please enter the URL and use "%s" as a placeholder for the lat/long values.<br /><br /><strong>Example:</strong><br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s'); 
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][0]  = array('Internal IP-Database', 'The internal database will be used to map the user\'s ip to a country.');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][1]  = array('geoplugin.net', 'The following configuration is necessary for this service:<br /> http://www.geoplugin.net/php.gp?ip=%s');
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][2]  = array('External service (JSON)', 'Geolocation will use an external service to detect the location of the user. To configure the service, please enter the URL and use the placeholder "%s" for the IP adress to look up.');

?>