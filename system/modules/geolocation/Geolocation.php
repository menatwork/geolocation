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
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Class Geolocation
 *
 * Provide methods for geolocation
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class Geolocation extends Frontend
{
    // States for geolocation

    const RESET          = 0;
    const GEO_LOCATION   = 1;
    const IP_LOCATION    = 2;
    const FALLBACK       = 3;
    const DEACTIVAIT     = 4;
    const CHANGE_BY_USER = 5;

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

        // Load location from Session ------------------------------------------

        $this->loadSession();

        if ($this->objUserGeolocation != null && is_object($this->objUserGeolocation))
        {
            // Fallback for nojs or missing user input
            if ($this->objUserGeolocation->isTracked() != true && $this->Input->post("isAJAX") != true)
            {
                $this->objUserGeolocation->setIP($this->Environment->ip);
                $objResult = $this->doIPLookUp($this->objUserGeolocation);
                
                if($objResult == FALSE)
                {
                    $this->objUserGeolocation->setTracked(true);
                    $this->objUserGeolocation->setDeactivated(true);
                }
                else
                {
                    $this->objUserGeolocation = $objResult;
                    $this->objUserGeolocation->setTracked(true);
                }
                
                $this->saveSession();

                return;
            }
            else
            {
                return;
            }
        }

        // Load location from Cookie -------------------------------------------

        $objGeoLocationCookie = $this->loadCookie();

        if ($objGeoLocationCookie != null && $objGeoLocationCookie != "")
        {
            // Make a array from the cookie
            $arrCookie = json_decode($objGeoLocationCookie, true);

            if (is_array($arrCookie))
            {
                // Check if the mandatory fields are set
                if (key_exists("countryShort", $arrCookie) && key_exists("mode", $arrCookie))
                {
                    // Create new container
                    $this->objUserGeolocation = new GeolocationContainer();

                    // Set values from cookie
                    $this->objUserGeolocation->setCountryShort($arrCookie['countryShort']);
                    $this->objUserGeolocation->setCountry($this->getCountryByShortTag($arrCookie['countryShort']));
                    $this->objUserGeolocation->setLat($arrCookie['lat']);
                    $this->objUserGeolocation->setLon($arrCookie['lon']);

                    // Set state
                    $this->changeState($arrCookie['mode']);

                    // Special Flags
                    $this->objUserGeolocation->setFailed(false);
                    $this->objUserGeolocation->setTracked(true);

                    // Save in Session
                    $this->saveSession();

                    return;
                }
            }
        }

        // IP Override or new creation -----------------------------------------
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

                        // Set state
                        $this->changeState(self::FALLBACK);

                        // Special Flags
                        $this->objUserGeolocation->setFailed(false);
                        $this->objUserGeolocation->setTracked(true);

                        break;
                    }
                }
            }
        }

        $this->saveSession();
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

    /**
     * Save the container $this->objUserGeolocation into the Session 
     */
    protected function saveSession()
    {
        $this->Session->set("geolocation", $this->objUserGeolocation);
    }

    /**
     * Load from the Session the geolocation into $this->objUserGeolocation
     */
    protected function loadSession()
    {
        $this->objUserGeolocation = $this->Session->get("geolocation");
    }

    /**
     * Load the geolocation cookier
     * 
     * @return String 
     */
    protected function loadCookie()
    {
        return $this->Input->cookie("Geolocation");
    }

    // Getter / Setter ---------------------------------------------------------

    /**
     * Get the current geolocation, see constructor for more information.
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
            $this->objUserGeolocation->setCountryShort($strCountryShort);
            $this->objUserGeolocation->setCountry($this->getCountryByShortTag($strCountryShort));

            // Set satet
            $this->changeState(self::CHANGE_BY_USER);

            // Set special flags
            $this->objUserGeolocation->setTracked(true);
            $this->objUserGeolocation->setFailed(false);

            // Save in session
            $this->saveSession();
        }
        else
        {
            throw new Exception("Unknown country tag: $strCountryShort");
        }
    }

    // Helper ------------------------------------------------------------------

    /**
     * Set the state for the container
     * 
     * @param int $intMode
     * @throws Exception when a unknown mode is set. 
     */
    protected function changeState($intMode)
    {
        // Reset state
        switch ($intMode)
        {
            case self::RESET:
            case self::GEO_LOCATION:
            case self::IP_LOCATION:
            case self::FALLBACK:
            case self::DEACTIVAIT:
            case self::CHANGE_BY_USER:
                $this->objUserGeolocation->setGeolocated(false);
                $this->objUserGeolocation->setIPLookup(false);
                $this->objUserGeolocation->setFallback(false);
                $this->objUserGeolocation->setDeactivated(false);
                $this->objUserGeolocation->setChangeByUser(false);
                break;

            default:
                throw new Exception("Unknown state: $intMode");
                break;
        }

        switch ($intMode)
        {
            case self::GEO_LOCATION:
                $this->objUserGeolocation->setGeolocated(true);
                break;
            case self::IP_LOCATION:
                $this->objUserGeolocation->setIPLookup(true);
                break;

            case self::FALLBACK:
                $this->objUserGeolocation->setFallback(true);
                break;

            case self::DEACTIVAIT:
                $this->objUserGeolocation->setDeactivated(true);
                break;

            case self::CHANGE_BY_USER:
                $this->objUserGeolocation->setChangeByUser(true);
                break;

            default:
                throw new Exception("Unknown state: $intMode");
                break;
        }
    }

    /**
     * User the Contao function to get a full country name for a short tag
     * 
     * @param type $strShort
     * @return boolean 
     */
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
     * Insert JS vars for geoCore and geolocation 
     * 
     * @param string $strContent
     * @param string $strTemplate
     * @return String 
     */
    public function insertJSVars($strContent, $strTemplate)
    {
        if ($strTemplate == "fe_page")
        {
            // Init array if isn't set
            if (!is_array($GLOBALS['TL_LANG']['MSC']['JS']))
            {
                $GLOBALS['TL_LANG']['MSC']['JS'] = array();
            }

            if (!is_array($GLOBALS['TL_LANG']['ERR']['JS']))
            {
                $GLOBALS['TL_LANG']['ERR']['JS'] = array();
            }

            // Load duration time for cookies
            $arrDurations = deserialize($GLOBALS['TL_CONFIG']['geo_cookieDuration']);

            // Build html code
            $strJS = "";
            $strJS .= "<script>";
            $strJS .= " var geo_cookieDurationW3C = " . (is_numeric($arrDurations[0]) ? $arrDurations[0] : "0") . ";";
            $strJS .= " var geo_cookieDurationUser = " . (is_numeric($arrDurations[1]) ? $arrDurations[1] : "0") . ";";

            // Define language
            foreach ($GLOBALS['TL_LANG']['MSC']['JS'] as $key => $value)
            {
                $strJS .= " var $key = '$value';";
            }

            foreach ($GLOBALS['TL_LANG']['ERR']['JS'] as $key => $value)
            {
                $strJS .= " var $key = '$value';";
            }

            $strJS .= " </script>";
            $strJS .= "\n";

            // Insert into html 
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
     * @return GeolocationContainer
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

        // Try to loat information from lookup services
        $arrLookUpServices    = deserialize($GLOBALS['TL_CONFIG']['geo_GeolookUpSettings']);
        $objGeolocationResult = FALSE;

        // Run trough each service
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

        // Check if we have a positive result form one of the lookup services
        if ($objGeolocationResult === FALSE)
        {
            return FALSE;
        }
        else
        {
            $objGeolocation->setGeolocated(true);
            $objGeolocation->setIPLookup(false);
            $objGeolocation->setFallback(false);
            $objGeolocation->setChangeByUser(false);

            $objGeolocation->setFailed(false);
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
    public function doIPLookUp(GeolocationContainer $objGeolocation)
    {
        // No values found, so try to get the country
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
                // Set information for geolocation
                $objGeolocation->setCountry($this->getCountryByShortTag($GLOBALS['TL_CONFIG']['geo_countryFallback']));
                $objGeolocation->setCountryShort($GLOBALS['TL_CONFIG']['geo_countryFallback']);

                $objGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $objGeolocation->getIP()));

                $objGeolocation->setGeolocated(false);
                $objGeolocation->setIPLookup(false);
                $objGeolocation->setFallback(true);
                $objGeolocation->setChangeByUser(false);

                $objGeolocation->setFailed(false);
                $objGeolocation->setError("");
                $objGeolocation->setErrorID(0);
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            // Set information for geolocation
            $objGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $objGeolocation->getIP()));

            $objGeolocation->setGeolocated(false);
            $objGeolocation->setIPLookup(true);
            $objGeolocation->setFallback(false);
            $objGeolocation->setChangeByUser(false);

            $objGeolocation->setFailed(false);
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

        try
        {
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
            $this->saveSession();

            // Set Information about location
            $arrReturn["lat"]          = $this->objUserGeolocation->getLat();
            $arrReturn["lon"]          = $this->objUserGeolocation->getLon();
            $arrReturn["countryShort"] = $this->objUserGeolocation->getCountryShort();
            $arrReturn["error"]        = $this->objUserGeolocation->getError();

            if ($this->objUserGeolocation->isChangeByUser())
            {
                $arrReturn["mode"] = self::CHANGE_BY_USER;
            }
            else if ($this->objUserGeolocation->isGeolocated())
            {
                $arrReturn["mode"] = self::GEO_LOCATION;
            }
            else if ($this->objUserGeolocation->isIPLookup())
            {
                $arrReturn["mode"] = self::IP_LOCATION;
            }
            else if ($this->objUserGeolocation->isFallback())
            {
                $arrReturn["mode"] = self::FALLBACK;
            }
            else if ($this->objUserGeolocation->isDeactivated())
            {
                $arrReturn["mode"] = self::DEACTIVAIT;
            }
        }
        catch (Exception $exc)
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = "Unknown error.";

            $this->log($exc->getMessage(), __CLASS__ . " | " . __FUNCTION__, TL_ERROR);
        }

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

        // Check if lat/lon is set
        if ((strlen($this->Input->post("lat")) == 0 || strlen($this->Input->post("lon")) == 0) && (strlen($this->Input->post("country")) == 0 || strlen($this->Input->post("countryShort")) == 0))
        {
            throw new Exception("Missing longitude/latitude or country/countryShort.");
        }

        $this->objUserGeolocation->setLat($this->Input->post("lat"));
        $this->objUserGeolocation->setLon($this->Input->post("lon"));

        // Do geolocation look up
        $objResultLocation = $this->doGeoLookUP($this->objUserGeolocation);

        // Check if there was a result
        if ($objResultLocation == FALSE)
        {
            // Do a ip look up as fallback
            $this->objUserGeolocation->setIP($this->Environment->ip);
            $objResultLocation = $this->doIPLookUp($this->objUserGeolocation);

            // Check if we have a result
            if ($objResultLocation == FALSE)
            {
                // Error handling
                $arrReturn["success"] = false;
                $arrReturn["error"]   = "No results from lookup services.";

                $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));

                $this->objUserGeolocation->setTracked(TRUE);
                $this->changeState(self::DEACTIVAIT);
            }
            else
            {
                $this->objUserGeolocation = $objResultLocation;
                $this->objUserGeolocation->setTracked(TRUE);
            }
        }
        else
        {
            $this->objUserGeolocation = $objResultLocation;
            $this->objUserGeolocation->setTracked(TRUE);
        }

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
        $this->objUserGeolocation->setIP($this->Environment->ip);
        $objResultLocation = $this->doIPLookUp($this->objUserGeolocation);

        if ($objResultLocation == FALSE)
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = "No results from lookup services.";

            $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));

            $this->objUserGeolocation->setDeactivated(TRUE);
            $this->objUserGeolocation->setTracked(TRUE);
        }
        else
        {
            $this->objUserGeolocation = $objResultLocation;
            $this->objUserGeolocation->setTracked(TRUE);
        }

        return $arrReturn;
    }

    protected function ajaxChangeLocation($arrReturn)
    {
        if (strlen($this->Input->post("location")) == 0)
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = "Missing location.";

            return $arrReturn;
        }

        try
        {
            $this->setUserGeolocationByShortCountry($this->Input->post("location"));
        }
        catch (Exception $exc)
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = $exc->getMessage();
        }

        return $arrReturn;
    }

}

?>