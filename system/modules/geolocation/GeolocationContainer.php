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
 * Class GeolocationContainer
 *
 * Container class for geolocation information
 * 
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
    protected $arrCountries;        // Countryname long
    protected $arrCountriesShort;   // Countryname short, 2 chars
    protected $strIP;               // IP
    protected $strLat;              // Lat
    protected $strLon;              // Long
    // State   
    protected $arrTrackFinished;    // Array with finished tracking functions
    protected $intTrackRunning;     // Current running track type
    protected $intTrackType;        // Tracking type
    protected $booTracked;          // Tracking finished
    protected $booFailed;           // Something goes wrong
    // Error
    protected $strError;            // Error msg
    protected $intError;            // Error ID

    /* -------------------------------------------------------------------------
     * Basic functions
     */

    public function __construct()
    {
        $this->arrCountries      = array();
        $this->arrCountriesShort = array();
        $this->strIP             = "";
        $this->strLon            = "";
        $this->strLat            = "";
        $this->arrTrackFinished  = array();
        $this->intTrackRunning   = self::LOCATION_NONE;
        $this->intTrackType      = self::LOCATION_NONE;
        $this->booTracked        = false;
        $this->booFailed         = false;
        $this->strError          = "";
        $this->intError          = self::ERROR_NONE;
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

    /**
     * Unserialize 
     * 
     * @param type $serialized 
     */
    public function unserialize($serialized)
    {
        // Set default values
        $this->arrCountries      = array();
        $this->arrCountriesShort = array();
        $this->strIP             = "";
        $this->strLon            = "";
        $this->strLat            = "";
        $this->arrTrackFinished  = array();
        $this->intTrackRunning   = self::LOCATION_NONE;
        $this->intTrackType      = self::LOCATION_NONE;
        $this->booTracked        = false;
        $this->booFailed         = false;
        $this->strError          = "";
        $this->intError          = self::ERROR_NONE;
        
        // Get a list with all DefaultProperties
        $reflectionClass      = new ReflectionClass('GeolocationContainer');
        $reflectionProperties = $reflectionClass->getDefaultProperties();

        // Deserialize the data and check it
        $serialized = @unserialize($serialized);
        if(!is_array($serialized))
        {
            $serialized = array($serialized);
        }
                        
        foreach ($serialized as $key => $value)
        {
            if (array_key_exists($key, $reflectionProperties))
            {
                $this->$key = $value;
            }
        }
        
        if(!is_array($this->arrTrackFinished))
        {
            $this->arrTrackFinished = array();
        }
        
        // Old version mode
        if(array_key_exists('strCountry', $serialized))
        {
            $this->arrCountries = array($serialized['strCountry']);
        }
        
        if(array_key_exists('strCountryShort', $serialized))
        {
            $this->arrCountriesShort = array($serialized['strCountryShort']);
        }
        
        // Check if we have really an array for some vars
        if(!is_array($this->arrCountriesShort))
        {
            if(empty($this->arrCountriesShort))
            {
                $this->arrCountriesShort = array();
            }
            else
            {
                $this->arrCountriesShort = (array) $this->arrCountriesShort;
            }
        }
              
        if(!is_array($this->arrCountries))
        {
            if(empty($this->arrCountries))
            {
                $this->arrCountries = array();
            }
            else
            {
                $this->arrCountries = (array) $this->arrCountries;
            }
        }
    }

    /**
     * Return all values as array
     * @return type 
     */
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

    /**
     * Get data from a array and try to set it as current values.     *
     * @param array $arrData List with all data.
     */
    public function appendData($arrData)
    {
        $reflectionClass = new ReflectionClass('GeolocationContainer');
        foreach (array_keys($reflectionClass->getDefaultProperties()) as $value)
        {
            if(array_key_exists($value, $arrData))
            {
                $this->$value = $arrData[$value];
            }
        }

        // Check if we have arrays where we need them.
        if(!is_array($this->arrTrackFinished))
        {
            $this->arrTrackFinished = array();
        }

        // Old version mode
        if(array_key_exists('strCountry', $arrData))
        {
            $this->arrCountries = array($arrData['strCountry']);
        }

        if(array_key_exists('strCountryShort', $arrData))
        {
            $this->arrCountriesShort = array($arrData['strCountryShort']);
        }

        // Check if we have really an array for some vars
        if(!is_array($this->arrCountriesShort))
        {
            if(empty($this->arrCountriesShort))
            {
                $this->arrCountriesShort = array();
            }
            else
            {
                $this->arrCountriesShort = (array) $this->arrCountriesShort;
            }
        }

        if(!is_array($this->arrCountries))
        {
            if(empty($this->arrCountries))
            {
                $this->arrCountries = array();
            }
            else
            {
                $this->arrCountries = (array) $this->arrCountries;
            }
        }
    }
   
    /* -------------------------------------------------------------------------
     * Getter / Setter for informations
     */

    public function getCountries()
    {
        if (count($this->arrCountries) == 0)
        {
            return array($GLOBALS['TL_LANG']['MSC']['GEO']['xx']);
        }

        return (array) $this->arrCountries;
    }

    public function setCountries($arrCountries)
    {
        $this->arrCountries = $arrCountries;
    }

    public function getCountriesShort()
    {
        if(count($this->arrCountriesShort) == 0)
        {
            return array('xx');
        }        
        
        return (array) $this->arrCountriesShort;
    }

    public function setCountriesShort($arrCountriesShort)
    {
        $this->arrCountriesShort = $arrCountriesShort;
    }

    public function getCountry($intIndex = 0)
    {
        if(array_key_exists($intIndex, $this->arrCountries))
        {
            return $this->arrCountries[$intIndex];
        }
        
        return $GLOBALS['TL_LANG']['MSC']['GEO']['xx'];
    }

    public function setCountry($strCountry)
    {
        if(!in_array($strCountry, $this->arrCountries))
        {
            $this->arrCountries[] = $strCountry;
        }
    }

    public function getCountryShort($intIndex = 0)
    {
        if(array_key_exists($intIndex, $this->arrCountriesShort))
        {
            return $this->arrCountriesShort[$intIndex];
        }
        
        return 'xx';
    }

    public function setCountryShort($strCountryShort)
    {
        if(!in_array($strCountryShort, $this->arrCountriesShort))
        {
            $this->arrCountriesShort[] = $strCountryShort;
        }
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
     * Check if a tracking type allready run
     * 
     * @param type $intTrackFinished
     * @return boolean 
     */
    public function isTrackFinished($intTrackFinished)
    {
        if(!is_array($this->arrTrackFinished))
        {
            $this->arrTrackFinished = array();
        }        
        
       if(in_array($intTrackFinished, $this->arrTrackFinished))
       {
           return true;
       }
       else
       {
           return false;
       }
    }

    /**
     * Add a finished tracking type
     * 
     * @param type $intTrackFinished 
     */
    public function addTrackFinished($intTrackFinished)
    {
        $this->arrTrackFinished[$intTrackFinished] = $intTrackFinished;
    }
    
    /**
     * Count how many traks allready finished
     * 
     * @return int 
     */
    public function countTrackFinished()
    {
        return count($this->arrTrackFinished);
    }

    /**
     * Get the current running tracking type
     * 
     * @return type 
     */
    public function getTrackRunning()
    {
        return $this->intTrackRunning;
    }

    /**
     * Set the current running tracking tpye.
     * 
     * @param type $intTrackRunning 
     */
    public function setTrackRunning($intTrackRunning)
    {
        $this->intTrackRunning = $intTrackRunning;
    }

    
    /**
     * Check if we allready tracked
     * 
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
     * Clean all countries, reinit all arrays
     */
    public function cleanAllCountries()
    {
        $this->arrCountries = array();
        $this->arrCountriesShort = array();
    }
    
    public function containsCountryShort($mixKey)
    {        
        if(!is_array($mixKey))
        {
            $mixKey = trimsplit(",", $mixKey);
        }
        
        if(count($this->arrCountriesShort) == 0 && in_array('xx', $mixKey))
        {
            return true;
        }
        
        foreach ($mixKey as $strKey)
        {
            if (in_array($strKey, $this->arrCountriesShort))
            {
                return true;
            }
        }

        return false;
    }

}

?>