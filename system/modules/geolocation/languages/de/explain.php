<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @copyright  MEN AT WORK 2011
 * @package    Language
 * @license    GNU/LGPL 
 * @filesource
 */

$GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'] = array();
$GLOBALS['TL_LANG']['XPL']['lookUpClassIP'] = array();

$arrFile = scan(TL_ROOT . "/system/modules/geolocation");

foreach ($arrFile as $value)
{
    if (preg_match("/GeoLookUp.*\.php/", $value) && !preg_match("/.*(Factory|Interface).*/", $value))
    {
        $objService = GeoLookUpFactory::getEngine($value);

        if ($objService->getType() == GeoLookUpInterface::GEO || $objService->getType() == GeoLookUpInterface::BOTH)
        {
            $GLOBALS['TL_LANG']['XPL']['lookUpClassGeo'][] = array($objService->getName(), $objService->getDescription("de"));
        }
        
        if ($objService->getType() == GeoLookUpInterface::IP || $objService->getType() == GeoLookUpInterface::BOTH)
        {
            $GLOBALS['TL_LANG']['XPL']['lookUpClassIP'][] = array($objService->getName(), $objService->getDescription("de"));
        }
    }
}

?>
