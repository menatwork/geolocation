<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

class ModuleGeolocation extends Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = '';

    public function generate()
    {
        if (TL_MODE == 'FE')
        {
            if ($this->geo_chosen == true)
            {
                if(version_compare(VERSION, '3.0', '<'))
                {
                    $GLOBALS['TL_JAVASCRIPT'][] = 'plugins/chosen/chosen.js';
                    $GLOBALS['TL_CSS'][]        = 'plugins/chosen/chosen.css';
                }
                else
                {
                    $GLOBALS['TL_JAVASCRIPT'][] = 'assets/mootools/chosen/chosen.js';
                    $GLOBALS['TL_CSS'][]        = 'assets/mootools/chosen/chosen.css';
                }
            }

            $GLOBALS['TL_JAVASCRIPT'][] = "system/modules/geolocation/html/js/geolocation.js";
        }

        // Change template
        $this->strTemplate = $this->geo_template;

        // Call parent
        return parent::generate();
    }

    protected function compile()
    {
        if (REQUEST_TOKEN == "REQUEST_TOKEN")
        {
            $strRequestToken = "";
        }
        else
        {
            $strRequestToken = REQUEST_TOKEN;
        }

        $strJS = "<script type=\"text/javascript\">";
        $strJS .="window.addEvent('domready', function(){";
        $strJS .="if (typeof(RunGeolocation) != 'undefined') RunGeolocation.addInfoElement('geoInfo_" . $this->id . "');";
        $strJS .="GeoUpdater.setMessages({";
        $strJS .="changing:'{$GLOBALS['TL_LANG']['MSC']['geo_msc_Changing']}',";
        $strJS .="noConnection:'{$GLOBALS['TL_LANG']['ERR']['geo_err_NoConnection']}',";
        $strJS .="});";
        $strJS .="GeoUpdater.setRequestToken('" . $strRequestToken . "');";
        $strJS .="GeoUpdater.setSession('" . session_id() . "');";
        $strJS .="});";
        $strJS .="</script>";

        // Add location object        
        $this->Template->UserGeolocation = Geolocation::getInstance()->getUserGeolocation();

        // Add JS
        $this->Template->strJS = $strJS;
        $this->Template->strId = $this->id;
        $this->Template->lang = $GLOBALS['TL_LANGUAGE'];
        $this->Template->geoChosen = $this->geo_chosen;
    }

}

?>