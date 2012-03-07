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
 * @package    Geolrotection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Class Geolocation
 *
 * Provide methods for Geolocation.
 * @copyright  MEN AT WORK 2011-2012
 * @package    Geolocation
 */
class Geolocation extends Frontend
{

    /**
     * Container for geo information
     * @var GeolocationContainer 
     */
    protected $objUserGeolocation;

    /**
     * Container for geo information
     * @var Geolocation 
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
        $this->import("Input");

        // Get geolocation from session/cookie or create a new one        
        $objGeoLocationSession = $this->loadSession();
        $objGeoLocationCookie  = $this->loadCookie();

        // Load first Session
        if ($objGeoLocationSession != null && is_object($objGeoLocationSession))
        {
            $this->objUserGeolocation = $objGeoLocationSession;
            return;
        }

        // Try to load from cookie
        if (true || $objGeoLocationCookie != null && $objGeoLocationCookie != "")
        {
            $arrCookie = json_decode($objGeoLocationCookie, true);

            if (is_array($arrCookie) && strlen($arrCookie['cacheID']) != 0)
            {
                $objResult = $this->Database
                        ->prepare("SELECT * FROM tl_geodatacache WHERE cache_id=?")
                        ->limit(1)
                        ->execute($arrCookie['cacheID']);

                if ($objResult->numRows != 0)
                {
                    $this->objUserGeolocation = new GeolocationContainer();

                    $this->objUserGeolocation->setIP($objResult->ipnum);
                    $this->objUserGeolocation->setLat($objResult->lat);
                    $this->objUserGeolocation->setLon($objResult->lon);

                    $this->objUserGeolocation->setCountryShort($objResult->country_short);
                    $this->objUserGeolocation->setCountry($this->getCountryByShortTag($objResult->country_short));

                    if ($objResult->ipnum != "0")
                    {
                        $this->objUserGeolocation->setIPLookup(true);
                    }
                    else
                    {
                        $this->objUserGeolocation->setGeolocated(true);
                    }

                    $this->saveSession($this->objUserGeolocation);

                    return;
                }
            }

            if (is_array($arrCookie) && strlen($arrCookie['countryShort']) != 0 && $this->getCountryByShortTag($arrCookie['countryShort']) != false)
            {
                $this->objUserGeolocation = new GeolocationContainer();

                $this->objUserGeolocation->setCountryShort($arrCookie['countryShort']);
                $this->objUserGeolocation->setCountry($this->getCountryByShortTag($arrCookie['countryShort']));
                
                $this->objUserGeolocation->setChangeByUser(true);

                $this->saveSession($this->objUserGeolocation);

                return;
            }
        }

        // No session or cookie so make a new geolocation container
        // Init object
        $this->objUserGeolocation = new GeolocationContainer();

        // Check ip override
        if ($GLOBALS['TL_CONFIG']['geo_customOverride'] == true)
        {
            // Get IP`s from config
            $arrIP           = deserialize($GLOBALS['TL_CONFIG']['geo_overrideIps']);
            $arrCountries    = $this->getCountries();
            $strCountryShort = $GLOBALS['TL_CONFIG']['geo_customCountryFallback'];

            // Check if we have a array
            if ($arrIP != null && is_array($arrIP) && $strCountryShort != null && key_exists($strCountryShort, $arrCountries))
            {
                // Search in array for current IP
                foreach ($arrIP as $value)
                {
                    if ($value["ipAddress"] == $this->Environment->ip)
                    {
                        $this->objUserGeolocation->setCountry($arrCountries[$strCountryShort]);
                        $this->objUserGeolocation->setCountryShort($strCountryShort);
                        $this->objUserGeolocation->setChangeByUser(false);
                        $this->objUserGeolocation->setFailed(false);
                        $this->objUserGeolocation->setGeolocated(false);
                        $this->objUserGeolocation->setIPLookup(false);
                        $this->objUserGeolocation->setFallback(true);
                        $this->objUserGeolocation->setIP("");
                        $this->objUserGeolocation->setError("");
                        $this->objUserGeolocation->setErrorID(0);

                        $this->Session->set("geolocation", $this->objUserGeolocation);

                        break;
                    }
                }
            }
        }

        $this->saveSession($this->objUserGeolocation);
    }

    /**
     * Get current instance from GeoProtection
     * 
     * @return Geolocation 
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Geolocation();
        }

        return self::$instance;
    }

    // Session / Cookies -------------------------------------------------------

    protected function saveSession(GeolocationContainer $objUserGeolocation)
    {
        $this->Session->set("geolocation", $objUserGeolocation);
    }

    protected function loadSession()
    {
        return $this->Session->get("geolocation");
    }

    protected function loadCookie()
    {
        return $this->Input->cookie("Geolocation");
    }

    // Getter / Setter ---------------------------------------------------------

    /**
     * Get either the current geolocation from session.
     * If no session was found, check ip override or return a new 
     * empty geolocation.
     * 
     * @return GeolocationContainer 
     */
    public function getUserGeolocation()
    {
        return $this->objUserGeolocation;
    }

