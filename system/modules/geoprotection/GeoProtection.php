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
 * Class GeoProtection
 *
 * Provide methods for GeoProtection.
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 */
class GeoProtection extends Frontend
{

    /**
     * IP cache array
     * @var array
     */
    private static $arrIPCache = array();

    /**
     * Container for geo information
     * @var GeoProtectionContainer 
     */
    protected $objCountryInformation;

    /**
     * Container for geo information
     * @var GeoProtectionContainer 
     */
    protected static $instance = null;

    /**
     * Constructor 
     */
    protected function __construct()
    {
        // Call parent constructor
        parent::__construct();

        // Import classes
        $this->import("Session");
        $this->import("Environment");

        // Get geolocation from session or create a new one        
        $objGeoProtection = $this->Session->get("geoprotection");

        if ($objGeoProtection != null && is_object($objGeoProtection))
        {
            $this->objCountryInformation = $objGeoProtection;
        }
        else
        {
            // Init functions
            $this->objCountryInformation = new GeoProtectionContainer();

            // Check ip override
            if ($GLOBALS['TL_CONFIG']['gp_customOverrideGp'] == true)
            {
                // Get IP`s from config
                $arrIP           = deserialize($GLOBALS['TL_CONFIG']['gp_overrideIps']);
                $arrCountries    = $this->getCountries();
                $strCountryShort = $GLOBALS['TL_CONFIG']['gp_customCountryFallback'];

                // Check if we have a array
                if ($arrIP != null && is_array($arrIP) && $strCountryShort != null && key_exists($strCountryShort, $arrCountries))
                {
                    // Search in array for current IP
                    foreach ($arrIP as $value)
                    {
                        if ($value["ipAddress"] == $this->Environment->ip)
                        {
                            $this->objCountryInformation->setCountry($arrCountries[$strCountryShort]);
                            $this->objCountryInformation->setCountryShort($strCountryShort);
                            $this->objCountryInformation->setChangeByUser(false);
                            $this->objCountryInformation->setFailed(false);
                            $this->objCountryInformation->setGeolocated(false);
                            $this->objCountryInformation->setIPLookup(false);
                            $this->objCountryInformation->setFallback(true);
                            $this->objCountryInformation->setIP("");
                            $this->objCountryInformation->setError("");
                            $this->objCountryInformation->setErrorID(0);

                            $this->Session->set("geoprotection", $this->objCountryInformation);

                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get current instance from GeoProtection
     * 
     * @return GeoProtection 
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new GeoProtection();
        }

        return self::$instance;
    }

    // Functions ---------------------------------------------------------------

    /**
     * Get either the current geolocation from session.
     * If no session was found, check ip override or return a new 
     * empty geolocation.
     * 
     * @return GeoProtectionContainer 
     */
    public function getUseGeoLocation()
    {
        return $this->objCountryInformation;
    }

    /**
     * Set the user location by country short tag
     * 
     * @param string $strCountryShort
     * @throws Exception If short country is unknown 
     */
    public function setUserGeolocation($strCountryShort)
    {
        $arrCountries = $this->getCountries();

        if (key_exists($strCountryShort, $arrCountries))
        {
            $this->objCountryInformation->setCountry($arrCountries[$strCountryShort]);
            $this->objCountryInformation->setCountryShort($strCountryShort);
            $this->objCountryInformation->setChangeByUser(true);
            $this->objCountryInformation->setFailed(false);
            $this->objCountryInformation->setGeolocated(false);
            $this->objCountryInformation->setIPLookup(false);
            $this->objCountryInformation->setFallback(false);
            $this->objCountryInformation->setIP("");
            $this->objCountryInformation->setError("");
            $this->objCountryInformation->setErrorID(0);

            // Save in Session
            $this->Session->set("geoprotection", $this->objCountryInformation);
        }
        else
        {
            throw new Exception("Unknown country tag: $strCountryShort");
        }
    }

    /**
     * AJAX Call get for a lat/lon the country information
     * 
     * @return type 
     */
    public function dispatchAjax()
    {
        switch ($this->Input->post("action"))
        {
            /**
             * Set geolocation for user.
             * Try to get the lat/lon from cache db or use lookup service.
             * Write all information into session. 
             */
            case "GeoProSetLocation":
                // Link for lat/lon lookup
                $strLink = "http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s";

                // Setup return array
                $arrReturn = array(
                    "success" => true,
                    "value" => "",
                    "error" => ""
                );

                // Run
                try
                {
                    // Check if lat/lon is set
                    if ((strlen($this->Input->post("lat")) == 0 || strlen($this->Input->post("lon")) == 0) && (strlen($this->Input->post("country")) == 0 || strlen($this->Input->post("countryShort")) == 0))
                    {
                        throw new Exception("Missing longitude/latitude or country/countryShort.");
                    }

                    // Check if user has set his location
                    if (strlen($this->Input->post("countryShort")) != null)
                    {
                        $this->setUserGeolocation($this->Input->post("countryShort"));

                        $arrReturn["value"] = "Set location by user cookies.";
                        return $arrReturn;
                    }

                    // Split 
                    $arrLat = trimsplit(".", $this->Input->post("lat"));
                    $arrLon = trimsplit(".", $this->Input->post("lon"));

                    // Check if we have two values
                    if (count($arrLat) != 2 || count($arrLon) != 2)
                    {
                        throw new Exception("The longitude or latitude dosen't seem to be a valid loaction.");
                    }

                    // Only get the last three numbers after the point 
                    $arrLat[1] = substr($arrLat[1], 0, 3);
                    $arrLon[1] = substr($arrLon[1], 0, 3);

                    // Check if we have the location already in database
                    $objResult = $this->Database
                            ->prepare("SELECT * FROM tl_geodatacache WHERE lat=? AND lon=?")
                            ->execute(implode(".", $arrLat), implode(".", $arrLon));

                    // No values found, so try to get the country
                    if ($objResult->numRows == 0)
                    {
                        $objRequest = new Request();
                        $objRequest->send(vsprintf($strLink, array(implode(".", $arrLat), implode(".", $arrLon))));
                        if ($objRequest->code != 200)
                        {
                            throw new Exception(vsprintf("Request error code %s - %s ", array($objRequest->code, $objRequest->error)));
                        }

                        $arrAddress = json_decode($objRequest->response, true);
                        $arrAddress = $arrAddress["address"];

                        $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                                ->set(array("lat" => implode(".", $arrLat), "lon" => implode(".", $arrLon), "create_on" => time(), "country" => $arrAddress["country"], "country_short" => $arrAddress["country_code"]))
                                ->execute();

                        // Set information for geolocation
                        $this->objCountryInformation->setCountry($arrAddress["country"]);
                        $this->objCountryInformation->setCountryShort($arrAddress["country_code"]);
                        $this->objCountryInformation->setIP("");
                        $this->objCountryInformation->setIPLookup(false);
                        $this->objCountryInformation->setFailed(false);
                        $this->objCountryInformation->setGeolocated(true);
                        $this->objCountryInformation->setFallback(false);
                        $this->objCountryInformation->setError("");
                        $this->objCountryInformation->setErrorID(0);

                        // Save in Session
                        $this->Session->set("geoprotection", $this->objCountryInformation);

                        $arrReturn["value"] = "Inset new location.";
                    }
                    // Found a entry in database
                    else
                    {
                        // Set information for geolocation
                        $this->objCountryInformation->setCountry($objResult->country);
                        $this->objCountryInformation->setCountryShort($objResult->country_short);
                        $this->objCountryInformation->setIP("");
                        $this->objCountryInformation->setIPLookup(false);
                        $this->objCountryInformation->setFailed(false);
                        $this->objCountryInformation->setGeolocated(true);
                        $this->objCountryInformation->setFallback(false);
                        $this->objCountryInformation->setError("");
                        $this->objCountryInformation->setErrorID(0);

                        // Save in Session
                        $this->Session->set("geoprotection", $this->objCountryInformation);

                        $arrReturn["value"] = "Found location in database.";
                    }
                }
                catch (Exception $exc)
                {
                    // Set information for geolocation
                    $this->objCountryInformation->setCountry("");
                    $this->objCountryInformation->setCountryShort("");
                    $this->objCountryInformation->setIP("");
                    $this->objCountryInformation->setIPLookup(false);
                    $this->objCountryInformation->setFailed(true);
                    $this->objCountryInformation->setGeolocated(false);
                    $this->objCountryInformation->setFallback(false);
                    $this->objCountryInformation->setError($exc->getMessage());
                    $this->objCountryInformation->setErrorID(-1);

                    // Try to load the location by IP
                    $this->objCountryInformation = $this->doIPLookUp($this->objCountryInformation);

                    // Save in Session
                    $this->Session->set("geoprotection", $this->objCountryInformation);

                    // Error handling
                    $arrReturn["success"] = false;
                    $arrReturn["error"]   = $exc->getMessage();

                    return $arrReturn;
                }

                // Return debug information
                return $arrReturn;

            /**
             * Set error msg from js script.
             */
            case "GeoProSetError":
                switch ($this->Input->post("errID"))
                {
                    case 1:
                        $strError = "Premission denined";
                        break;

                    case 2:
                        $strError = "Position unavailable";
                        break;

                    case 3:
                        $strError = "Timeout";
                        break;

                    case 10:
                        $strError = "Not supported Browser.";
                        break;

                    default:
                        $strError = "Unknown error";
                        break;
                }

                // Set information for geolocation
                $this->objCountryInformation->setCountry("");
                $this->objCountryInformation->setCountryShort("");
                $this->objCountryInformation->setIP("");
                $this->objCountryInformation->setIPLookup(true);
                $this->objCountryInformation->setFailed(true);
                $this->objCountryInformation->setGeolocated(false);
                $this->objCountryInformation->setFallback(false);
                $this->objCountryInformation->setError($strError);
                $this->objCountryInformation->setErrorID($this->Input->post("errID"));

                // Get Geolocation from IP
                $this->objCountryInformation = $this->doIPLookUp($this->objCountryInformation);

                // Save in Session
                $this->Session->set("geoprotection", $this->objCountryInformation);

                return array(
                    "success" => true,
                    "value" => "Set location by ip",
                    "error" => ""
                );

            case "GeoProChangeLocation":
                if (strlen($this->Input->post("location")) == 0)
                {
                    return array(
                        "success" => false,
                        "value" => "",
                        "error" => "Missing location."
                    );
                }

                try
                {
                    $this->setUserGeolocation($this->Input->post("location"));
                }
                catch (Exception $exc)
                {
                    return array(
                        "success" => false,
                        "value" => "",
                        "error" => $exc->getMessage()
                    );
                }

                return array(
                    "success" => true,
                    "value" => "",
                    "error" => ""
                );

            default:
                return false;
        }
    }

    /**
     * 
     * @param GeoProtectionContainer $objGeoLocation
     * @return GeoProtectionContainer
     */
    public function doIPLookUp($objGeoLocation = null)
    {
        if ($objGeoLocation == null)
        {
            $objGeoLocation = new GeoProtectionContainer();
        }

        //calculate ipNum
        $arrIP = explode(".", $this->Environment->ip);
        $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

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

        // Check if we have for this ip a land
        if (strlen($country->country) != 0 && strlen($country->country_short) != 0)
        {
            $objGeoLocation->setIPLookup(true);
            $objGeoLocation->setIP($this->Environment->ip);
            $objGeoLocation->setCountry($country->country);
            $objGeoLocation->setCountryShort($country->country_short);
        }
        // If not try to load the fallback land or set a blank country
        else
        {
            // Set the default values for ip lookup
            $objGeoLocation->setIPLookup(false);
            $objGeoLocation->setIP($this->Environment->ip);

            // Check if a fallback is define
            if ($GLOBALS['TL_CONFIG']['gp_countryFallback'] != null && $GLOBALS['TL_CONFIG']['gp_countryFallback'] != "none")
            {
                // Get contrys
                $arrCountries       = $this->getCountries();
                $strCountryFallback = $GLOBALS['TL_CONFIG']['gp_countryFallback'];

                // Set in container file
                $objGeoLocation->setCountry($arrCountries[$strCountryFallback]);
                $objGeoLocation->setCountryShort($strCountryFallback);
                $objGeoLocation->setFallback(true);
            }
            else
            {
                // Set empty country settings
                $objGeoLocation->setCountry($country->country);
                $objGeoLocation->setCountryShort($country->country_short);
            }
        }

        return $objGeoLocation;
    }

    /**
     * Set in Header settings for geoProtection 
     * 
     * @param string $strContent
     * @param string $strTemplate
     * @return Stirng 
     */
    public function insertJSVars($strContent, $strTemplate)
    {
        if ($strTemplate == "fe_page")
        {
            // Load language
            $this->loadLanguageFile("geoProtection");

            if(!is_array($GLOBALS['TL_LANG']['geoProtection']['js']))
            {
                $GLOBALS['TL_LANG']['geoProtection']['js'] = array();
            }            

            $arrDurations = deserialize($GLOBALS['TL_CONFIG']['gp_cookieDuration']);

            $strJS = "";
            $strJS .= "<script>";
            $strJS .= "\n";
            $strJS .= "var gp_cookieEnabeld = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true) ? "true" : "false");
            $strJS .= "\n";
            $strJS .= "var gp_cookieDurationW3C = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true && is_numeric($arrDurations[0])) ? $arrDurations[0] : "0");
            $strJS .= "\n";
            $strJS .= "var gp_cookieDurationUser = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true && is_numeric($arrDurations[1])) ? $arrDurations[1] : "0");
            $strJS .= "\n";
            
            foreach ($GLOBALS['TL_LANG']['geoProtection']['js'] as $key => $value)
            {
                $strJS .= "var $key = '$value'";
                $strJS .= "\n";
            }

            $strJS .= "</script>";
            $strJS .= "\n";

            $strContent = preg_replace("^<script ^", "$strJS$0", $strContent, 1);
        }

        return $strContent;
    }

    /**
     * get Country-List
     */
    public function getCountriesByContinent()
    {
        $return = array();
        $countries = array();
        $arrAux = array();
        $arrTmp = array();

        $this->loadLanguageFile('countries');
        $this->loadLanguageFile('continents');
        include(TL_ROOT . '/system/config/countries.php');
        include(TL_ROOT . '/system/modules/geoprotection/countriesByContinent.php');
        foreach ($countriesByContinent as $strConKey => $arrCountries)
        {

            $strConKeyTranslated = strlen($GLOBALS['TL_LANG']['CONTINENT'][$strConKey]) ? utf8_romanize($GLOBALS['TL_LANG']['CONTINENT'][$strConKey]) : $strConKey;
            $arrAux[$strConKey]  = $strConKeyTranslated;
            foreach ($arrCountries as $strCount)
            {


                $arrTmp[$strConKeyTranslated][$strCount] = strlen($GLOBALS['TL_LANG']['CNT'][$strCount]) ? utf8_romanize($GLOBALS['TL_LANG']['CNT'][$strCount]) : $countries[$strName];
            }
        }

        ksort($arrTmp);

        foreach ($arrTmp as $strConKey => $arrCountries)
        {
            asort($arrCountries);
            //get original continent key
            $strOrgKey           = array_search($strConKey, $arrAux);
            $strConKeyTranslated = strlen($GLOBALS['TL_LANG']['CONTINENT'][$strOrgKey]) ? ($GLOBALS['TL_LANG']['CONTINENT'][$strOrgKey]) : $strConKey;
            foreach ($arrCountries as $strKey => $strCountry)
            {

                $return[$strConKeyTranslated][$strKey] = strlen($GLOBALS['TL_LANG']['CNT'][$strKey]) ? $GLOBALS['TL_LANG']['CNT'][$strKey] : $countries[$strKey];
            }
        }

        return $return;
    }

}

?>