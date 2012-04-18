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
$GLOBALS['TL_LANG']['tl_page']['geo_legend']        = 'Geolocation-Einstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['geo_single_page']   = array('Geolocation auf dieser Seite einbinden', 'Hier können Sie das Varhalten der aktuellen Seite einstellen.');
$GLOBALS['TL_LANG']['tl_page']['geo_child_page']    = array('Geolocation auf allen Unterseiten einbinden', 'Hier können Sie das Verhalten aller Unterseiten einstellen.');

$GLOBALS['TL_LANG']['tl_page']['geo_single_choose'] = array('Lokalisierungsoptionen', 'Hier können Sie einstellen, welche Methode benutzt werden soll, um einen Benutzer zu lokalisieren.');
$GLOBALS['TL_LANG']['tl_page']['geo_child_choose']  = array('Lokalisierungsoptionen', 'Hier können Sie einstellen, welche Methode benutzt werden soll, um einen Benutzer zu lokalisieren.');

$GLOBALS['TL_LANG']['tl_page']['geo_w3c']           = array('W3C Lokalisierung', 'Wählen Sie diese Option wenn eine lokalisierung über die W3C API erfolgen soll.');
$GLOBALS['TL_LANG']['tl_page']['geo_ip']            = array('IP Lokalisierung', 'Wählen Sie diese Option wenn eine lokalisierung über die IP erfolgen soll.');
$GLOBALS['TL_LANG']['tl_page']['geo_fallback']      = array('Fallback', 'Wählen Sie diese Option, wenn ein Fallback gesetzt werden soll, wenn keine andere Funktion ein Ergebnis liefert.');
