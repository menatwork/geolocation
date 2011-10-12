<?php
if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
 * Class GeoProptection
 *
 * Provide methods for GeoProtection.
 * @copyright  MEN AT WORK 2011
 * @package    Controller
 */
class GeoProtection extends Frontend
{

    /**
     * IP cache array
     * @var array
     */
    private static $arrIPCache = array();

    public function checkPermission($objElement, $strBuffer)
    {
        //check if geoprotection is enabled
        if ($objElement->gp_protected && TL_MODE != 'BE')
        {
            $arrIpAddress = array();
            foreach (deserialize($GLOBALS['TL_CONFIG']['overrideIps']) as $ip)
            {
                $arrIpAddress[] = $ip['ipAddress'];
            }

            //show if gp_override is on and IP is valid
            if ($objElement->gp_protected && (
                    ($GLOBALS['TL_CONFIG']['customOverrideGp'])
                    && (in_array($this->Environment->ip, $arrIpAddress))
                    && ($GLOBALS['TL_CONFIG']['customCountryFallback'] == '')))
            {
                return $strBuffer;
            }

            //calculate ipNum
            $arrIP = explode(".", $this->Environment->ip);
            //$arrIP = explode(".", '92.50.97.80');
            $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

            if (($GLOBALS['TL_CONFIG']['customOverrideGp'])
                    && (in_array($this->Environment->ip, $arrIpAddress)))
            {
                self::$arrIPCache[$ipNum] = ($GLOBALS['TL_CONFIG']['customCountryFallback']) ? strtolower($GLOBALS['TL_CONFIG']['customCountryFallback']) : $GLOBALS['TL_CONFIG']['countryFallback'];
            }

            // Load country from cache or do a db-lookup
            if (!isset(self::$arrIPCache[$ipNum]))
            {
                // Initialize cache
                self::$arrIPCache[$ipNum] = '';

                $country = $this->Database->prepare("SELECT country_short FROM tl_geodata WHERE ? >= ipnum_start AND ? <= ipnum_end")
                        ->limit(1)
                        ->execute($ipNum, $ipNum);

                self::$arrIPCache[$ipNum] = ($country->country_short) ? strtolower($country->country_short) : $GLOBALS['TL_CONFIG']['countryFallback'];
            }
            
            if ($objElement->gp_mode == "gp_hide")
            {
                return (in_array(self::$arrIPCache[$ipNum], deserialize($objElement->gp_countries))) ? '' : $strBuffer;
            }

            return (in_array(self::$arrIPCache[$ipNum], deserialize($objElement->gp_countries))) ? $strBuffer : '';
        }

        return $strBuffer;
    }

}

?>