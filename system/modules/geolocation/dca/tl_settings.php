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
 * Selector
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'geo_customOverride';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['geo_customOverride'] = 'geo_overrideIps,geo_customCountryFallback';

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{geo_legend},geo_IPlookUpSettings,geo_GeolookUpSettings,geo_countryFallback,geo_cookieDuration,geo_customOverride';

/**
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_IPlookUpSettings'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_IPlookUpSettings'],
    'inputType' => 'multiColumnWizard',
    'explanation' => 'lookUpClassIP',
    'exclude' => true,
    'eval' => array
        (
        'helpwizard' => true,
        'style' => 'width:100%;',
        'columnFields' => array(
            'lookUpConfig' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpConfig'],
                'inputType' => 'text',
                'eval' => array('preserveTags' => true, 'decodeEntities' => true, 'style' => 'width:400px'),
            ),
            'lookUpClass' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpClass'],
                'inputType' => 'select',
                'eval' => array('style' => 'width:200px'),
                'options_callback' => array('tl_settings_geolocation', 'getIPLookUpServices')
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_GeolookUpSettings'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_GeolookUpSettings'],
    'inputType' => 'multiColumnWizard',
    'explanation' => 'lookUpClassGeo',
    'exclude' => true,
    'eval' => array
        (
        'helpwizard' => true,
        'style' => 'width:100%;',
        'columnFields' => array(
            'lookUpConfig' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpConfig'],
                'inputType' => 'text',
                'eval' => array('preserveTags' => true, 'decodeEntities' => true, 'style' => 'width:400px'),
            ),
            'lookUpClass' => array(
                'label' => $GLOBALS['TL_LANG']['tl_settings']['lookUpClass'],
                'inputType' => 'select',
                'eval' => array('style' => 'width:200px'),
                'options_callback' => array('tl_settings_geolocation', 'getGeoLookUpServices')
            )
        )
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_cookieDuration'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_cookieDuration'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit', 'multiple' => true, 'size' => 2, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_countryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => $this->getCountries(),
    'eval' => array('tl_class' => 'w50','multiple' => false, 'includeBlankOption' => true)
);

// IP Override
$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_customOverride'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_customOverride'],
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
    'eval' => array('includeBlankOption' => true, 'mandatory' => true)
);

class tl_settings_geolocation extends Backend
{

    public function getIPLookUpServices()
    {
        $arrReturn = array();
        $arrFile = scan(TL_ROOT . "/system/modules/geolocation");

        foreach ($arrFile as $value)
        {
            if (preg_match("/GeoLookUp.*\.php/", $value) && !preg_match("/.*(Factory|Interface).*/", $value))
            {
                $objService = GeoLookUpFactory::getEngine($value);

                if ($objService->getType() == GeoLookUpInterface::IP || $objService->getType() == GeoLookUpInterface::BOTH)
                {
                    $arrReturn[$value] = $objService->getName();
                }
            }
        }

        return $arrReturn;
    }

    public function getGeoLookUpServices()
    {
        $arrReturn = array();
        $arrFile = scan(TL_ROOT . "/system/modules/geolocation");

        foreach ($arrFile as $value)
        {
            if (preg_match("/GeoLookUp.*\.php/", $value) && !preg_match("/.*(Factory|Interface).*/", $value))
            {
                $objService = GeoLookUpFactory::getEngine($value);

                if ($objService->getType() == GeoLookUpInterface::GEO || $objService->getType() == GeoLookUpInterface::BOTH)
                {
                    $arrReturn[$value] = $objService->getName();
                }
            }
        }

        return $arrReturn;
    }

}

?>