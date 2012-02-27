<?php

class GeoProtectionContainer
{

    protected $strCountry;
    protected $strCountryShort;
    protected $strIP;
    protected $booGeolocated;
    protected $booIPLookup;
    protected $booFaild;
    protected $strError;
    protected $intError;

    public function __construct()
    {
        $this->strCountry = "";
        $this->strCountryShort = "";
        $this->strIP = "";
        $this->booGeolocated = false;
        $this->booIPLookup = false;
        $this->booFaild = false;
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
            "faild" => $this->booFaild,
            // Error
            "error" => $this->strError,
            "error_ID" => $this->intError
        );
    }

    public function getStrCountry()
    {
        return $this->strCountry;
    }

    public function setStrCountry($strCountry)
    {
        $this->strCountry = $strCountry;
    }

    public function getStrCountryShort()
    {
        return $this->strCountryShort;
    }

    public function setStrCountryShort($strCountryShort)
    {
        $this->strCountryShort = $strCountryShort;
    }

    public function getStrIP()
    {
        return $this->strIP;
    }

    public function setStrIP($strIP)
    {
        $this->strIP = $strIP;
    }

    public function getBooGeolocated()
    {
        return $this->booGeolocated;
    }

    public function setBooGeolocated($booGeolocated)
    {
        $this->booGeolocated = $booGeolocated;
    }

    public function getBooIPLookup()
    {
        return $this->booIPLookup;
    }

    public function setBooIPLookup($booIPLookup)
    {
        $this->booIPLookup = $booIPLookup;
    }

    public function getBooFaild()
    {
        return $this->booFaild;
    }

    public function setBooFaild($booFaild)
    {
        $this->booFaild = $booFaild;
    }

    public function getStrError()
    {
        return $this->strError;
    }

    public function setStrError($strError)
    {
        $this->strError = $strError;
    }

    public function getIntError()
    {
        return $this->intError;
    }

    public function setIntError($intError)
    {
        $this->intError = $intError;
    }

}

?>
