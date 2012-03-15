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
 * Class GeolocationContainer
 *
 * Container class for geolocation information
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class GeolocationContainer
{

    // Informations
    protected $strCountry;
    protected $strCountryShort;
    protected $strIP;
    protected $strLat;
    protected $strLon;
    protected $strCacheID;
    // Flags
    protected $booGeolocated;       // W3C Location
    protected $booIPLookup;         // IP Location
    protected $booChangeByUser;     // Changed by user
    protected $booFallback;         // User fallback or override
    protected $booDeactivated;      // Could not get location
    protected $booTracked;          // Tracking finished
    protected $booFailed;           // Something goes wrong
    // Error
    protected $strError;
    protected $intError;
    
    public function __construct()
    {
        $this->strCountry = "";
        $this->strCountryShort = "";
        $this->strIP = "";
        $this->strLon = "";
        $this->strLat = "";
        $this->strCacheID = "";
        $this->booGeolocated = false;
        $this->booIPLookup = false;
        $this->booFailed = false;
        $this->booChangeByUser = false;
        $this->booFallback = false;
        $this->booDeactivated = false;
        $this->booTracked = false;
        $this->strError = "";
        $this->intError = 0;
    }

    public function asArray()
    {
        return array(
            // Default
            "cacheID" => $this->strCacheID,
            // Country Information
            "country" => $this->strCountry,
            "country_short" => $this->strCountryShort,
            // IP/Location
            "ip" => $this->strIP,
            "lat" => $this->strLat,
            "lon" => $this->strLon,
            // Flags
            "geolocated" => $this->booGeolocated,
            "ip_lookup" => $this->booIPLookup,
            "faild" => $this->booFailed,
            "userChanged" => $this->booChangeByUser,
            "fallback" => $this->booFallback,
            "deactivated" => $this->booDeactivated,
            "tracked" => $this->booTracked,
            // Error
            "error" => $this->strError,
            "error_ID" => $this->intError
        );
    }

    public function getCountry()
    {
        return $this->strCountry;
    }

    public function setCountry($strCountry)
    {
        $this->strCountry = $strCountry;
    }

    public function getCountryShort()
    {
        return $this->strCountryShort;
    }

    public function setCountryShort($strCountryShort)
    {
        $this->strCountryShort = $strCountryShort;
    }

    public function getIP()
    {
        return $this->strIP;
    }

    public function setIP($strIP)
    {
        $this->strIP = $strIP;
    }

    /**
     * True if w3c was successfully
     * @return boolean 
     */
    public function isGeolocated()
    {
        return $this->booGeolocated;
    }

    public function setGeolocated($booGeolocated)
    {
        $this->booGeolocated = $booGeolocated;
    }

    /**
     * True if ip lookup was successfully
     * @return boolean 
     */
    public function isIPLookup()
    {
        return $this->booIPLookup;
    }

    public function setIPLookup($booIPLookup)
    {
        $this->booIPLookup = $booIPLookup;
    }

    public function isFailed()
    {
        return $this->booFailed;
    }

    public function setFailed($booFaild)
    {
        $this->booFailed = $booFaild;
    }

    public function getError()
    {
        return $this->strError;
    }

    public function setError($strError)
    {
        $this->strError = $strError;
    }

    public function getErrorID()
    {
        return $this->intError;
    }

    public function setErrorID($intError)
    {
        $this->intError = $intError;
    }

    /**
     * True if user has changed his country
     * @return boolean 
     */
    public function isChangeByUser()
    {
        return $this->booChangeByUser;
    }

    public function setChangeByUser($booChangeByUser)
    {
        $this->booChangeByUser = $booChangeByUser;
    }

     /**
     * True if w3c and 
     * @return boolean 
     */
    public function isFallback()
    {
        return $this->booFallback;
    }

    public function setFallback($booFallback)
    {
        $this->booFallback = $booFallback;
    }

    public function getLat()
    {
        return $this->strLat;
    }

    public function setLat($strLat)
    {
        $this->strLat = $strLat;
    }

    public function getLon()
    {
        return $this->strLon;
    }

    public function setLon($strLon)
    {
        $this->strLon = $strLon;
    }

    public function isDeactivated()
    {
        return $this->booDeactivated;
    }

    public function setDeactivated($booDeactivated)
    {
        $this->booDeactivated = $booDeactivated;
    }

    public function getCacheID()
    {
        return $this->strCacheID;
    }

    public function setCacheID($strCacheID)
    {
        $this->strCacheID = $strCacheID;
    }

    /**
     * Check if we have a result for tracking
     * @return boolean True - Try to locat | False - not yet tracked 
     */
    public function isChooseByUser()
    {
        return ($this->isChangeByUser() || $this->isGeolocated() || $this->isIPLookup() || $this->isFallback() || $this->isDeactivated());
    }
    
    /**
     * Check if we allready tracked
     * @return boolean True - Tracking is finished | False - Tracking not done  
     */
    public function isTracked()
    {
        return $this->booTracked;
    }
         
    public function setTracked($booTracked)
    {
        $this->booTracked = $booTracked;
    }

}

?>
