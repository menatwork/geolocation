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
 * @copyright  MEN AT WORK 2011
 * @package    GeoProtection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Class GeoProtection
 *
 * Provide methods for GeoProtection.
 * @copyright  MEN AT WORK 2011
 * @package    Controller
 */
class GeoProtection extends Frontend
{
    /**
     * AJAX Call get for a lat/lon the country information
     * @return type 
     */
    public function dispatchAjax()
    {
        if ($this->Input->post("action") == "GeoProSetLocation")
        {
            // Link for lat/lon lookup
            $strLink = "http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s";

            // Setup return array
            $arrReturn = array("success" => true, "value" => "", "error" => "");
            $arrCountry = array("country" => "", "country_short" => "", "geolocated" => false);

            // Run
            try
            {
                // Check if lat/lon is set
                if (strlen($this->Input->post("lat")) == 0 || strlen($this->Input->post("lon")) == 0)
                {
                    throw new Exception("Missing longitude or latitude.");
                }

                // Split 
                $arrLat = trimsplit(".", $this->Input->post("lat"));
                $arrLon = trimsplit(".", $this->Input->post("lon"));

                // Check if we have two values
                if (count($arrLat) != 2 || count($arrLon) != 2)
                {
                    throw new Exception("The longitude or latitude dosen't seem to be a valid loaction.");
                }

                // Only get the las three numbers after the point 
                $arrLat[1] = substr($arrLat[1], 0, 3);
                $arrLon[1] = substr($arrLon[1], 0, 3);

                // Check if we have the location already in database
                $objResult = $this->Database
                        ->prepare("SELECT * FROM tl_geodatacache WHERE lat=? AND lon=?")
                        ->execute(implode(".", $arrLat), implode(".", $arrLon));

                // No values found, so try to get the country
                if ($objResult->numRows == 0)
                {
                    $objRequest = new Request();
                    $objRequest->send(vsprintf($strLink, array($this->Input->post("lat"), $this->Input->post("lon"))));
                    if ($objRequest->code != 200)
                    {
                        throw new Exception(vsprintf("Request error code %s - %s ", array($objRequest->code, $objRequest->error)));
                    }

                    $arrAddress = json_decode($objRequest->response, true);
                    $arrAddress = $arrAddress["address"];

                    $this->Database->prepare("INSERT INTO tl_geodatacache %s")
                            ->set(array("lat" => implode(".", $arrLat), "lon" => implode(".", $arrLon), "create_on" => time(), "country" => $arrAddress["country"], "country_short" => $arrAddress["country_code"]))
                            ->execute();

                    $arrCountry = array("country" => $arrAddress["country"], "country_short" => $arrAddress["country_code"], "geolocated" => true);
                    $arrReturn["value"] = "Inset new location.";
                }
                // Found a entry in database
                else
                {
                    $arrCountry = array("country" => $objResult->country, "country_short" => $objResult->country_short, "geolocated" => true);
                    $arrReturn["value"] = "Found location in database.";
                }

                // Set Session
                $this->Session->set("geoprotection", $arrCountry);
            }
            catch (Exception $exc)
            {
                // Error handling
                $arrReturn["success"] = false;
                $arrReturn["error"] = $exc->getMessage();
                return $arrReturn;
            }

            // Return debug information
            return $arrReturn;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check premission and display element or not
     * 
     * @param type $objElement
     * @param type $strBuffer
     * @return type 
     */
    public function checkPermission($objElement, $strBuffer)
    {
        //check if geoprotection is enabled
        if ($objElement->gp_protected && TL_MODE != 'BE')
        {
            // Vars
            $strCountryShort = '';
            $arrIpAddress = array();

            // Session   
            $strSessionCountry = $this->Session->get("geoprotection");            
            if (!is_array($strSessionCountry) && ($strSessionCountry["country_short"] == null || $strSessionCountry["country_short"] == ""))
            {
                $strSessionCountry = false;
            }
            else
            {
                $strSessionCountry = $strSessionCountry["country_short"];
            }

            // User location ---------------------------------------------------
            // Get override IP's
            if ($GLOBALS['TL_CONFIG']['gp_customOverrideGp'] == true)
            {
                foreach (deserialize($GLOBALS['TL_CONFIG']['gp_overrideIps']) as $ip)
                {
                    $arrIpAddress[] = $ip['ipAddress'];
                }

                // Check if the current ip in array
                if (in_array($this->Environment->ip, $arrIpAddress))
                {
                    // If no fallback land is choosen, see all
                    if ($GLOBALS['TL_CONFIG']['gp_customCountryFallback'] == '')
                    {
                        return $strBuffer;
                    }
                    else
                    {
                        $strCountryShort = $GLOBALS['TL_CONFIG']['gp_customCountryFallback'];
                    }
                }
            }
            // Use geolocation 
            else if ($strSessionCountry != false)
            {
                $strCountryShort = $strSessionCountry;
            }
            // Use falback
            else
            {
                $strCountryShort = $GLOBALS['TL_CONFIG']['gp_countryFallback'];
            }

            // Settings --------------------------------------------------------
            // Use content settings
            if ($objElement->gp_protected_overwrite != "")
            {
                $strMode = $objElement->gp_mode;
                $arrCountries = deserialize($objElement->gp_countries);
            }
            // Use global settings
            else
            {
                $strMode = $GLOBALS['TL_CONFIG']['gp_mode'];
                $arrCountries = deserialize($GLOBALS['TL_CONFIG']['gp_countries']);
            }

            // Return or not, this is the question
            if (in_array($strCountryShort, $arrCountries))
            {
                return ($strMode == "gp_hide") ? '' : $strBuffer;
            }
            else
            {
                return ($strMode == "gp_show") ? '' : $strBuffer;
            }
        }

        return $strBuffer;
    }

    /**
     * get Country-List
     */
    public function getCountriesByContinent()
    {
        $return = array();
        $countries = array();
        $arrAux = array();
        $arrTmp = array();

        $this->loadLanguageFile('countries');
        $this->loadLanguageFile('continents');
        include(TL_ROOT . '/system/config/countries.php');
        include(TL_ROOT . '/system/modules/geoprotection/countriesByContinent.php');
        foreach ($countriesByContinent as $strConKey => $arrCountries)
        {

            $strConKeyTranslated = strlen($GLOBALS['TL_LANG']['CONTINENT'][$strConKey]) ? utf8_romanize($GLOBALS['TL_LANG']['CONTINENT'][$strConKey]) : $strConKey;
            $arrAux[$strConKey] = $strConKeyTranslated;
            foreach ($arrCountries as $strCount)
            {


                $arrTmp[$strConKeyTranslated][$strCount] = strlen($GLOBALS['TL_LANG']['CNT'][$strCount]) ? utf8_romanize($GLOBALS['TL_LANG']['CNT'][$strCount]) : $countries[$strName];
            }
        }

        ksort($arrTmp);

        foreach ($arrTmp as $strConKey => $arrCountries)
        {
            asort($arrCountries);
            //get original continent key
            $strOrgKey = array_search($strConKey, $arrAux);
            $strConKeyTranslated = strlen($GLOBALS['TL_LANG']['CONTINENT'][$strOrgKey]) ? ($GLOBALS['TL_LANG']['CONTINENT'][$strOrgKey]) : $strConKey;
            foreach ($arrCountries as $strKey => $strCountry)
            {

                $return[$strConKeyTranslated][$strKey] = strlen($GLOBALS['TL_LANG']['CNT'][$strKey]) ? $GLOBALS['TL_LANG']['CNT'][$strKey] : $countries[$strKey];
            }
        }

        return $return;
    }

}

?>