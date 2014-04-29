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
 * Class Geolocation
 *
 * Provide methods for geolocation
 * 
 * @copyright  MEN AT WORK 2012
 * @copyright  backbone87  2012
 * @package    geolocation
 * @deprecated
 */
class GeolocationLatLng 
{
    /**
     * Const
     */
    // Formats
    const FORMAT_D     = 'd';
    const FORMAT_DM    = 'dm';
    const FORMAT_DMS   = 'dms';
    const FORMAT_FLOAT = 'float';
    
    // Values
    const EARTH_RADIUS = 6371000;

    // WGS-84 ellipsoid params 
    const WGS84_A  = 6378137;
    const WGS84_B  = 6356752.314245;
    const WGS84_FR = 298.257223563; // f = fr ^ (-1)   

    /**
     * Vars
     */
    private $fltLat;
    private $fltLng;
    private $fltLatRad;
    private $fltLngRad;

    /* -------------------------------------------------------------------------
     * Basic functions
     */

    /**
     * Create a new helper class
     * 
     * @param float $fltLat
     * @param float $fltLng 
     * @throws Exception
     * @throws OutOfRangeException 
     */
    public function __construct($fltLat, $fltLng)
    {
        self::checkValues($fltLat, $fltLng);

        $this->fltLat = $fltLat;
        $this->fltLng = $fltLng;
        $this->fltLatRad = deg2rad($fltLat);
        $this->fltLngRad = deg2rad($fltLng);
    }

    /**
     * Create a new helper class with latitude and longtidue.
     * See "parseDMS" for more information.
     * 
     * @param mix $varLat
     * @param mix $varLng
     * @return GeolocationLatLng 
     * @throws Exception
     * @throws OutOfRangeException 
     */
    public static function create($varLat, $varLng)
    {        
        $varLat = self::parseString($varLat);
        $varLng = self::parseString($varLng);
        
        return new self($varLat, $varLng);
    }

