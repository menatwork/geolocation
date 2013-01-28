<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Class GeoLookUpGeoplugin
 *
 * Provide methods for decoding messages from look up services
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class GeoLookUpGeoplugin extends Backend implements GeoLookUpInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @param String $strConfig
     * @param GeolocationContainer $objGeolocation
     * @return boolean|GeolocationContainer 
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation)
    {
        $objRequest = new Request();
        $objRequest->send(vsprintf($strConfig, array($objGeolocation->getIP())));

        if ($objRequest->code != 200)
        {
            $this->log("Error by location service: " . vsprintf("Request error code %s - %s ", array($objRequest->code, $objRequest->error)), __CLASS__ . " | " . __FUNCTION__, TL_ERROR);
            return false;
        }
        
        $arrResponse = deserialize($objRequest->response);
        
        if(!is_array($arrResponse))
        {
            return false;
        }
        
        $arrCountryShort = strtolower($arrResponse['geoplugin_countryCode']);
        
        $arrCountries = $this->getCountries();
        
        if(!key_exists($arrCountryShort, $arrCountries))
        {
            return false;
        }
        
        $objGeolocation->setCountryShort($arrCountryShort);
        $objGeolocation->setCountry($arrCountries[$arrCountryShort]);
        $objGeolocation->setLat($arrResponse['geoplugin_latitude']);
        $objGeolocation->setLon($arrResponse['geoplugin_longitude']);
        
        return $objGeolocation;
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return $GLOBALS['TL_LANG']['GEO']['geoplugin'];
    }

    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType()
    {
        return GeoLookUpInterface::IP;
    }
    

}

?>
