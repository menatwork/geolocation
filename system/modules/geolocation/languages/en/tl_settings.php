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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_legend']                    = 'Geolocation-Einstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps_headline']      = array('Benutzerdefinierte IP-Adressen','Hier können Sie eine kommagetrennte Liste von benutzerdefinierten IP-Adressen eingeben.');
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps']               = array('IP-Adressen','');
$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback']           = array('Länder-Fallback', 'Wählen Sie hier das Fallback-Land, wenn die IP-Adresse keinem Land zugeordnet werden kann.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customOverride']            = array('Benutzerdefinierte IP-Adressen verwenden', 'Klicken Sie hier um benutzerdefinierte Einstellungen zu konfigurieren.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customCountryFallback']     = array('Benutzerdefiniertes Länder-Fallback', 'Wählen Sie hier das Fallback-Land aus, das für die benutzerdefinierte IP-Adresse gelten soll.');
$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration']            = array('Cookie Lebenszeit', 'Hier können Sie die Lebenszeit der Cookies setzen. Links für Cookies die per W3C Gelocation ermittelt wurden, rechts für Einstellungen, die vom Benutzer getroffen wurden.');

// LookUp Services
$GLOBALS['TL_LANG']['tl_settings']['geo_IPlookUpSettings']          = array('IP-Service', 'Hier können Sie einstellen, welcher Dienst benutzt werden soll, um die IP-Adresse in einen Standort aufzulösen.'); 
$GLOBALS['TL_LANG']['tl_settings']['geo_GeolookUpSettings']         = array('W3C-Service', 'Hier können Sie einstellen, welcher Dienst benutzt werden soll, um Lat/Lon in einen Standort aufzulösen.'); 
$GLOBALS['TL_LANG']['tl_settings']['lookUpConfig']                  = array('Service-URL', 'Geben Sie hier einen String für die Konfiguration ein.');
$GLOBALS['TL_LANG']['tl_settings']['lookUpClass']                   = array('Klasse', 'Wählen Sie den Service aus, der ausgeführt werden soll.');
