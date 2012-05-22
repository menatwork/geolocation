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

/**
 * Reference
 */
$GLOBALS['TL_LANG']['GEO']['internalIPDatabase']                = 'Internal IP-Database';
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