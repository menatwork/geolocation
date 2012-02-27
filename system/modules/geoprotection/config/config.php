<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  MEN AT WORK 2011
 * @package    GeoProtection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Register hook 
 */
$GLOBALS['TL_HOOKS']['getContentElement'][] = array('GeoProtection', 'checkPermission');
$GLOBALS['TL_HOOKS']['dispatchAjax'][] = array('GeoProtection', 'dispatchAjax');

// Set JS for geolocation
$objSession = Session::getInstance();
$arrGeoProtection = $objSession->get("geoprotection");

if (TL_MODE == 'FE' && (!is_array($arrGeoProtection) || $arrGeoProtection["geolocated"] == false))
{
    $GLOBALS['TL_JAVASCRIPT'][] = "system/modules/geoprotection/html/js/GeoCore.js";
}


?>