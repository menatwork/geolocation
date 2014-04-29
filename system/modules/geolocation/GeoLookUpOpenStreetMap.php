<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Class GeoProLookUpInterface
 *
 * Provide methods for decoding messages from look up services
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class GeoLookUpOpenStreetMap extends Backend implements GeoLookUpInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @param type $strConfig
     * @param GeolocationContainer $objGeolocation
     * @return boolean|\GeolocationContainer 
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation)
    {
        $objRequest = new Request();
        $objRequest->send(vsprintf($strConfig, array($objGeolocation->getLat(), $objGeolocation->getLon())));

        if ($objRequest->code != 200)
        {
            $this->log("Error by location service: " . vsprintf("Request error code %s - %s ", array($objRequest->code, $objRequest->error)), __CLASS__ . " | " . __FUNCTION__, __FUNCTION__);
            return false;
        }

        $arrJson = json_decode($objRequest->response, true);

        if (!is_array($arrJson))
        {
            $this->log("Response is not a array.", __CLASS__ . " | " . __FUNCTION__, __FUNCTION__);
            return false;
        }
        
        $arrCountries = $this->getCountries();
        
        $strCountryShort = $arrJson['address']['country_code'];
        $strCounty = $arrCountries[$arrJson['address']['country_code']];
             
        $objGeolocation->setCountryShort($strCountryShort);
        $objGeolocation->setCountry($strCounty);     
        
        return $objGeolocation;        
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return $GLOBALS['TL_LANG']['GEO']['openStreetMap'];
    }

    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType()
    {
        return GeoLookUpInterface::GEO;
    }
    

}

?>
