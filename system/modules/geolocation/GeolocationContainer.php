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
class GeolocationContainer implements Serializable
{
    /* -------------------------------------------------------------------------
     * Const
     */

    // LookUp

    const LOCATION_NONE        = 0;
    const LOCATION_W3C         = 1;
    const LOCATION_IP          = 2;
    const LOCATION_FALLBACK    = 3;
    const LOCATION_IP_OVERRIDE = 4;
    const LOCATION_BY_USER     = 5;

    // Errors
    const ERROR_NONE                 = 0;
    const ERROR_PERMISSION_DENIES    = 1;
    const ERROR_POSITION_UNAVAILABLE = 2;
    const ERROR_TIME_OUT             = 3;
    const ERROR_UNSUPPORTED_BROWSER  = 10;
    const ERROR_CONNECTION_AJAX      = 20;
    const ERROR_NO_IP_RESULT         = 30;
    const ERROR_NO_W3C_RESULT        = 31;

    /* -------------------------------------------------------------------------
     * Vars
     */

    // Informations
    protected $strCountry;
    protected $strCountryShort;
    protected $strIP;
    protected $strLat;
    protected $strLon;
    // State   
    protected $intTrackType;
    protected $booTracked;          // Tracking finished
    protected $booFailed;           // Something goes wrong
    // Error
    protected $strError;
    protected $intError;

    /* -------------------------------------------------------------------------
     * Basic functions
     */

    public function __construct()
    {
        $this->strCountry = "";
        $this->strCountryShort = "";
        $this->strIP = "";
        $this->strLon = "";
        $this->strLat = "";
        $this->intRunningTrackType = self::LOCATION_NONE;
        $this->arrFinishedrackType = array();
        $this->intTrackType = self::LOCATION_NONE;
        $this->booTracked = false;
        $this->booFailed = false;
        $this->strError = "";
        $this->intError = self::ERROR_NONE;
    }

    /**
     * Serialize all information 
     * 
     * @return type 
     */
    public function serialize()
    {
        return serialize($this->asArray());
    }

    public function unserialize($serialized)
    {
        // Get a list with all DefaultProperties
        $reflectionClass      = new ReflectionClass('GeolocationContainer');
        $reflectionProperties = $reflectionClass->getDefaultProperties();

        // Deserialize the data and check it
        $serialized = unserialize($serialized);

        foreach ($serialized as $key => $value)
        {
            if (key_exists($key, $reflectionProperties))
            {
                $this->$key = $value;
            }
        }
    }

    public function asArray()
    {
        $reflectionClass = new ReflectionClass('GeolocationContainer');
        $arrReturn       = array();

        foreach (array_keys($reflectionClass->getDefaultProperties()) as $value)
        {
            $arrReturn[$value] = $this->$value;
        }

        return $arrReturn;
    }
   
    /* -------------------------------------------------------------------------
     * Getter / Setter for informations
     */

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

    /* -------------------------------------------------------------------------
     * Getter / Setter for flags and state information
     */

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

    public function getTrackType()
    {
        return $this->intTrackType;
    }

    public function setTrackType($intTrackType)
    {
        $this->intTrackType = $intTrackType;
    }

    public function getRunningTrackType()
    {
        return $this->intRunningTrackType;
    }

    public function setRunningTrackType($intRunningTrackType)
    {
        $this->intRunningTrackType = $intRunningTrackType;
    }

    public function getFinishedrackType()
    {
        return $this->arrFinishedrackType;
    }

    public function setFinishedrackType($arrFinishedrackType)
    {
        $this->arrFinishedrackType = $arrFinishedrackType;
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

}

?>