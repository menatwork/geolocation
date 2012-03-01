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
 * @package    GeoLocation
 * @license    GNU/LGPL
 * @filesource
 */
/**
 * Selector
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'geo_customOverridegeo';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'geo_activateCookies';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'geo_activateCountryFallback';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['geo_customOverridegeo']       = 'geo_overrideIps,geo_customCountryFallback';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['geo_activateCookies']         = 'geo_cookieDuration';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['geo_activateCountryFallback'] = 'geo_countryFallback';

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{geo_protection_legend},geo_lookUpSettingsIP,geo_lookUpSettingsGeo,geo_activateCookies,geo_activateCountryFallback,geo_customOverridegeo';

/**
 * Add field
 */
// Cookies 
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_activateCookies'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_activateCookies'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_cookieDuration'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array('rgxp' => 'digit', 'multiple' => true, 'size' => 2, 'mandatory' => true)
);

// CountryFallback
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_activateCountryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_activateCountryFallback'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_countryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => $this->getCountries(),
    'default' => 'de',
    'eval' => array('multiple' => false, 'mandatory' => true)
);

// IP Override
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_customOverridegeo'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_customOverridegeo'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_overrideIps'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps_headline'],
    'inputType' => 'multiColumnWizard',
    'exclude' => true,
    'eval' => array
        (
        'style' => 'width:100%;',
        'columnFields' => array(
            'ipAddress' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['geo_overrideIps'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:600px', 'nospace' => true),
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_customCountryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_customCountryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => $this->getCountries(),
    'eval' => array('multiple' => false, 'mandatory' => true, 'includeBlankOption' => false)
);

// Lookup Settings
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_lookUpSettingsIP'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_lookUpSettingsIP'],
    'inputType' => 'multiColumnWizard',
    'exclude' => true,
    'eval' => array
        (
        'style' => 'width:100%;',
        'columnFields' => array(
            'lookUpConfig' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpConfig'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:300px'),
            ),
            'lookUpClass' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpClass'],
                'inputType' => 'select',
                'eval' => array('style' => 'width:300px'),
                'options_callback' => array('geo_tl_settings', 'getLookUpServicesIP')
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_lookUpSettingsGeo'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_lookUpSettingsGeo'],
    'inputType' => 'multiColumnWizard',
    'exclude' => true,
    'eval' => array
        (
        'style' => 'width:100%;',
        'columnFields' => array(
            'lookUpConfig' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpConfig'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:300px'),
            ),
            'lookUpClass' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpClass'],
                'inputType' => 'select',
                'eval' => array('style' => 'width:300px'),
                'options_callback' => array('geo_tl_settings', 'getLookUpServicesGeo')
            )
        )
    )
);


class geo_tl_settings extends Backend
{

    public function getLookUpServicesIP()
    {
        $arrReturn = array();
        $arrFile = scan(TL_ROOT . "/system/modules/geolocation");

        foreach ($arrFile as $value)
        {
            if (preg_match("/GeoLookUp.*\.php/", $value) && !preg_match("/.*(Factory|Interface).*/", $value))
            {
                $objService = GeoLookUpFactory::getEngine($value);
                
                if($objService->getType() == 1 || $objService->getType() == 3)
                {
                    $arrReturn[$value] = $objService->getName();
                }
            }
        }

        return $arrReturn;
    }
    
    public function getLookUpServicesGeo()
    {
        $arrReturn = array();
        $arrFile = scan(TL_ROOT . "/system/modules/geolocation");
        
        foreach ($arrFile as $value)
        {
            if(preg_match("/GeoLookUp.*\.php/", $value) && !preg_match("/.*(Factory|Interface).*/", $value))
            {
                $objService = GeoLookUpFactory::getEngine($value);
                
                if($objService->getType() == 2 || $objService->getType() == 3)
                {
                    $arrReturn[$value] = $objService->getName();
                }
            }
        }
        
        return $arrReturn;
    }
}

?>