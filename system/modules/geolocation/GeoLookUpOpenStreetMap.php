<?php

if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
class GeoLookUpOpenStreetMap extends Backend implements GeoLookUpInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return GeolocationContainer
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation)
    {
        $objRequest = new Request();
        $objRequest->send(vsprintf($strConfig, array($objGeolocation->getLat(), $objGeolocation->getLon())));

        if ($objRequest->code != 200)
        {
            $this->log("Error by location service: " . vsprintf("Request error code %s - %s ", array($objRequest->code, $objRequest->error)), __CLASS__ . " | " . __FUNCTION__, __FUNCTION__);
            return false;
        }

        $arrJson = json_decode($objRequest->response, true);

        if (!is_array($arrJson))
        {
            $this->log("Response is not a array.", __CLASS__ . " | " . __FUNCTION__, __FUNCTION__);
            return false;
        }
        
        $arrCountries = $this->getCountries();
        
        $strCountryShort = $arrJson['address']['country_code'];
        $strCounty = $arrCountries[$arrJson['address']['country_code']];
             
        $objGeolocation->setCountryShort($strCountryShort);
        $objGeolocation->setCountry($strCounty);     
        
        return $objGeolocation;        
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return $GLOBALS['TL_LANG']['Geolocation']['lu']['OpenStreetMap'][0];
    }
    
    /**
     *
     * @param type $strLanguage
     * @return string 
     */
    public function getDescription()
    {
        return $GLOBALS['TL_LANG']['Geolocation']['lu']['OpenStreetMap'][1];
    }

    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType()
    {
        return GeoLookUpInterface::GEO;
    }
    

}

?>
