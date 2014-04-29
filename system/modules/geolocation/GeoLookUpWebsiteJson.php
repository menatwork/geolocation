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
 * Class GeoProLookUpInterface
 *
 * Provide methods for decoding messages from look up services
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class GeoLookUpWebsiteJson extends Backend implements GeoLookUpInterface
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
        $objRequest->send(vsprintf($strConfig, $objGeolocation->getLat(), $objGeolocation->getLon()));

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

        $mixResult = $this->searchCountry($arrJson);

        if ($mixResult == false)
        {
            return false;
        }

        $arrCountries = $this->getCountries();

        $strCountryShort = $mixResult;
        $strCounty       = $arrCountries[$mixResult];

        $objGeolocation->setCountryShort($strCountryShort);
        $objGeolocation->setCountry($strCounty);

        return $objGeolocation;
    }

    protected function searchCountry($arrArray)
    {
        foreach ($arrArray as $key => $value)
        {
            if (is_array($value) == true)
            {
                $strCountry = searchCountry($value);

                if ($strCountry !== false)
                {
                    return $strCountry;
                }
            }
            else
            {
                if (preg_match("/.*(country).*/", $key))
                {
                    if (strlen($value) == 2)
                    {
                        return $value;
                    }
                    else
                    {
                        continue;
                    }
                }
                else
                {
                    continue;
                }
            }
        }

        return false;
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return $GLOBALS['TL_LANG']['GEO']['websiteJSON'];
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
