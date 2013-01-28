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
 * Class GeoLookUpInternIP
 *
 * Provide methods for decoding messages from look up services
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
class GeoLookUpInternIP extends Backend implements GeoLookUpInterface
{

    /**
     * IP cache array
     * @var array
     */
    private static $arrIPCache = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @param type $strConfig
     * @param GeolocationContainer $objGeolocation
     * @return boolean|GeolocationContainer 
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation)
    {
        //calculate ipNum
        $arrIP = explode(".", $objGeolocation->getIP());
        $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

        
        // Load country from cache or do a db-lookup
        if (!isset(self::$arrIPCache[$ipNum]))
        {
            // Initialize cache
            self::$arrIPCache[$ipNum] = '';

            $arrResult = $this->Database->prepare("SELECT * FROM tl_geodata WHERE ? >= ipnum_start AND ? <= ipnum_end")
                    ->limit(1)
                    ->execute($ipNum, $ipNum)
                    ->fetchAllAssoc();
            
            if (count($arrResult) != 0)
            {                
                $country_short = strtolower($arrResult[0]['country_short']);
                
                $arrCountries = $this->getCountries();
                
                self::$arrIPCache[$ipNum] = $country_short;

                $objGeolocation->setCountryShort($country_short);
                $objGeolocation->setCountry($arrCountries[$country_short]);
            }
            else
            {
                return false;
            }
        }
        else
        {
            $arrCountries = $this->getCountries();

            $objGeolocation->setCountryShort(self::$arrIPCache[$ipNum]);
            $objGeolocation->setCountry($arrCountries[self::$arrIPCache[$ipNum]]);
        }
        
        return $objGeolocation;
    }

    /**
     * 
     * @return string 
     */
    public function getName()
    {
        return $GLOBALS['TL_LANG']['GEO']['internalIPDatabase'];
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
