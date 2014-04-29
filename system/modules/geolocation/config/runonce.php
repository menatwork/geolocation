<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

// Be silenced
@error_reporting(0);
@ini_set("display_errors", 0);

/**
 * Runonce Job
 */
class runonceJob extends Backend
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Run job
     */
    public function run()
    {
        if (!isset($GLOBALS['TL_CONFIG']['geo_IPlookUpSettings']))
        {
            $this->Config->add("\$GLOBALS['TL_CONFIG']['geo_IPlookUpSettings']", 'a:1:{i:0;a:2:{s:12:"lookUpConfig";s:37:"http://www.geoplugin.net/php.gp?ip=%s";s:11:"lookUpClass";s:22:"GeoLookUpGeoplugin.php";}}');
        }

        if (!isset($GLOBALS['TL_CONFIG']['geo_GeolookUpSettings']))
        {
            $this->Config->add("\$GLOBALS['TL_CONFIG']['geo_GeolookUpSettings']", 'a:1:{i:0;a:2:{s:12:"lookUpConfig";s:74:"http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s";s:11:"lookUpClass";s:26:"GeoLookUpOpenStreetMap.php";}}');
        }
    }
}

// Run once
$objRunonceJob = new runonceJob();
$objRunonceJob->run();

?>