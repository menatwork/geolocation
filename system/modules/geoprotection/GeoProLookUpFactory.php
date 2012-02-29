<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @package    geoprotection
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Factory for create the codifyengine
 */
class GeoProLookUpFactory extends Backend
{

    /**
     * Create the codifyengine.
     * 
     * @return CtoComCodifyengineAbstract 
     */
    public static function getEngine($strEngine)
    {
        // Check if engine exists in filesystem
        if (!file_exists(TL_ROOT . "/system/modules/geoprotection/$strEngine.php"))
        {
            throw new Exception("Unknown 'LookUp' class: $strEngine");
        }
        
        // Get a new class
        $objEnginge = new $strEngine();

        // Get engine
        if ($objEnginge instanceof GeoProLookUpInterface)
        {
            return $objEnginge;
        }
        else
        {
            throw new Exception("$strEngine is not fro typ");
        }
    }
}

?>