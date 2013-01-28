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
 * Factory for create the codifyengine
 */
class GeoLookUpFactory extends Backend
{
    
    /**
     * Create the codifyengine.
     * 
     * @return GeoLookUpResolveInterface 
     */
    public static function getEngine($strEngine)
    {
        // Check if engine exists in filesystem
        if (!file_exists(TL_ROOT . "/system/modules/geolocation/$strEngine"))
        {
            throw new Exception("Unknown 'LookUp' class: $strEngine");
        }
        
        $strEngine = preg_replace("/\.php/", "", $strEngine);
                
        // Get a new class
        $objEnginge = new $strEngine();
        
        // Get engine
        if ($objEnginge instanceof GeoLookUpInterface)
        {
            return $objEnginge;
        }
        else
        {
            throw new Exception("$strEngine is not from type GeoLookUpInterface");
        }
    }
}

?>