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

// extend selector
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'customOverrideGp';

// extend subpalettes
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['customOverrideGp'] = 'overrideIps,customCountryFallback';

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{gp_protection_legend},countryFallback,customOverrideGp'; 

/**
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['overrideIps'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['overrideIps_headline'],
    'inputType' => 'multiColumnWizard',
    'exclude' => true,
    'eval' => array
        (
        'style' => 'width:100%;',
        'columnFields' => array
            (
            'ipAddress' => array
                (
                'label' => $GLOBALS['TL_LANG']['tl_settings']['overrideIps'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:600px', 'nospace' => true),
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['customOverrideGp'] = array(
    'label' 		=> &$GLOBALS['TL_LANG']['tl_settings']['customOverrideGp'],
    'inputType' 	=> 'checkbox',
    'eval' 			=> array('tl_class' => 'clr', 'submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['customCountryFallback'] = array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['customCountryFallback'],
	'exclude'   	=> true,
	'inputType' 	=> 'select',
	'options'   	=> array_merge(array("" => &$GLOBALS['TL_LANG']['tl_settings']['allCountries']), $this->getCountries()),
	'eval'      	=> array('multiple' => false)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['countryFallback'] = array(
	'label'			=> &$GLOBALS['TL_LANG']['tl_settings']['countryFallback'],
	'exclude'   	=> true,
	'inputType' 	=> 'select',
	'options'   	=> $this->getCountries(),
	'default'   	=> 'de',
	'eval'      	=> array('multiple' => false, 'mandatory' => true)
);
?>