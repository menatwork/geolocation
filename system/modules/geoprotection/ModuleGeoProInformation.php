<?php

if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 * @license    GNU/LGPL
 * @filesource
 */
class ModuleGeoProInformation extends Module
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_geoprotection_information';

    protected function compile()
    {
        // Check if we have in the session the geolocation information;
        $objSession       = Session::getInstance();
        $arrGeoProtection = $objSession->get("geoprotection");

        if (is_array($arrGeoProtection) && $arrGeoProtection["geolocated"] == true)
        {
            $this->Template->geolocated = true;
        }
        else
        {
            $this->Template->geolocated = false;
        }

        /**
         * Get from the Session the current location information
         * 
         * @return array() Keys: country | country_short | geolocated | faild | error | error_ID
         */
        $arrInformations = GeoProtection::getUseGeoLocation();

        $this->Template->isGeolocatetd = $arrInformations["geolocated"];
        $this->Template->isFaild = $arrInformations["faild"];
        $this->Template->strError = $arrInformations["error"];
        $this->Template->intError = $arrInformations["error_ID"];
        $this->Template->strCountry = $arrInformations["country"];
        $this->Template->strShortCountry = $arrInformations["country_short"];
    }

}

?>