    /**
     * Set the user location by country short tag
     * 
     * @param string $strCountryShort
     * @throws Exception If short country is unknown 
     */
    public function setUserGeolocationByShortCountry($strCountryShort)
    {
        if ($this->getCountryByShortTag($strCountryShort) != FALSE)
        {
            $this->objUserGeolocation = new GeolocationContainer();

            $this->objUserGeolocation->setCountryShort($strCountryShort);
            $this->objUserGeolocation->setCountry($this->getCountryByShortTag($strCountryShort));

            $this->objUserGeolocation->setChangeByUser(true);

            $this->saveSession($this->objUserGeolocation);
        }
        else
        {
            throw new Exception("Unknown country tag: $strCountryShort");
        }
    }

    // Helper ------------------------------------------------------------------

    protected function getCountryByShortTag($strShort)
    {
        $arrCountries = $this->getCountries();

        if (key_exists($strShort, $arrCountries))
        {
            return $arrCountries[$strShort];
        }
        else
        {
            return FALSE;
        }
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
            $this->loadLanguageFile("default");

            if (!is_array($GLOBALS['TL_LANG']['Geolocation']['js']))
            {
                $GLOBALS['TL_LANG']['Geolocation']['js'] = array();
            }

            $arrDurations = deserialize($GLOBALS['TL_CONFIG']['geo_cookieDuration']);

            $strJS = "";
            $strJS .= "<script>";
            $strJS .= "\n";
            $strJS .= "var geo_cookieEnabeld = true;";
            $strJS .= "\n";
            $strJS .= "var geo_cookieDurationW3C = " . (is_numeric($arrDurations[0]) ? $arrDurations[0] : "0") . ";";
            $strJS .= "\n";
            $strJS .= "var geo_cookieDurationUser = " . (is_numeric($arrDurations[1]) ? $arrDurations[1] : "0") . ";";
            $strJS .= "\n";

            foreach ($GLOBALS['TL_LANG']['Geolocation']['js'] as $key => $value)
            {
                $strJS .= "var $key = '$value';";
                $strJS .= "\n";
            }

            $strJS .= "</script>";
            $strJS .= "\n";

            $strContent = preg_replace("^<script ^", "$strJS$0", $strContent, 1);
        }

