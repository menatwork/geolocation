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
 * Class GeoProtectionContainer
 *
 * Container class for geolocation information
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 */
class GeoProtectionContainer
{

    protected $strCountry;
    protected $strCountryShort;
    protected $strIP;
    protected $booGeolocated;
    protected $booIPLookup;
    protected $booFailed;
    protected $booChangeByUser;
    protected $booFallback;
    protected $strError;
    protected $intError;

    public function __construct()
    {
        $this->strCountry = "";
        $this->strCountryShort = "";
        $this->strIP = "";
        $this->booGeolocated = false;
        $this->booIPLookup = false;
        $this->booFailed = false;
        $this->booChangeByUser = false;
        $this->booFallback = false;
        $this->strError = "";
        $this->intError = 0;
    }

    public function asArray()
    {
        return array(
            // Country Information
            "country" => $this->strCountry,
            "country_short" => $this->strCountryShort,
            // IP
            "ip" => $this->strIP,
            // Flags
            "geolocated" => $this->booGeolocated,
            "ip_lookup" => $this->booIPLookup,
            "faild" => $this->booFailed,
            "userChanged" => $this->booChangeByUser,
            "fallback" => $this->booFallback,
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

    public function isGeolocated()
    {
        return $this->booGeolocated;
    }

    public function setGeolocated($booGeolocated)
    {
        $this->booGeolocated = $booGeolocated;
    }

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

    public function isChangeByUser()
    {
        return $this->booChangeByUser;
    }

    public function setChangeByUser($booChangeByUser)
    {
        $this->booChangeByUser = $booChangeByUser;
    }

    public function isFallback()
    {
        return $this->booFallback;
    }

    public function setFallback($booFallback)
    {
        $this->booFallback = $booFallback;
    }

}

?>
