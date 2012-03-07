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
 * @copyright  MEN AT WORK 2011-2012 
 * @package    Language
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Translation for JS 
 */
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_msc_Start']               = '<img src="system/modules/geolocation/html/img/ajax-loader.gif" /> Versuch Standtort zu ermitteln.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_msc_Finished']            = 'Standort Ermittelung fertiggestellt.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_msc_Changing']            = '<img src="system/modules/geolocation/html/img/ajax-loader.gif" /> Standort wird geändert.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_NoConnection']        = 'Fehler beim Verbinden zum Server.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_PremissionDenied']    = 'Erlaubnis verwehrt.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_PositionUnavailable'] = 'Position konnte nicht ermittelt werden.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_TimeOut']             = 'Zeit überschreitung.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_UnsupportedBrowser']  = 'Ihr Browser ist zu alt. Kaufen Sie sich einen neuen Rechner.';
$GLOBALS['TL_LANG']['Geolocation']['js']['geo_err_UnknownError']        = 'Unbekannter Fehler.';

/**
 * Translation for LookUp Services
 */
$GLOBALS['TL_LANG']['Geolocation']['lu']['InternIP']        = array('Interner IP Lookup', 'Über eine interne Datenbank wird versucht einer IP einem Land zuzuweisen.');
$GLOBALS['TL_LANG']['Geolocation']['lu']['WebsiteJson']     = array('Webseiten Abfrage - JSON', 'Es wird versuch ein Anfrage an eine Webseite zu senden und einer Antwort zu inerprtieren. Als Konfiguration muss die Adresse der Seite mit Platzhaltern "%s" zu versehen an der die IP Adresse oder Lat/Lon Werte plaziert werden sollen.');
$GLOBALS['TL_LANG']['Geolocation']['lu']['OpenStreetMap']   = array('Open Street Map - JSON', 'Es wird versucht per Open Street Map das Land zu ermitteln.Als Konfiguration muss die Adresse, sowie die Platzhalter ("%s") für die Lat/Lon Wert angegeben werden.<br />Beispiel:<br />http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s');

/**
 * Translation for module
 */
$GLOBALS['TL_LANG']['Geolocation']['module'] = "";

