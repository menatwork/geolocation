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
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Register hook 
 */
$GLOBALS['TL_HOOKS']['dispatchAjax'][] = array('Geolocation', 'dispatchAjax');
$GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('Geolocation', 'insertJSVars');

/**
 * Set JS/Hook for geolocation 
 */
if (TL_MODE == 'FE')
{
    $objUserGeolocation = Geolocation::getInstance()->getUserGeolocation();

    // Include js for W3C Geolocation when no user cahnge, W3C geolocation, ip lookup or fallback was done. 
    if ((!$objUserGeolocation->isChangeByUser() && !$objUserGeolocation->isGeolocated() && !$objUserGeolocation->isIPLookup() && !$objUserGeolocation->isFallback() && !$objUserGeolocation->isDeactivated()))
    {
        $GLOBALS['TL_JAVASCRIPT'][] = "system/modules/geolocation/html/js/geoCore.js";
    }
}

/**
 * Add FE Mod
 */
$GLOBALS['FE_MOD']['miscellaneous']['geolocation'] = 'ModuleGeolocation';

?>