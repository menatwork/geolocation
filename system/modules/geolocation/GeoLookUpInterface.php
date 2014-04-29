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
 * Provide methods for decoding messages from look up services.
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
interface GeoLookUpInterface
{
    const IP = 1;
    const GEO = 2;
    const BOTH = 3;

    /**
     * @return GeolocationContainer
     */
    public function getLocation($strConfig, GeolocationContainer $objGeolocation);
    
    /**
     * @return String name of look up service
     */
    public function getName();    
    
    /**
     * @return int 1 IP | 2 Lon/Lat | 3 Both
     */
    public function getType();
}

?>
