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
$GLOBALS['TL_LANG']['tl_settings']['geo_legend']                    = 'Geolocation settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps_headline']      = array('Custom ip adresses','Here you can enter a comma separated list of custom ip addresses.');
$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps']               = array('IP addresses','');
$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback']           = array('Country fallback', 'Please choose a country as fallback, when no mapping of the ip address is possible.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customOverride']            = array('Use custom ip adresses', 'Manual mapping of custom ip addresses to a country.');
$GLOBALS['TL_LANG']['tl_settings']['geo_customCountryFallback']     = array('Custom country fallback', 'Choose a country as a fallback for the custom ip addresses.');
$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration']            = array('Cookie life time in days', 'Here you can set the cookie lifetime. The left value is the lifetime of the values detected by the w3c API, the right value is the lifetime of the values, entered manually by the user.');

$GLOBALS['TL_LANG']['tl_settings']['geo_IPlookUpSettings']          = array('IP service', 'Here you can choose the service for mapping the ip address to a country.'); 
$GLOBALS['TL_LANG']['tl_settings']['geo_GeolookUpSettings']         = array('W3C service', 'Here you can choose the service for mapping the lat/long values to a country.'); 
$GLOBALS['TL_LANG']['tl_settings']['lookUpConfig']                  = array('Service URL', 'Please enter the configuration string.');
$GLOBALS['TL_LANG']['tl_settings']['lookUpClass']                   = array('Service class', 'Choose the lookup service.');
