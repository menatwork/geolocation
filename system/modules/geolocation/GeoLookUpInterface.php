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
interface GeoLookUpInterface
{
    const IP = 1;
    const GEO = 2;
    const BOTH = 3;

    /**
     * @return GeolocationContainer
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation);
    
    /**
     * @return String Name of LookUp Service
     */
    public function getName();    
    
    /**
     * @param string $strLanguage
     * @return String - Description of this class.
     */
    public function getDescription();
    
    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType();
}

?>
