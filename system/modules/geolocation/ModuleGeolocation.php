<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2012
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
                $GLOBALS['TL_JAVASCRIPT'][] = "plugins/chosen/chosen.js";
                $GLOBALS['TL_CSS'][]        = "plugins/chosen/chosen.css";
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

        $strJS = "<script>";
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