    /**
     * Create a new helper class with latitude and longtidue.
     * See "parseDMS" for more information.
     * 
     * @param mix $strLatLng
     * @return GeolocationLatLng 
     * @throws Exception
     * @throws OutOfRangeException 
     */
    public static function createFromString($strLatLng)
    {
        $arrLatLng = trimsplit(",", $strLatLng);

        if (count($arrLatLng) != 2)
        {
            throw new ErrorException("Could not find a latitude or longitude, use a comma for separating.");
        }

        if (preg_match("/[N|E|S|W]/i", $strLatLng))
        {
            if (preg_match("/[N|S]/i", $arrLatLng[0]))
            {
                $varLat = self::parseString($arrLatLng[0]);
                $varLng = self::parseString($arrLatLng[1]);
            }
            else
            {
                $varLat = self::parseString($arrLatLng[1]);
                $varLng = self::parseString($arrLatLng[0]);
            }
        }
        else
        {
            $varLat = self::parseString($arrLatLng[0]);
            $varLng = self::parseString($arrLatLng[1]);
        }

        return new self($varLat, $varLng);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString($strFormat = self::FORMAT_FLOAT)
    {
        return $this->getLat($strFormat) . ' ' . $this->getLng($strFormat);
    }

    /* -------------------------------------------------------------------------
     * Helper functions
     */

    /**
     *
     * @param type $fltLat
     * @param type $fltLng
     * @return boolean
     * @throws Exception
     * @throws OutOfRangeException 
     */
    public static function checkValues($fltLat, $fltLng)
    {
        // Check Longitude/Latitude
        if (!is_numeric($fltLat))
        {
            throw new Exception("Longitude is not a valide float value.");
        }

        if (!is_numeric($fltLng))
        {
            throw new Exception("Latitude is not a valide float value.");
        }
        
        $fltLng = floatval($fltLng);
        $fltLat = floatval($fltLat);

        if ($fltLng > 180 || $fltLng < -180)
        {
            throw new OutOfRangeException("Longitude is not between -180° and 180°");
        }
        if ($fltLng > 90 || $fltLng < -90)
        {
            throw new OutOfRangeException("Latitude is not between -90° and 90°");
        }

        return true;
    }

    /**
     * 
     * @param type $fltLat
     * @param type $strFormat
     * @return type 
     */
    public static function formatLat($fltLat, $strFormat = self::FORMAT_DMS)
    {
        return self::formatFloat($fltLat, $strFormat) . ($fltLat < 0 ? 'S' : 'N');
    }

    /**
     * 
     * @param type $fltLng
     * @param type $strFormat
     * @return type 
     */
    public static function formatLng($fltLng, $strFormat = self::FORMAT_DMS)
    {
        return self::formatFloat($fltLng, $strFormat) . ($fltLng < 0 ? 'W' : 'E');
    }

    /**
     * 
     * @param type $fltBearing
     * @param type $strFormat
     * @return type 
     */
    public static function formatBearing($fltBearing, $strFormat = self::FORMAT_DMS)
    {
        $fltBearing = ($fltBearing + 360) % 360;
        $strBearing = self::formatFloat($fltBearing, $strFormat);
        return str_replace('360', '0', $strBearing);
    }

    /**
     *
     * @param type $fltValue
     * @param type $strFormat
     * @return string 
     */
    protected static function formatFloat($fltValue, $strFormat = self::FORMAT_DMS)
    {
        $fltValue = abs($fltValue);

        switch ($strFormat)
        {
            case self::FORMAT_D:
                $strFormat = str_pad(sprintf('%.4f', $fltValue), 8, '0', STR_PAD_LEFT) . "\u00B0";
                break;

            case self::FORMAT_DM:
                $fltValue *= 60;
                $strFormat = str_pad(strval(floor($fltValue / 60)), 3, '0', STR_PAD_LEFT) . "\u00B0";
                $strFormat .= str_pad(sprintf('%.2f', $fltValue % 60), 5, '0', STR_PAD_LEFT) . "\u2032";
                break;

            case self::FORMAT_DMS:
                $fltValue *= 3600;
                $strFormat = str_pad(strval(floor($fltValue / 3600)), 3, '0', STR_PAD_LEFT) . "\u00B0";
                $strFormat .= str_pad(strval(floor($fltValue / 60) % 60), 2, '0', STR_PAD_LEFT) . "\u2032";
                $strFormat .= str_pad(strval(floor($fltValue % 60)), 2, '0', STR_PAD_LEFT) . "\u2033";
                break;

            case self::FORMAT_FLOAT:
                $strFormat = $fltValue;                
                break;
        }
        
        return $strFormat;
    }

    /**
     * Parse a string to a float value.
     * Example for strings
     *  || Float
     *      51.000
     *      -51.856
     *  || DMS, DM, D 
     *      14° 5' 4'' N
     *      14° 5' 4''
     *      14° 5' 4,2''
     *      14° 5'
     *      14°
     *      -14° 5' 4''
     *      -14° 5' 4,2''
     *      -14° 5'
     *      -14°
     *      0140504N
     *      -0140504
     * 
     * @param string $strCoordinate
     * @return float/null 
     */
    public static function parseString($strCoordinate)
    {
        // If we have a float return it
        if (is_float($strCoordinate))
        {
            return floatval($strCoordinate);
        }

        $strCoreCoordinate = $strCoordinate;
        $fltCoordinate     = 0;
        $booNegative       = false;

        // Clean Up
        $strCoordinate = preg_replace("/[N|E|S|W]/i", "", $strCoordinate);

        // DMS | DM | D
        if (preg_match("/[.*°.*'.*''|.*°.*'|.*°]/", trim($strCoordinate)))
        {
            $arrPart = preg_split("/[°|'|'']/", trim($strCoordinate), -1, PREG_SPLIT_NO_EMPTY);

            foreach ($arrPart as $key => $value)
            {
                // Remove whitespace
                $mixValue = trim($value);

                // Check if we have a number/float/double
                if (!is_numeric($mixValue) && !is_float($mixValue) && !is_double($mixValue))
                {
                    unset($arrPart[$key]);
                }

                // Save value
                $arrPart[$key] = floatval($mixValue);
            }

            // Reset keys
            $arrPart = array_values($arrPart);

            // Check fo negative value
            if ($arrPart[0] < 0)
            {
                $booNegative = true;
                $arrPart[0]  = abs($arrPart[0]);
            }

            switch (count($arrPart))
            {
                case 1:
                    $fltCoordinate = $arrPart[0];
                    break;

                case 2:
                    $fltCoordinate = $arrPart[0] + ($arrPart[1] / 60);
                    break;

                case 3:
                    $fltCoordinate = $arrPart[0] + ($arrPart[1] / 60) + ($arrPart[2] / 3600);
                    break;

                default:
                    return null;
            }
        }
        // Check for fixed-width unseparated format eg 0033709W
        else if (preg_match("/-?\d{7}/", trim($strCoordinate)))
        {
            if (preg_match("/^-/", trim($strCoordinate)))
            {
                $booNegative   = true;
                $strCoordinate = preg_replace("/^-/", "", $strCoordinate);
            }

            $arrPart = array();
            $arrPart[0] = intval(substr($strCoordinate, 0, 3));
            $arrPart[1] = intval(substr($strCoordinate, 3, 2));
            $arrPart[2] = intval(substr($strCoordinate, 5, 2));

            if ($arrPart[0] < 0)
            {
                $booNegative = true;
            }

            $fltCoordinate = $arrPart[0] + ($arrPart[1] / 60) + ($arrPart[2] / 3600);
        }
        else
        {
            return null;
        }

        $fltCoordinate = floatval($fltCoordinate);

        if (preg_match("/.*[W|S].*/i", $strCoreCoordinate) || $booNegative == true)
        {
            return $fltCoordinate * -1;
        }
        else
        {
            return $fltCoordinate;
        }
    }

    /* -------------------------------------------------------------------------
     * Getter/Setter functions
     */

    public function __get($strKey)
    {
        switch ($strKey)
        {
            case 'lat':
                return $this->fltLat;
                break;

            case 'lng':
                return $this->fltLng;
                break;

            case 'latRad':
                return $this->fltLatRad;
                break;

            case 'lngRad':
                return $this->fltLngRad;
                break;
        }
    }

    /**
     * Return a format latitude
     * 
     * @param mix $strFormat - See GeolocationLatLng::FORMAT_XXX
     * @return float|string  
     */
    public function getLat($strFormat = self::FORMAT_FLOAT)
    {         
        return $strFormat == self::FORMAT_FLOAT ? $this->fltLat : self::formatLat($this->fltLat, $strFormat);
    }

    /**
     * Return a format longitude
     * 
     * @param mix $strFormat - See GeolocationLatLng::FORMAT_XXX
     * @return float|string 
     */
    public function getLng($strFormat = self::FORMAT_FLOAT)
    {
        return $strFormat == self::FORMAT_FLOAT ? $this->fltLng : self::formatLng($this->fltLng, $strFormat);
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @param type $fltR
     * @return type 
     */
    public function getDistanceTo(GeolocationLatLng  $objOther, $fltR = self::EARTH_RADIUS)
    {
        $fltDeltaLat = $objOther->fltLatRad - $this->fltLatRad;
        $fltDeltaLng = $objOther->fltLngRad - $this->fltLngRad;

        $a = sin($fltDeltaLat / 2) * sin($fltDeltaLat / 2)
                + cos($this->fltLatRad) * cos($objOther->fltLatRad)
                * sin($fltDeltaLng / 2) * sin($fltDeltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $fltR * $c;
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @param type $fltA
     * @param type $fltB
     * @param type $fltF
     * @return int|string 
     */
    public function getRealDistanceTo(GeolocationLatLng  $objOther, $fltA = self::WGS84_A, $fltB = self::WGS84_B, $fltF = self::WGS84_FR)
    {
        // accept reziprocal F, too,
        // for all useful F values: F << 1 and reziprocal F >> 1 
        $fltF > 1 && $fltF = 1 / $fltF;

        $fltDeltaL = deg2rad($objOther->fltLng - $this->fltLng);

        $fltU1    = atan((1 - $fltF) * tan(deg2rad($this->fltLat)));
        $fltSinU1 = sin($fltU1);
        $fltCosU1 = cos($fltU1);

        $fltU2    = atan((1 - $fltF) * tan(deg2rad($objOther->fltLat)));
        $fltSinU2 = sin($fltU2);
        $fltCosU2 = cos($fltU2);

        $fltLambda = $fltDeltaL;
        $n         = 100;

        do
        {
            $fltSinLambda = sin($fltLambda);
            $fltCosLambda = cos($fltLambda);

            $a           = $fltCosU2 * $fltSinLambda;
            $b           = $fltCosU1 * $fltSinU2 - $fltSinU1 * $fltCosU2 * $fltCosLambda;
            $fltSinSigma = sqrt($a * $a + $b * $b);

            if ($fltSinSigma == 0)
                return 0; // co-incident points

            $fltCosSigma = $fltSinU1 * $fltSinU2 + $fltCosU1 * $fltCosU2 * $fltCosLambda;
            $fltSigma    = atan2($fltSinSigma, $fltCosSigma); // POSSIBLE ERROR ARG SWITCH

            $fltSinAlpha   = $fltCosU1 * $fltCosU2 * $fltSinLambda / $fltSigma;
            $fltCosSqAlpha = 1 - $fltSinAlpha * $fltSinAlpha;

            $fltLambdaPrev = $fltLambda;

            if ($fltCosSqAlpha == 0)
            {
                $fltCos2SigmaM = 0;
                $c             = 0;
                $fltLambda     = $fltDeltaL + $fltF * $fltSinAlpha * $fltSigma;
            }
            else
            {
                $fltCos2SigmaM = $fltCosSigma - 2 * $fltSinU1 * $fltSinU2 / $fltCosSqAlpha;
                $c             = $fltF / 16 * $fltCosSqAlpha * (4 + $fltF * (4 - 3 * $fltCosSqAlpha));
                $d             = $fltCos2SigmaM + $c * $fltCosSigma * (-1 + 2 * $fltCos2SigmaM * $fltCos2SigmaM);
                $fltLambda     = $fltDeltaL + (1 - $c) * $fltF * $fltSinAlpha * ($fltSigma + $c * $fltSinSigma * $d);
            }
        }
        while (abs($fltLambda - $fltLambdaPrev) > 1e-12 && --$n);

        if ($n == 0)
        {
            return null; // formula failed to converge
        }

        $fltUSq        = $fltCosSqAlpha * ($fltA * $fltA - $fltB * $fltB) / ($fltB * $fltB);
        $a             = 1 + $fltUSq / 16384 * (4096 + $fltUSq * (-768 + $fltUSq * (320 - 175 * $fltUSq)));
        $b             = $fltUSq / 1024 * (256 + $fltUSq * (-128 + $fltUSq * (74 - 47 * $fltUSq)));
        $c             = $fltCosSigma * (-1 + 2 * $fltCos2SigmaM * $fltCos2SigmaM);
        $d             = $b / 6 * $fltCos2SigmaM * (-3 + 4 * $fltSinSigma * $fltSinSigma) * (-3 + 4 * $fltCos2SigmaM * $fltCos2SigmaM);
        $fltDeltaSigma = $b * $fltSinSigma * ($fltCos2SigmaM + $b / 4 * ($c - $d));

        $fltS = $fltB * $a * ($fltSigma - $fltDeltaSigma);

        return $fltS;
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @param type $fltR
     * @return type 
     */
    public function getRhumbDistanceTo(GeolocationLatLng  $objOther, $fltR = self::EARTH_RADIUS)
    {
        $fltPI       = pi();
        $fltDeltaLat = $objOther->fltLatRad - $this->fltLatRad;
        $fltDeltaLng = abs($objOther->fltLngRad - $this->fltLngRad);
        if ($fltDeltaLng > Math . PI)
            $fltDeltaLng = 2 * $fltPI - $fltDeltaLng;

        $fltDeltaPhi = log(tan($objOther->fltLatRad / 2 + $fltPI / 4) / tan($this->fltLatRad / 2 + $fltPI / 4));

        // E-W line gives $fltDeltaPhi = 0
        $q = $fltDeltaPhi != 0 ? $fltDeltaLat / $fltDeltaPhi : cos($this->fltLatRad);

        return sqrt($fltDeltaLat * $fltDeltaLat + $q * $q * $fltDeltaLng * $fltDeltaLng) * $fltR;
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @return type 
     */
    public function getBearingTo(GeolocationLatLng  $objOther)
    {
        $fltDeltaLng = $objOther->fltLngRad - $this->fltLngRad;

        $y = sin($fltDeltaLng) * cos($objOther->fltLatRad);
        $x = cos($this->fltLatRad) * sin($objOther->fltLatRad)
                - sin($this->fltLatRad) * cos($objOther->fltLatRad) * cos($fltDeltaLng);

        return rad2deg(atan2($y, $x) + 360) % 360;
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @return type 
     */
    public function getFinalBearingTo(GeolocationLatLng  $objOther)
    {
        return ($objOther->getBearingTo($this) + 180) % 360;
    }

    /**
     *
     * @param GeolocationLatLng  $objOther
     * @return type 
     */
    public function getRhumbBearingTo(GeolocationLatLng  $objOther)
    {
        $fltPI       = pi();
        $fltDeltaLng = $objOther->fltLngRad - $this->fltLngRad;

        $fltDeltaPhi = log(tan($objOther->fltLatRad / 2 + $fltPI / 4) / tan($this->fltLatRad / 2 + $fltPI / 4));

        if (abs($fltDeltaLng) > $fltPI)
        {
            $fltMod      = $fltDeltaLng > 0 ? -1 : 1;
            $fltDeltaLng = $fltMod * (2 * $fltPI - $fltDeltaLng);
        }

        $fltBearing = atan2($fltDeltaLng, $fltDeltaPhi);

        return (rad2deg($fltBearing) + 360) % 360;
    }

    /**
     *
     * @param type $fltBearing
     * @param type $fltDistance
     * @param type $fltR
     * @return \LatLng 
     */
    public function getDestination($fltBearing, $fltDistance, $fltR = self::EARTH_RADIUS)
    {
        $fltPI      = pi();
        $fltDistance /= $fltR;
        $fltBearing = deg2rad($fltBearing);

        $fltDestLat = asin(sin(lat1) * cos(dist) + cos(lat1) * sin(dist) * cos(brng));
        $fltDestLng = $this->fltLngRad + atan2(
                        sin($fltBearing) * sin($fltDistance) * cos($this->fltLatRad), cos($fltDistance) - sin($this->fltLatRad) * sin($fltDestLat)
        );
        $fltDestLng = ($fltDestLng + 3 * $fltPI) % (2 * $fltPI) - $fltPI;  // normalise to -180..+180º

        return new GeolocationLatLng(rad2deg($fltDestLat), rad2deg($fltDestLng));
    }

    /**
     *
     * @param type $fltBearing
     * @param type $fltDistance
     * @param type $fltR
     * @return \LatLng 
     */
    public function getRhumbDestination($fltBearing, $fltDistance, $fltR = self::EARTH_RADIUS)
    {
        $fltPI      = pi();
        $fltDistance /= $fltR;
        $fltBearing = deg2rad($fltBearing);

        $fltDestLat  = $this->fltLatRad + $fltDistance * cos($fltBearing);
        $fltDeltaLat = $fltDestLat - $this->fltLatRad;
        $fltDeltaPhi = log(tan($fltDestLat / 2 + $fltPI / 4) / tan($this->fltLatRad / 2 + $fltPI / 4));
        $q           = $fltDeltaPhi != 0 ? $fltDeltaLat / $fltDeltaPhi : cos($this->fltLatRad);
        $fltDeltaLng = $fltDistance * sind($fltBearing) / $q;

        if (abs($fltDestLat) > $fltPI / 2)
        {
            $fltMod     = $fltDestLat > 0 ? 1 : -1;
            $fltDestLat = $fltMod * ($fltPI - $fltDestLat);
        }
        $fltDestLng = ($this->fltLngRad + $fltDeltaLng + 3 * $fltPI) % (2 * $fltPI) - $fltPI;

        return new GeolocationLatLng(rad2deg($fltDestLat), rad2deg($fltDestLng));
    }

}

?>