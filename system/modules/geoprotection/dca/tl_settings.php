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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'gp_customOverrideGp';

// extend subpalettes
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['gp_customOverrideGp'] = 'gp_overrideIps,gp_customCountryFallback';

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{gp_protection_legend},gp_mode,gp_countries,gp_countryFallback,gp_customOverrideGp';

/**
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_overrideIps'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_overrideIps_headline'],
    'inputType' => 'multiColumnWizard',
    'exclude' => true,
    'eval' => array
        (
        'style' => 'width:100%;',
        'columnFields' => array(
            'ipAddress' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['gp_overrideIps'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:600px', 'nospace' => true),
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_customOverrideGp'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_customOverrideGp'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_customCountryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_customCountryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => array_merge(array("" => &$GLOBALS['TL_LANG']['tl_settings']['gp_allCountries']), $this->getCountries()),
    'eval' => array('multiple' => false)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_countryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_countryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => $this->getCountries(),
    'default' => 'de',
    'eval' => array('multiple' => false, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_mode'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_mode'],
    'default' => 'show',
    'exclude' => true,
    'inputType' => 'select',
    'options' => array(
        'gp_show' => &$GLOBALS['TL_LANG']['MSC']['hiddenShow'],
        'gp_hide' => &$GLOBALS['TL_LANG']['MSC']['hiddenHide']
    ),
    'reference' => &$GLOBALS['TL_LANG']['tl_content'],
    'eval' => array('mandatory' => true, 'includeBlankOption' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gp_countries'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['gp_countries'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => array('GeoProtection', 'getCountriesByContinent'),
    'eval' => array('multiple' => true, 'size' => 8, 'mandatory' => true)
);
?>