<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2014
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
class Geolocation extends Controller
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
     * Flag to check if the location core function are finished
     * 
     * @var type 
     */
    protected $booRunFinished = false;

    /**
     * Constructor 
     */
    protected function __construct()
    {
        // Call parent constructor
        parent::__construct();
        $this->import('Database');

        // Check ip overwride
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

        if ($this->Environment->isAjaxRequest && strlen($this->Input->post("session")) != 0)
        {
            session_id($this->Input->post("session"));
        }

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

        if (!$this->Environment->isAjaxRequest && $this->objUserGeolocation->isTracked() != true)
        {
            $this->checkGeolocation();
        }
        else
        {
            $this->booRunFinished = true;
        }

        // Save container in session or cookie
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

        // Save all data from the container to the session.
        $arrData = $this->objUserGeolocation->asArray();

        // See #6747 in contao core.
        $arrSessionData = $this->Session->getData();
        if(empty($arrSessionData))
        {
            $this->Session->setData(array('geolocation' => $arrData));
        }
        else
        {
            $this->Session->appendData(array('geolocation' => $arrData));
        }
    }

    /**
     * Load from the Session the geolocation into $this->objUserGeolocation
     *
     * @return boolean True => Load | False => no Data
     */
    protected function loadSession()
    {
        try
        {
            $mixGeolocation = $this->Session->get("geolocation");

            if (is_array($mixGeolocation))
            {
                $this->objUserGeolocation = new GeolocationContainer();
                $this->objUserGeolocation->appendData($mixGeolocation);

                return true;
            }
        }
        catch (\Exception $exc)
        {
            // Nothing to do.
        }

        return false;
    }

    /**
     * Save the geolocation container to cookie 
     */
    protected function saveCookie()
    {
        $arrDuration = deserialize($GLOBALS['TL_CONFIG']['geo_cookieDuration']);
        if (!is_array($arrDuration) || count($arrDuration) != 3)
        {
            $arrDuration = array(5, 1, 1);
        }

        $this->objUserGeolocation->setIP(preg_replace("/\.\d?\d?\d?$/", ".0", $this->objUserGeolocation->getIP()));

        // Make a string from container
        $strCookieValue = 'GeolocationContainerV2';
        $strCookieValue .= '|';
        $strCookieValue .= implode(",", $this->objUserGeolocation->getCountriesShort());
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->getIP();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->getLat();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->getLon();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->getTrackRunning();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->getTrackType();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->isTracked();
        $strCookieValue .= '|';
        $strCookieValue .= $this->objUserGeolocation->isFailed();

        $strCookieName = ($GLOBALS['TL_CONFIG']['geo_cookieName']) ? $GLOBALS['TL_CONFIG']['geo_cookieName'] : 'geolocation';

        try
        {

            // User another lifetime for cookies if the geolocation failed or is deactivated
            if ($this->objUserGeolocation->isFailed() == true || $this->objUserGeolocation->getTrackType() == GeolocationContainer::LOCATION_NONE)
            {
                $this->setCookie($strCookieName, $strCookieValue, time() + 60 * 60 * 24 * intval($arrDuration[2]));
            }
            elseif ($this->objUserGeolocation->getTrackType() == GeolocationContainer::LOCATION_BY_USER)
            {
                $this->setCookie($strCookieName, $strCookieValue, time() + 60 * 60 * 24 * intval($arrDuration[1]));
            }
            else
            {
                $this->setCookie($strCookieName, $strCookieValue, time() + 60 * 60 * 24 * intval($arrDuration[0]));
            }

        }
        catch (\Exception $exc)
        {
            // Nothing to do.
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
            $strCookieName = ($GLOBALS['TL_CONFIG']['geo_cookieName']) ? $GLOBALS['TL_CONFIG']['geo_cookieName'] : 'geolocation';
            $mixGeolocation = $this->Input->cookie($strCookieName);

            if (strlen($mixGeolocation) != 0)
            {
                // New geolocation container
                if (preg_match("/.*GeolocationContainerV2.*/", $mixGeolocation))
                {
                    // Get init vars
                    $arrCountries       = $this->getCountries();
                    $arrValues          = trimsplit('|', $mixGeolocation);
                    $objUserGeolocation = new GeolocationContainer();
                    $objUserGeolocation->setTracked(false);
                    $objUserGeolocation->setFailed(false);

                    // Get countries
                    foreach (trimsplit(',', $arrValues[1]) as $value)
                    {
                        if (!key_exists($value, $arrCountries))
                        {
                            continue;
                        }

                        $objUserGeolocation->setCountryShort($value);
                    }

                    $objUserGeolocation->setIP($arrValues[2]);
                    $objUserGeolocation->setLat($arrValues[3]);
                    $objUserGeolocation->setLon($arrValues[4]);
                    $objUserGeolocation->setTrackRunning($arrValues[5]);
                    $objUserGeolocation->setTrackType($arrValues[6]);
                    $objUserGeolocation->setTracked((boolean) $arrValues[7]);
                    $objUserGeolocation->setFailed((boolean) $arrValues[8]);

                    $this->objUserGeolocation = $objUserGeolocation;
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
    public function setUserGeolocationByShortCountries($strCountriesShort)
    {
        // Clean up
        $this->objUserGeolocation->cleanAllCountries();

        $arrCountriesKeys = array_keys($this->getCountries());

        // Add new countries
        foreach ($strCountriesShort as $value)
        {
            if (in_array($value, $arrCountriesKeys))
            {
                $this->objUserGeolocation->setCountryShort($value);
            }
        }

        // Set meta informations
        $this->objUserGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);
        $this->objUserGeolocation->setTracked(true);
        $this->objUserGeolocation->setTrackType(GeolocationContainer::LOCATION_BY_USER);

        $this->objUserGeolocation->setFailed(false);
        $this->objUserGeolocation->setError("");
        $this->objUserGeolocation->setErrorID(GeolocationContainer::ERROR_NONE);

        // Save in session
        $this->saveSession();
        $this->saveCookie();
    }

    /* -------------------------------------------------------------------------
     * Helper / Hooks
     */

    /**
     * Use the Contao function to get a full country name for a short tag
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

        if (REQUEST_TOKEN == "REQUEST_TOKEN")
        {
            $strRequestToken = "";
        }
        else
        {
            $strRequestToken = REQUEST_TOKEN;
        }

        // Build html code
        $strJS = "";
        $strJS .= "<script type=\"text/javascript\">";
        $strJS .= "var RunGeolocation = new Geolocation({session:'" . session_id() . "',requesttoken:'" . $strRequestToken . "',messages:{";
        $strJS .= "noConnection:'{$GLOBALS['TL_LANG']['ERR']['GEO']['noConnection']}',";
        $strJS .= "permissionDenied:'{$GLOBALS['TL_LANG']['ERR']['GEO']['permissionDenied']}',";
        $strJS .= "positionUnavailable:'{$GLOBALS['TL_LANG']['ERR']['GEO']['positionUnavailable']}',";
        $strJS .= "timeOut:'{$GLOBALS['TL_LANG']['ERR']['GEO']['timeOut']}',";
        $strJS .= "unsupportedBrowser:'{$GLOBALS['TL_LANG']['ERR']['GEO']['unsupportedBrowser']}',";
        $strJS .= "unknownError:'{$GLOBALS['TL_LANG']['ERR']['GEO']['unknownError']}',";
        $strJS .= "start:'{$GLOBALS['TL_LANG']['MSC']['GEO']['start']}',";
        $strJS .= "finished:'{$GLOBALS['TL_LANG']['MSC']['GEO']['finished']}',";
        $strJS .= "changing:'{$GLOBALS['TL_LANG']['MSC']['GEO']['changing']}'";
        $strJS .= "}});";
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
    public function checkContentelement($objElement, $strBuffer)
    {
        if ($this->booRunFinished == true)
        {
            return $strBuffer;
        }

        // Check if we have allready a geolocation from user
        if ($this->objUserGeolocation->isTracked() == true)
        {
            $this->booRunFinished = true;
            return $strBuffer;
        }

        $this->checkGeolocation();

        return $strBuffer;
    }

    /**
     * Set JS/Hook for geolocation 
     * 
     * @param Database_Result $objPage
     * @param Database_Result $objLayout
     * @param PageRegular $objPageRegular 
     */
    public function checkModuleelement($strBuffer, $strTemplate)
    {
        if ($this->booRunFinished == true)
        {
            return $strBuffer;
        }

        // Check if we have allready a geolocation from user
        if ($this->objUserGeolocation->isTracked() == true)
        {
            $this->booRunFinished = true;
            return $strBuffer;
        }

        $this->checkGeolocation();

        return $strBuffer;
    }

    /**
     * Geolocation core functions
     * 
     * @global Object $objPage 
     */
    protected function checkGeolocation()
    {
        global $objPage;
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

        // Get current running method as string
        $stringCurrentRunning = "";
        switch ($this->objUserGeolocation->getTrackRunning())
        {
            case GeolocationContainer::LOCATION_W3C:
                $stringCurrentRunning = "w3c";
                break;

            case GeolocationContainer::LOCATION_IP:
                $stringCurrentRunning = "ip";
                break;

            case GeolocationContainer::LOCATION_FALLBACK:
                $stringCurrentRunning = "fallback";
                break;

            case GeolocationContainer::LOCATION_NONE:
            default :
                $stringCurrentRunning = "none";
        }

        // Rest current running loacatin operation, if not in current methods
        if (!in_array($stringCurrentRunning, $arrMethods))
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
                            $GLOBALS['TL_JAVASCRIPT']['geoCore']            = "system/modules/geolocation/html/js/geoCore.js";
                            $GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('Geolocation', 'insertJSVars');

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
                                $booBreakOutter = true;
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

        $this->booRunFinished = true;
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
            $objGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);

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
            $objGeolocation->setTrackRunning(GeolocationContainer::LOCATION_NONE);

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
            "value"   => "",
            "error"   => "",
            "lang"    => $GLOBALS['TL_LANGUAGE']
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

        // Get the input and make a unique array from it
        $mixLocation = $this->Input->post("location");
        $mixLocation = trimsplit(",", $mixLocation);
        $mixLocation = (array) $mixLocation;
        $mixLocation = array_keys(array_flip($mixLocation));

        try
        {
            $this->setUserGeolocationByShortCountries($mixLocation);
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