        return $strContent;
    }

    // Functions ---------------------------------------------------------------

    /**
     * User lookup services to get information about a lat/lon value
     * 
     * @param string $strLat
     * @param string $strLon
     * @return \GeolocationContainer
     * @throws Exception If Lat/Lon is not valide
     */
    public function doGeoLookUP(GeolocationContainer $objGeolocation)
    {
        // Split 
        $arrLat = trimsplit(".", $objGeolocation->getLat());
        $arrLon = trimsplit(".", $objGeolocation->getLon());

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
            $arrLookUpServices    = deserialize($GLOBALS['TL_CONFIG']['geo_GeolookUpSettings']);
            $objGeolocationResult = FALSE;

            foreach ($arrLookUpServices as $value)
            {
                $objLookUpService     = GeoLookUpFactory::getEngine($value["lookUpClass"]);
                $objGeolocationResult = $objLookUpService->getLocation($value["lookUpConfig"], $objGeolocation);

                if ($objGeolocationResult !== FALSE)
                {
                    $objGeolocation = $objGeolocationResult;
                    break;
                }
            }

            if ($objGeolocationResult === FALSE)
            {
                // Set information for geolocation
                $objGeolocation->setCacheID("");

                $objGeolocation->setCountry("");
                $objGeolocation->setCountryShort("");

                $objGeolocation->setLat(implode(".", $arrLatFull));
                $objGeolocation->setLon(implode(".", $arrLonFull));

                $objGeolocation->setGeolocated(false);
                $objGeolocation->setFailed(true);
                $objGeolocation->setIPLookup(false);
                $objGeolocation->setChangeByUser(false);
                $objGeolocation->setFallback(false);
                $objGeolocation->setDeactivated(false);

                $objGeolocation->setError("No geolocation found.");
                $objGeolocation->setErrorID(-1);
            }
            else
            {
                $strCacheID = md5(implode(".", $arrLat) . " | " . implode(".", $arrLon) . " | " . $objGeolocation->getCountryShort());

                $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                        ->set(array(
                            "lat" => implode(".", $arrLat),
                            "lon" => implode(".", $arrLon),
                            "create_on" => time(),
                            "country" => $objGeolocation->getCountry(),
                            "country_short" => $objGeolocation->getCountryShort(),
                            "cache_ID" => $strCacheID
                        ))
                        ->execute();

                // Set information for geolocation
                $objGeolocation->setCacheID($strCacheID);

                $objGeolocation->setGeolocated(true);
                $objGeolocation->setFailed(false);
                $objGeolocation->setIPLookup(false);
                $objGeolocation->setChangeByUser(false);
                $objGeolocation->setFallback(false);
                $objGeolocation->setDeactivated(false);

                $objGeolocation->setError("");
                $objGeolocation->setErrorID(0);
            }
        }
        // Found a entry in database
        else
        {
            // Set information for geolocation
            $objGeolocation->setCacheID($objResult->cache_id);

            $objGeolocation->setCountry($objResult->country);
            $objGeolocation->setCountryShort($objResult->country_short);

            $objGeolocation->setIP("");
            $objGeolocation->setLat($objResult->lat);
            $objGeolocation->setLon($objResult->lon);

            $objGeolocation->setGeolocated(true);
            $objGeolocation->setFailed(false);
            $objGeolocation->setIPLookup(false);
            $objGeolocation->setChangeByUser(false);
            $objGeolocation->setFallback(false);
            $objGeolocation->setDeactivated(false);

            $objGeolocation->setError("");
            $objGeolocation->setErrorID(0);
        }

        return $objGeolocation;
    }

    /**
     * User lookup service to get informations about a ip adress
     * 
     * @param GeolocationContainer $objGeolocation
     * @return \GeolocationContainer 
     */
    public function doIPLookUp(GeolocationContainer $objGeolocation = null)
    {
        if ($objGeolocation == null)
        {
            $objGeolocation = new GeolocationContainer();
        }

        // Build number from ip
        $arrIP = explode(".", $this->Environment->ip);
        $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

        // Check if we have the location already in database
        $objResult = $this->Database
                ->prepare("SELECT * FROM tl_geodatacache WHERE ipnum=?")
                ->execute($ipNum);

        // No values found, so try to get the country
        if ($objResult->numRows == 0)
        {
            $arrLookUpServices    = deserialize($GLOBALS['TL_CONFIG']['geo_IPlookUpSettings']);
            $objGeolocationResult = FALSE;

            foreach ($arrLookUpServices as $value)
            {
                $objLookUpService     = GeoLookUpFactory::getEngine($value["lookUpClass"]);
                $objGeolocationResult = $objLookUpService->getLocation($value["lookUpConfig"], $objGeolocation);

                if ($objGeolocationResult !== FALSE)
                {
                    $objGeolocation = $objGeolocationResult;
                    break;
                }
            }

            if ($objGeolocationResult === FALSE)
            {
                // Check if a fallback is define
                if (strlen($GLOBALS['TL_CONFIG']['geo_countryFallback']) != 0)
                {
                    $strCountryFallback = $GLOBALS['TL_CONFIG']['geo_countryFallback'];

                    // Set information for geolocation
                    $objGeolocation->setCountry($this->getCountryByShortTag($strCountryFallback));
                    $objGeolocation->setCountryShort($strCountryFallback);

                    $objGeolocation->setIP("");

                    $objGeolocation->setGeolocated(false);
                    $objGeolocation->setFailed(false);
                    $objGeolocation->setIPLookup(false);
                    $objGeolocation->setChangeByUser(false);
                    $objGeolocation->setFallback(true);
                    $objGeolocation->setDeactivated(false);
                }
                else
                {
                    // Set information for geolocation
                    $objGeolocation->setCountry("");
                    $objGeolocation->setCountryShort("");
                    $objGeolocation->setIP("");

                    $objGeolocation->setGeolocated(false);
                    $objGeolocation->setFailed(false);
                    $objGeolocation->setIPLookup(false);
                    $objGeolocation->setChangeByUser(false);
                    $objGeolocation->setFallback(false);
                    $objGeolocation->setDeactivated(true);

                    $objGeolocation->setError("No geolocation|IP|Fallback found.");
                    $objGeolocation->setErrorID(-1);
                }
            }
            else
            {
                $strCacheID = md5($ipNum . " | " . $objGeolocation->getCountryShort());

                $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                        ->set(array(
                            "ipnum" => $ipNum,
                            "create_on" => time(),
                            "country" => $objGeolocation->getCountry(),
                            "country_short" => $objGeolocation->getCountryShort(),
                            "cache_id" => $strCacheID
                        ))
                        ->execute();

                // Set information for geolocation
                $objGeolocation->setIP(preg_replace("/\..*$/", ".XXX", $objGeolocation->getIP()));

                $objGeolocation->setCacheID($strCacheID);

                $objGeolocation->setGeolocated(false);
                $objGeolocation->setFailed(false);
                $objGeolocation->setIPLookup(true);
                $objGeolocation->setChangeByUser(false);
                $objGeolocation->setFallback(false);
                $objGeolocation->setDeactivated(false);

                $objGeolocation->setError("");
                $objGeolocation->setErrorID(0);
            }
        }
        // Found a entry in database
        else
        {
            // Set information for geolocation
            $objGeolocation->setCacheID($objResult->cache_id);

            $objGeolocation->setCountry($objResult->country);
            $objGeolocation->setCountryShort($objResult->country_short);

            $objGeolocation->setIP("");
            $objGeolocation->setLat("");
            $objGeolocation->setLon("");

            $objGeolocation->setGeolocated(false);
            $objGeolocation->setFailed(false);
            $objGeolocation->setIPLookup(true);
            $objGeolocation->setChangeByUser(false);
            $objGeolocation->setFallback(false);
            $objGeolocation->setDeactivated(false);

            $objGeolocation->setError("");
            $objGeolocation->setErrorID(0);
        }

        return $objGeolocation;
    }

    // AJAX Functions ----------------------------------------------------------

    /**
     * AJAX Hook
     * 
     * @return array() Keys: success | value | error 
     */
    public function dispatchAjax()
    {
        // Setup return array
        $arrReturn = array(
            "success" => true,
            "value" => "",
            "error" => ""
        );

        // Chose function
        switch ($this->Input->post("action"))
        {
            /**
             * Set geolocation for user.
             * Try to get the lat/lon from cache db or use lookup service.
             * Write all information into session. 
             */
            case "GeoSetLocation":
                $arrReturn = $this->ajaxSetLocation($arrReturn);
                break;

            /**
             * Set error msg from js script.
             */
            case "GeoSetError":
                $arrReturn = $this->ajaxSetError($arrReturn);
                break;

            /**
             * Change location by user settings 
             */
            case "GeoChangeLocation":
                $arrReturn = $this->ajaxChangeLocation($arrReturn);
                break;

            default:
                return false;
        }

        // Save in Session
        $this->saveSession($this->objUserGeolocation);

        // Return answer
        return $arrReturn;
    }

    /**
     *
     * @param type $arrReturn
     * @return string|boolean
     * @throws Exception 
     */
    protected function ajaxSetLocation($arrReturn)
    {
        try
        {
            // Check if lat/lon is set
            if ((strlen($this->Input->post("lat")) == 0 || strlen($this->Input->post("lon")) == 0) && (strlen($this->Input->post("country")) == 0 || strlen($this->Input->post("countryShort")) == 0))
            {
                throw new Exception("Missing longitude/latitude or country/countryShort.");
            }

            $this->objUserGeolocation->setLat($this->Input->post("lat"));
            $this->objUserGeolocation->setLon($this->Input->post("lon"));

            // Do geolocation look up
            $this->objUserGeolocation = $this->doGeoLookUP($this->objUserGeolocation);

            // Check if there was a result
            if ($this->objUserGeolocation->isFailed())
            {
                // Do a ip look up as fallback
                $this->objUserGeolocation->setIP($this->Environment->IP);
                $this->objUserGeolocation = $this->doIPLookUp($this->objUserGeolocation);

                // Check if we have a result
                if ($this->objUserGeolocation->isFailed())
                {
                    // Error handling
                    $arrReturn["success"] = false;
                    $arrReturn["error"]   = $this->objUserGeolocation->getError();
                }
            }
        }
        catch (Exception $exc)
        {
            // Try to load the location by IP
            $this->objUserGeolocation->setIP($this->Environment->IP);
            $this->objUserGeolocation = $this->doIPLookUp($this->objUserGeolocation);

            // Error handling
            $arrReturn["success"] = false;
            $arrReturn["error"]   = $exc->getMessage();
        }

        $arrReturn["cache_id"] = $this->objUserGeolocation->getCacheID();

        // Return debug information
        return $arrReturn;
    }

    protected function ajaxSetError($arrReturn)
    {
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
        $this->objUserGeolocation->setError($strError);
        $this->objUserGeolocation->setErrorID($this->Input->post("errID"));

        // Get Geolocation from IP
        $this->objUserGeolocation->setIP($this->Environment->IP);
        $this->objUserGeolocation = $this->doIPLookUp($this->objUserGeolocation);

        if ($this->objUserGeolocation->isFailed())
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = $this->objUserGeolocation->getError();
        }
        else
        {
            if ($this->objUserGeolocation->isFallback() == true)
            {
                $arrReturn["success"] = true;
                $arrReturn["value"]   = "Set location by fallback";
            }
            else
            {
                $arrReturn["success"]  = true;
                $arrReturn["value"]    = "Set location by ip";
                $arrReturn["cache_id"] = $this->objUserGeolocation->getCacheID();
            }
        }

        return $arrReturn;
    }

    protected function ajaxChangeLocation($arrReturn)
    {
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
            $this->setUserGeolocationByShortCountry($this->Input->post("location"));
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
    }

}

?>