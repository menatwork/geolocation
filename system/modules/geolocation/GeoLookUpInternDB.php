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
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Class GeoProLookUpInterface
 *
 * Provide methods for decoding msg from look up services.
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 */
class GeoLookUpInternDB extends Backend implements GeoLookUpInterface
{

    /**
     * IP cache array
     * @var array
     */
    private static $arrIPCache = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return String shortTag of location
     */
    public function getLocation($strConfig, $strLat, $strLon, $strIP)
    {
        //calculate ipNum
        $arrIP = explode(".", $strIP);
        $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

        // Load country from cache or do a db-lookup
        if (!isset(self::$arrIPCache[$ipNum]))
        {
            // Initialize cache
            self::$arrIPCache[$ipNum] = '';

            $country = $this->Database->prepare("SELECT country_short FROM tl_geodata WHERE ? >= ipnum_start AND ? <= ipnum_end")
                    ->limit(1)
                    ->execute($ipNum, $ipNum);
            
            if($country->numRows != 0 )
            {
                self::$arrIPCache[$ipNum] = $country->country_short;
                return $country->country_short;
            }
            else 
            {
                return false;
            }            
        }
        else
        {
            return self::$arrIPCache[$ipNum];
        }
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return "Intern DB Look UP";
    }

    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType()
    {
        return 1;
    }

}

?>
