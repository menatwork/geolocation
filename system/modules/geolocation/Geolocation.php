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
 * Class Geolocation
 *
 * Provide methods for geolocation
 * 
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class Geolocation extends Frontend
{

    /**
     * Container for geo information
     * @var GeolocationContainer 
     */
    protected $objUserGeolocation;

    /**
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
                    if ($value["ipAddress"] == $_SERVER['REMOTE_ADDR'])
                    {
                        $this->objUserGeolocation = new GeolocationContainer();

                        $this->objUserGeolocation->setCountry($arrCountries[$strCountryShort]);
                        $this->objUserGeolocation->setCountryShort($strCountryShort);

                        $this->objUserGeolocation->setTracked(true);
                        $this->objUserGeolocation->setTrackType(GeolocationContainer::LOCATION_IP_OVERRIDE);

                        $this->objUserGeolocation->setFailed(false);
                        $this->objUserGeolocation->setError("");
                        $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);

                        return;
                    }
                }
            }
        }

        // Import classes
        $this->import("Session");
        $this->import("Environment");
        $this->import("Input");

        // Try to load the geolocation container from session or cookie
        if (($booLoadBySession = $this->loadSession()) == false)
        {
            if (($booLoadByCookie = $this->loadCookie()) == false)
            {
                $this->objUserGeolocation = new GeolocationContainer();

                $this->saveSession();
                $this->saveCookie();
            }
        }

        // Save contaoner in session or cookie
        if ($this->objUserGeolocation->isTracked() == true)
        {
            if ($booLoadByCookie == true)
            {
                $this->saveSession();
            }

            if ($booLoadBySession == true)
            {
                $this->saveCookie();
            }

            return;
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

    /* -------------------------------------------------------------------------
     * Session / Cookies
     */

    /**
     * Save the container $this->objUserGeolocation into the Session 
     */
    protected function saveSession()
    {        
        $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));
        $this->Session->set("geolocation", $this->objUserGeolocation);
    }

    /**
     * Load from the Session the geolocation into $this->objUserGeolocation
     * 
     * @return boolean True => Load | False => no Data
     */
    protected function loadSession()
    {
        $mixGeolocation = $this->Session->get("geolocation");

        if (is_object($mixGeolocation))
        {
            $this->objUserGeolocation = $mixGeolocation;
            return true;
        }

        return false;
    }

    /**
     * Save the geolocation container to cookie 
     */
    protected function saveCookie()
    {
        $arrDuration = deserialize($GLOBALS['TL_CONFIG']['geo_cookieDuration']);
        if (!is_array($arrDuration) || count($arrDuration) != 2)
        {
            $arrDuration = array(5, 1);
        }

        $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));

        // User another lifetime for cookies if the geolocation failed or is deactivated
        if ($this->objUserGeolocation->isFailed() == true)
        {
            $this->setCookie("geolocation", serialize($this->objUserGeolocation), time() + 60 * 60 * 24 * intval($arrDuration[1]));
        }
        else if ($this->objUserGeolocation->getTrackType() == GeolocationContainer::LOCATION_BY_USER)
        {
            $this->setCookie("geolocation", serialize($this->objUserGeolocation), time() + 60 * 60 * 24 * intval($arrDuration[1]));
        }
        else
        {
            $this->setCookie("geolocation", serialize($this->objUserGeolocation), time() + 60 * 60 * 24 * intval($arrDuration[0]));
        }
    }

    /**
     * Load the geolocation container from cookie
     * 
     * @return boolean True => Load | False => no Data
     */
    protected function loadCookie()
    {
        try
        {
            $mixGeolocation = $this->Input->cookie("geolocation");

            if (strlen($mixGeolocation) != 0)
            {
                if (!preg_match("/.*GeolocationContainer.*/", $mixGeolocation))
                {
                    return false;
                }

                $mixGeolocation = @unserialize($mixGeolocation);

                if ($mixGeolocation != false || is_object($mixGeolocation))
                {
                    $this->objUserGeolocation = $mixGeolocation;                    
                    return true;
                }
            }
        }
        catch (Exception $exc)
        {
            return false;
        }

        return false;
    }

    /* -------------------------------------------------------------------------
     * Getter / Setter
     */

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

            $this->objUserGeolocation->setTracked(true);
            $this->objUserGeolocation->setTrackType(GeolocationContainer::LOCATION_BY_USER);

            $this->objUserGeolocation->setFailed(false);
            $this->objUserGeolocation->setError("");
            $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);

            // Save in session
            $this->saveSession();
            $this->saveCookie();
        }
        else
        {
            throw new Exception("Unknown country tag: $strCountryShort");
        }
    }

    /* -------------------------------------------------------------------------
     * Helper / Hooks
     */

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
        if ($strTemplate != "fe_page")
        {
            return $strContent;
        }

        // Build html code
        $strJS = "";
        $strJS .= "<script type=\"text/javascript\">";

        $strJS .= "var RunGeolocation = new Geolocation({options:
                {messages:{
                    geo_err_NoConnection: '{$GLOBALS['TL_LANG']['ERR']['geo_err_NoConnection']}',                     
                    geo_err_PermissionDenied: '{$GLOBALS['TL_LANG']['ERR']['geo_err_PermissionDenied']}',
                    geo_err_PositionUnavailable: '{$GLOBALS['TL_LANG']['ERR']['geo_err_PositionUnavailable']}',
                    geo_err_TimeOut: '{$GLOBALS['TL_LANG']['ERR']['geo_err_TimeOut']}',
                    geo_err_UnsupportedBrowser: '{$GLOBALS['TL_LANG']['ERR']['geo_err_UnsupportedBrowser']}',
                    geo_err_UnknownError: '{$GLOBALS['TL_LANG']['ERR']['geo_err_UnknownError']}',
                    geo_msc_Start: '{$GLOBALS['TL_LANG']['MSC']['geo_msc_Start']}',
                    geo_msc_Finished: '{$GLOBALS['TL_LANG']['MSC']['geo_msc_Finished']}',
                    geo_msc_Changing: '{$GLOBALS['TL_LANG']['MSC']['geo_msc_Changing']}'
                }}});";
        $strJS .= "</script>";
        $strJS .= "\n</head>";

        // Insert into html
        $strContent = str_replace('</head>', $strJS, $strContent);

        // Build html code
        $strJS = "";
        $strJS .= "<script type=\"text/javascript\">";
        $strJS .= "window.addEvent('domready', function(){RunGeolocation.runGeolocation();});";
        $strJS .= "</script>";
        $strJS .= "\n</body>";

        // Insert into html          
        $strContent = str_replace('</body>', $strJS, $strContent);

        return $strContent;
    }

    /**
     * Set JS/Hook for geolocation 
     * 
     * @param Database_Result $objPage
     * @param Database_Result $objLayout
     * @param PageRegular $objPageRegular 
     */
    public function checkFrontpage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular)
    {
        // Check if we have allready a geolocation from user
        if ($this->objUserGeolocation->isTracked() == true)
        {
            return;
        }
        
        $arrMethods = array();

        // Check options for this page 
        if ($objPage->geo_single_page == true)
        {
            $arrMethods = deserialize($objPage->geo_single_choose);
        }
         // Check options for this page 
        else if ($objPage->geo_child_page == true)
        {
            $arrMethods = deserialize($objPage->geo_child_choose);
        }        
        // Check options from parentpages
        else
        {
            $intID = $objPage->pid;

            while ($intID != 0)
            {
                $arrResult = $this->Database
                        ->prepare("SELECT * FROM tl_page WHERE id =?")
                        ->execute($intID);

                if ($arrResult->numRows == 0)
                {
                    break;
                }

                $intID = $arrResult->pid;

                if ($arrResult->geo_child_choose == true)
                {
                    $arrMethods = deserialize($arrResult->geo_child_choose);
                    break;
                }
            }
        }
        
        // Rest current running loacatin operation, if not in current methods
        if (in_array(!$this->objUserGeolocation->getTrackRunning(), $arrMethods))
        {
            $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);
            $this->objUserGeolocation->setFailed(false);
            $this->objUserGeolocation->setError("");
            $this->objUserGeolocation->setErrorID("");
        }

        // Check if optios were found
        if (count($arrMethods) != 0)
        {
            // Check each option
            foreach ($arrMethods as $value)
            {
                // Flag for skipping this foreach
                $booBreakOutter      = false;
                // Some informations
                $intCurrenRunning    = $this->objUserGeolocation->getTrackRunning();
                $intMethodsStillLeft = count($arrMethods) - $this->objUserGeolocation->countTrackFinished();

                switch ($value)
                {
                    case "w3c":
                        $booNoneRunning = $intCurrenRunning == GeolocationContainer::LOCATION_NONE;
                        $booAllreadyRun = $this->objUserGeolocation->isTrackFinished(GeolocationContainer::LOCATION_W3C);

                        // Check if no other method is running and if this is the first time
                        if ($booNoneRunning == true && $booAllreadyRun == false)
                        {
                            // Include js and hock fpr w3c
                            $GLOBALS['TL_JAVASCRIPT']['geoCore']             = "system/modules/geolocation/html/js/geoCore.js";
                            $GLOBALS['TL_HOOKS']['parseFrontendTemplate'][]  = array('Geolocation', 'insertJSVars');

                            // Set information
                            $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_W3C);
                            $this->objUserGeolocation->setFailed(false);

                            //Break foreach
                            $booBreakOutter = true;
                        }
                        // Check if W3C is running but with errors
                        else if ($intCurrenRunning == GeolocationContainer::LOCATION_W3C && $this->objUserGeolocation->isFailed() == true)
                        {
                            // Set information
                            $this->objUserGeolocation->addTrackFinished(GeolocationContainer::LOCATION_W3C);
                            $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);

                            // Set tracked true, if this the only method
                            if ($intMethodsStillLeft == 1)
                            {
                                $this->objUserGeolocation->setTracked(true);
                                $this->objUserGeolocation->setFailed(true);
                            }
                        }
                        // Check if W3C is running 
                        else if ($intCurrenRunning == GeolocationContainer::LOCATION_W3C)
                        {
                            // Check if this is the only method
                            if ($intMethodsStillLeft == 1)
                            {
                                $GLOBALS['TL_JAVASCRIPT']['geoCore']            = "system/modules/geolocation/html/js/geoCore.js";
                                $GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('Geolocation', 'insertJSVars');

                                $booBreakOutter = true;
                            }
                            else
                            {
                                // Skip W3C and use next
                                $this->objUserGeolocation->addTrackFinished(GeolocationContainer::LOCATION_W3C);
                                $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);
                            }
                        }
                        break;

                    case "ip":
                        $booNoneRunning = $intCurrenRunning == GeolocationContainer::LOCATION_NONE;
                        $booAllreadyRun = $this->objUserGeolocation->isTrackFinished(GeolocationContainer::LOCATION_IP);

                        // Check if no other method is running and if this is the first time
                        if ($booNoneRunning == true && $booAllreadyRun == false)
                        {
                            $this->objUserGeolocation->setFailed(false);

                            // Try to load the location from IP
                            $this->objUserGeolocation->setIP($_SERVER['REMOTE_ADDR']);

                            $objResult = $this->doIPLookUp($this->objUserGeolocation);

                            // Check if we got a result
                            if ($objResult == FALSE)
                            {
                                $this->objUserGeolocation->addTrackFinished(GeolocationContainer::LOCATION_IP);
                                $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);

                                $this->objUserGeolocation->setError("No ip result.");
                                $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NO_IP_RESULT);
                                
                                // Set information for geolocation
                                $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));

                                if ($intMethodsStillLeft == 1)
                                {
                                    $this->objUserGeolocation->setTracked(true);
                                    $this->objUserGeolocation->setFailed(true);
                                }
                            }
                            else
                            {
                                // Got a result
                                $this->objUserGeolocation = $objResult;
                            }
                        }
                        break;

                    case "fallback":
                        $booNoneRunning = $intCurrenRunning == GeolocationContainer::LOCATION_NONE;
                        $booAllreadyRun = $this->objUserGeolocation->isTrackFinished(GeolocationContainer::LOCATION_FALLBACK);

                        // Check if a fallback is define
                        if ($booNoneRunning == true && $booAllreadyRun == false)
                        {
                            if (strlen($GLOBALS['TL_CONFIG']['geo_countryFallback']) != 0)
                            {
                                // Set information for geolocation
                                $this->objUserGeolocation->setCountry($this->getCountryByShortTag($GLOBALS['TL_CONFIG']['geo_countryFallback']));
                                $this->objUserGeolocation->setCountryShort($GLOBALS['TL_CONFIG']['geo_countryFallback']);

                                $this->objUserGeolocation->setTracked(true);
                                $this->objUserGeolocation->setTrackType(GeolocationContainer::LOCATION_FALLBACK);
                                $this->objUserGeolocation->addTrackFinished(GeolocationContainer::LOCATION_FALLBACK);

                                $this->objUserGeolocation->setFailed(false);
                                $this->objUserGeolocation->setError("");
                                $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);
                            }
                            else
                            {
                                $this->objUserGeolocation->addTrackFinished(GeolocationContainer::LOCATION_FALLBACK);
                                $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);

                                $this->objUserGeolocation->setFailed(true);
                                $this->objUserGeolocation->setError("No fallback defined");
                                $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_POSITION_UNAVAILABLE);

                                if ($intMethodsStillLeft == 1)
                                {
                                    $this->objUserGeolocation->setTracked(true);
                                    $this->objUserGeolocation->setFailed(true);
                                }
                            }
                        }

                        break;

                    default:
                        break;
                }

                if ($booBreakOutter == true)
                {
                    break;
                }
            }

            // Save in Session and cookie
            $this->saveSession();
            $this->saveCookie();
        }
    }

    /* -------------------------------------------------------------------------
     * Functions
     */

    /**
     * User lookup services to get information about a lat/lon value
     * 
     * @param GeolocationContainer $objGeolocation
     * @return boolean|GeolocationContainer Fales if no result else a GeolocationContainer
     * @throws Exception 
     */
    public function doGeoLookUP(GeolocationContainer $objGeolocation)
    {
        // Split 
        $arrLat = trimsplit(".", $objGeolocation->getLat());
        $arrLon = trimsplit(".", $objGeolocation->getLon());

        // Check if we have two values
        if (count($arrLat) != 2 || count($arrLon) != 2)
        {
            return false;
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
            return false;
        }
        else
        {
            $objGeolocation->setTracked(true);
            $objGeolocation->setTrackType(GeolocationContainer::LOCATION_W3C);
            $objGeolocation->addTrackFinished(GeolocationContainer::LOCATION_W3C);

            $objGeolocation->setFailed(false);
            $objGeolocation->setError("");
            $objGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);
        }

        return $objGeolocation;
    }

    /**
     * User lookup service to get informations about a ip adress
     * 
     * @param GeolocationContainer $objGeolocation
     * @return boolean|GeolocationContainer Fales if no result else a GeolocationContainer
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
            return false;
        }
        else
        {
            // Set information for geolocation
            $objGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $objGeolocation->getIP()));

            $objGeolocation->setTracked(true);
            $objGeolocation->setTrackType(GeolocationContainer::LOCATION_IP);
            $objGeolocation->addTrackFinished(GeolocationContainer::LOCATION_IP);

            $objGeolocation->setFailed(false);
            $objGeolocation->setError("");
            $objGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);
        }

        return $objGeolocation;
    }

    /* -------------------------------------------------------------------------
     * AJAX Calls
     */

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
            "error" => "",
            "lang" => $GLOBALS['TL_LANGUAGE']
        );
        
        // Try to load the geolocation container from session or cookie
        if (($booLoadBySession = $this->loadCookie()) == false)
        {
            if (($booLoadByCookie = $this->loadSession()) == false)
            {
                $this->objUserGeolocation = new GeolocationContainer();
                $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_W3C);
            }
        }

        try
        {
            // Chose function
            switch ($this->Input->post("action"))
            {
                /**
                 * Set geolocation for user.
                 * Try to get the lat/lon from lookup service.
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
            $this->saveCookie();
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
     * @return array
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
            $this->objUserGeolocation->setFailed(true);

            $this->objUserGeolocation->setError("No W3C result.");
            $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NO_W3C_RESULT);
        }
        else
        {
            $this->objUserGeolocation = $objResultLocation;
        }

        // Return debug information
        return $arrReturn;
    }

    /**
     *
     * @param type $arrReturn
     * @return array 
     */
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

        $this->objUserGeolocation->setFailed(true);

        // Set information for geolocation
        $this->objUserGeolocation->setError($strError);
        $this->objUserGeolocation->setErrorID($this->Input->post("errID"));

        return $arrReturn;
    }

    /**
     *
     * @param type $arrReturn
     * @return array 
     */
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