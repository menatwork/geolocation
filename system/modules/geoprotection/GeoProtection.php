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
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Class GeoProtection
 *
 * Provide methods for GeoProtection.
 * @copyright  MEN AT WORK 2011-2012
 * @package    Controller
 */
class GeoProtection extends Frontend
{

    /**
     * IP cache array
     * @var array
     */
    private static $arrIPCache = array();
    protected $arrCountryInformation;

    public function __construct()
    {
        parent::__construct();

       
    }

    /**
     * Get from the Session the current location information
     * 
     * @return array() Keys: country | country_short | geolocated | faild | error | error_ID
     */
    public static function getUseGeoLocation()
    {
        $Session          = Session::getInstance();
        $arrGeoProtection = $Session->get("geoprotection");

        if (is_array($arrGeoProtection))
        {
            return $arrGeoProtection;
        }
        else
        {
            return $this->arrCountryInformation;
        }
    }

    /**
     * AJAX Call get for a lat/lon the country information
     * @return type 
     */
    public function dispatchAjax()
    {
        switch ($this->Input->post("action"))
        {
            /**
             * Set geolocation for user.
             * Try to get the lat/lon from cache db or use lookup service.
             * Write all information into session. 
             */
            case "GeoProSetLocation":
                // Link for lat/lon lookup
                $strLink = "http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat=%s&lon=%s";

                // Setup return array
                $arrReturn = array(
                    "success" => true,
                    "value" => "",
                    "error" => ""
                );

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

                    // Only get the last three numbers after the point 
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

                        $this->arrCountryInformation["country"] = $arrAddress["country"];
                        $this->arrCountryInformation["country_short"] = $arrAddress["country_code"];
                        $this->arrCountryInformation["geolocated"] = true;

                        $arrReturn["value"] = "Inset new location.";
                    }
                    // Found a entry in database
                    else
                    {
                        $this->arrCountryInformation["country"] = $objResult->country;
                        $this->arrCountryInformation["country_short"] = $objResult->country_short;
                        $this->arrCountryInformation["geolocated"] = true;

                        $arrReturn["value"] = "Found location in database.";
                    }

                    // Set Session
                    $this->Session->set("geoprotection", $this->arrCountryInformation);
                }
                catch (Exception $exc)
                {
                    $this->arrCountryInformation[]


                    // Update Session with information
                    $this->Session->set("geoprotection", array(
                        "country" => "",
                        "country_short" => "",
                        "geolocated" => false,
                        "faild" => true,
                        "error" => $exc->getMessage()
                    ));

                    // Error handling
                    $arrReturn["success"] = false;
                    $arrReturn["error"]   = $exc->getMessage();
                    return $arrReturn;
                }

                // Return debug information
                return $arrReturn;

            /**
             * Set error msg from js script.
             */
            case "GeoProSetError":
                switch ($this->Input->post("errID"))
                {
                    case 1:
                        $strError = "Premission denined";
                        break;

                    case 2:
                        $strError = "Position unavailable";
                        break;

                    case 3:
                        $strError = "Timeout";
                        break;

                    case 10:
                        $strError = "Not supported Browser.";
                        break;

                    default:
                        $strError = "Unknown error";
                        break;
                }

                $arrIPLookUp = $this->doIPLookUp();

                // Update Session with information
                $this->Session->set("geoprotection", array(
                    "country" => $arrIPLookUp["country"],
                    "country_short" => $arrIPLookUp["country_short"],
                    "ip" => $arrIPLookUp["ip"],
                    "geolocated" => false,
                    "ip_looup" => true,
                    "faild" => true,
                    "error" => $strError,
                    "error_ID" => $this->Input->post("errID")
                ));

                return array(
                    "success" => true,
                    "value" => "",
                    "error" => ""
                );

            default:
                return false;
        }
    }

    /**
     * Make a ip lookup
     * 
     * @return array() Keys: IP | country | country_short
     */
    public function doIPLookUp()
    {
        //calculate ipNum
        $arrIP = explode(".", $this->Environment->ip);
        $ipNum = 16777216 * $arrIP[0] + 65536 * $arrIP[1] + 256 * $arrIP[2] + $arrIP[3];

        // Load country from cache or do a db-lookup
        if (!isset(self::$arrIPCache[$ipNum]))
        {
            // Initialize cache
            self::$arrIPCache[$ipNum] = '';

            $country = $this->Database->prepare("SELECT country_short FROM tl_geodata WHERE ? >= ipnum_start AND ? <= ipnum_end")
                    ->limit(1)
                    ->execute($ipNum, $ipNum);

            self::$arrIPCache[$ipNum] = ($country->country_short) ? strtolower($country->country_short) : $GLOBALS['TL_CONFIG']['countryFallback'];
        }

        return array(
            "IP" => $ipNum,
            "country" => $country->country,
            "country_short" => $country->country_short
        );
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
            $arrAux[$strConKey]  = $strConKeyTranslated;
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
            $strOrgKey           = array_search($strConKey, $arrAux);
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