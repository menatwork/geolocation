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