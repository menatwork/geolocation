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

/**
 * Palettes
 */
$arrPalettes = array('default', 'regular', 'root');

foreach ($arrPalettes as $PaletteName)
{
    if (key_exists($PaletteName, $GLOBALS['TL_DCA']['tl_page']['palettes']))
    {
        $mixPalette = trimsplit(";", $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName]);
        array_insert($mixPalette, 2, array("{geo_legend},geo_single_page,geo_child_page"));
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName] = implode(";", $mixPalette);
    }
}

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'geo_single_page';
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'geo_child_page';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['geo_single_page'] = 'geo_single_choose';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['geo_child_page']  = 'geo_child_choose';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['geo_single_page'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_single_page'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_child_page'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_child_page'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

//----------

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_single_choose'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_single_choose'],
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => array("w3c" => "geo_w3c", "ip" => "geo_ip", "fallback" => "geo_fallback"),
    'reference' => &$GLOBALS['TL_LANG']['tl_page'],
    'eval' => array('multiple' => true, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_child_choose'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_child_choose'],
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => array("w3c" => "geo_w3c", "ip" => "geo_ip", "fallback" => "geo_fallback"),
    'reference' => &$GLOBALS['TL_LANG']['tl_page'],
    'eval' => array('multiple' => true, 'mandatory' => true)
);
?>