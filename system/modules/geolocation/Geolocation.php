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

        // Get geolocation from session or create a new one        
        $objGeoProtection = $this->Session->get("geoprotection");

        if ($objGeoProtection != null && is_object($objGeoProtection))
        {
            $this->objUserGeolocation = $objGeoProtection;
        }
        else
        {
            // Init functions
            $this->objUserGeolocation = new GeolocationContainer();

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

                            $this->Session->set("geoprotection", $this->objUserGeolocation);

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

    // Functions ---------------------------------------------------------------

    /**
     * Get either the current geolocation from session.
     * If no session was found, check ip override or return a new 
     * empty geolocation.
     * 
     * @return GeolocationContainer 
     */
    public function getUseGeolocation()
    {
        return $this->objUserGeolocation;
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
            $this->objUserGeolocation->setCountry($arrCountries[$strCountryShort]);
            $this->objUserGeolocation->setCountryShort($strCountryShort);
            $this->objUserGeolocation->setChangeByUser(true);
            $this->objUserGeolocation->setFailed(false);
            $this->objUserGeolocation->setGeolocated(false);
            $this->objUserGeolocation->setIPLookup(false);
            $this->objUserGeolocation->setFallback(false);
            $this->objUserGeolocation->setIP("");
            $this->objUserGeolocation->setError("");
            $this->objUserGeolocation->setErrorID(0);

            // Save in Session
            $this->Session->set("geoprotection", $this->objUserGeolocation);
        }
        else
        {
            throw new Exception("Unknown country tag: $strCountryShort");
        }
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
        $this->Session->set("geoprotection", $this->objUserGeolocation);

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

            // Check if user has set his location
            if (strlen($this->Input->post("countryShort")) != null)
            {
                $this->setUserGeolocation($this->Input->post("countryShort"));

                $arrReturn["value"] = "Set location by user cookies.";
                return $arrReturn;
            }

            // Do geolocation look up
            $this->objUserGeolocation = $this->doGeoLookUP($this->Input->get("lat"), $this->Input->get("lon"));

            // Check if there was a result
            if ($this->objUserGeolocation->isFailed())
            {
                // Do a ip look up as fallback
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
            $this->objUserGeolocation = $this->doIPLookUp($this->objUserGeolocation);

            // Error handling
            $arrReturn["success"] = false;
            $arrReturn["error"]   = $exc->getMessage();
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
        $this->objUserGeolocation->setCountry("");
        $this->objUserGeolocation->setCountryShort("");
        $this->objUserGeolocation->setIP("");
        $this->objUserGeolocation->setIPLookup(false);
        $this->objUserGeolocation->setFailed(true);
        $this->objUserGeolocation->setGeolocated(false);
        $this->objUserGeolocation->setFallback(false);
        $this->objUserGeolocation->setError($strError);
        $this->objUserGeolocation->setErrorID($this->Input->post("errID"));

        // Get Geolocation from IP
        $this->objUserGeolocation = $this->doIPLookUp($this->objUserGeolocation);

        if ($this->objUserGeolocation->isFailed())
        {
            $arrReturn["success"] = false;
            $arrReturn["error"]   = $this->objUserGeolocation->getError();
        }
        else
        {
            if($this->objUserGeolocation->isFallback() == true)
            {
                $arrReturn["success"] = true;
                $arrReturn["value"]   = "Set location by fallback";
            }
            else
            {
                $arrReturn["success"] = true;
                $arrReturn["value"]   = "Set location by ip";
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
    }

    /**
     *
     * @param type $strLat
     * @param type $strLon
     * @return \GeolocationContainer
     * @throws Exception 
     */
    public function doGeoLookUP($strLat, $strLon)
    {
        $objGeolocation = new GeolocationContainer();

        // Split 
        $arrLat = trimsplit(".", $strLat);
        $arrLon = trimsplit(".", $strLon);

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
            $arrLookUpServices = deserialize($GLOBALS['TL_CONFIG']['geo_lookUpSettingsGeo']);
            $arrCountries      = $this->getCountries();
            $strCountryShort   = FALSE;

            foreach ($arrLookUpServices as $value)
            {
                $objLookUpService = GeoLookUpFactory::getEngine($value["lookUpClass"]);
                $strCountryShort  = $objLookUpService->getLocation($value["lookUpConfig"], implode(".", $arrLat), implode(".", $arrLon), NULL);

                if ($strCountryShort !== FALSE)
                {
                    break;
                }
            }

            if ($strCountryShort === FALSE)
            {
                // Set information for geolocation
                $objGeolocation->setCountry("");
                $objGeolocation->setCountryShort("");
                $objGeolocation->setIP("");
                $objGeolocation->setIPLookup(false);
                $objGeolocation->setFailed(true);
                $objGeolocation->setGeolocated(false);
                $objGeolocation->setFallback(false);
                $objGeolocation->setError("No geolocation found.");
                $objGeolocation->setErrorID(-1);
            }
            else
            {
                $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                        ->set(array("lat" => implode(".", $arrLat), "lon" => implode(".", $arrLon), "create_on" => time(), "country" => $arrCountries[$strCountryShort], "country_short" => $strCountryShort))
                        ->execute();

                // Set information for geolocation
                $objGeolocation->setCountry($arrCountries[$strCountryShort]);
                $objGeolocation->setCountryShort($strCountryShort);
                $objGeolocation->setIP("");
                $objGeolocation->setIPLookup(false);
                $objGeolocation->setFailed(false);
                $objGeolocation->setGeolocated(true);
                $objGeolocation->setFallback(false);
            }
        }
        // Found a entry in database
        else
        {
            // Set information for geolocation
            $objGeolocation->setCountry($objResult->country);
            $objGeolocation->setCountryShort($objResult->country_short);
            $objGeolocation->setIP("");
            $objGeolocation->setIPLookup(false);
            $objGeolocation->setFailed(false);
            $objGeolocation->setGeolocated(true);
            $objGeolocation->setFallback(false);
        }

        return $objGeolocation;
    }

    /**
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
            $arrLookUpServices = deserialize($GLOBALS['TL_CONFIG']['geo_lookUpSettingsIP']);
            $arrCountries      = $this->getCountries();
            $strCountryShort   = FALSE;

            foreach ($arrLookUpServices as $value)
            {
                $objLookUpService = GeoLookUpFactory::getEngine($value["lookUpClass"]);
                $strCountryShort  = $objLookUpService->getLocation($value["lookUpConfig"], NULL, NULL, $this->Environment->ip);

                if ($strCountryShort !== FALSE)
                {
                    break;
                }
            }

            if ($strCountryShort === FALSE)
            {
                // Check if a fallback is define
                if ($GLOBALS['TL_CONFIG']['geo_activateCountryFallback'] == true)
                {
                    $strCountryFallback = $GLOBALS['TL_CONFIG']['geo_countryFallback'];

                    // Set information for geolocation
                    $objGeolocation->setCountry($arrCountries[$strCountryFallback]);
                    $objGeolocation->setCountryShort($strCountryFallback);
                    $objGeolocation->setIP("");
                    $objGeolocation->setIPLookup(false);
                    $objGeolocation->setFailed(false);
                    $objGeolocation->setGeolocated(false);
                    $objGeolocation->setFallback(true);
                }
                else
                {
                    // Set information for geolocation
                    $objGeolocation->setCountry("");
                    $objGeolocation->setCountryShort("");
                    $objGeolocation->setIP("");
                    $objGeolocation->setIPLookup(false);
                    $objGeolocation->setFailed(true);
                    $objGeolocation->setGeolocated(false);
                    $objGeolocation->setFallback(false);
                    $objGeolocation->setError("No geolocation|IP|Fallback found.");
                    $objGeolocation->setErrorID(-1);
                }
            }
            else
            {
                $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                        ->set(array("ipnum" => $ipNum, "create_on" => time(), "country" => $arrCountries[$strCountryShort], "country_short" => $strCountryShort))
                        ->execute();

                // Set information for geolocation
                $objGeolocation->setCountry($arrCountries[$strCountryShort]);
                $objGeolocation->setCountryShort($strCountryShort);
                $objGeolocation->setIP(preg_replace("/\..*$/", ".XXX", $this->Environment->ip));
                $objGeolocation->setIPLookup(true);
                $objGeolocation->setFailed(false);
                $objGeolocation->setGeolocated(false);
                $objGeolocation->setFallback(false);
            }
        }
        // Found a entry in database
        else
        {
            // Set information for geolocation
            $objGeolocation->setCountry($objResult->country);
            $objGeolocation->setCountryShort($objResult->country_short);
            $objGeolocation->setIP(preg_replace("/\..*$/", ".XXX", $this->Environment->ip));
            $objGeolocation->setIPLookup(true);
            $objGeolocation->setFailed(false);
            $objGeolocation->setGeolocated(false);
            $objGeolocation->setFallback(false);
        }

        return $objGeolocation;
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
            $this->loadLanguageFile("Geolocation");

            if (!is_array($GLOBALS['TL_LANG']['Geolocation']['js']))
            {
                $GLOBALS['TL_LANG']['Geolocation']['js'] = array();
            }

            $arrDurations = deserialize($GLOBALS['TL_CONFIG']['gp_cookieDuration']);

            $strJS = "";
            $strJS .= "<script>";
            $strJS .= "\n";
            $strJS .= "var geo_cookieEnabeld = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true) ? "true" : "false");
            $strJS .= "\n";
            $strJS .= "var geo_cookieDurationW3C = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true && is_numeric($arrDurations[0])) ? $arrDurations[0] : "0");
            $strJS .= "\n";
            $strJS .= "var geo_cookieDurationUser = " . (($GLOBALS['TL_CONFIG']['gp_activateCookies'] == true && is_numeric($arrDurations[1])) ? $arrDurations[1] : "0");
            $strJS .= "\n";

            foreach ($GLOBALS['TL_LANG']['Geolocation']['js'] as $key => $value)
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