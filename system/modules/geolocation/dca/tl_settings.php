<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{geo_legend},geo_IPlookUpSettings,geo_GeolookUpSettings,geo_countryFallback,geo_customOverride;{geo_cookie_legend},geo_cookieDuration,geo_cookieName';

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
    'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit', 'multiple' => true, 'size' => 3, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_cookieName'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_cookieName'],
    'exclude' => true,
    'inputType' => 'text',
    'save_callback' => array(array('tl_settings_geolocation', 'saveCookieName')),
    'eval' => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['geo_countryFallback'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['geo_countryFallback'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => $this->getCountries(),
    'eval' => array('tl_class' => 'w50','multiple' => false, 'includeBlankOption' => true, 'chosen' => true)
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
    'eval' => array('includeBlankOption' => true, 'mandatory' => true, 'chosen' => true)
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

    public function saveCookieName($strString, $ObjDataContainer)
    {
        $arrSearch = array('/[^a-zA-Z0-9 _-]+/', '/ +/', '/\-+/');
        $arrReplace = array('', '-', '-');

        $strString = html_entity_decode($strString, ENT_QUOTES, $GLOBALS['TL_CONFIG']['characterSet']);
        $strString = strip_insert_tags($strString);
        $strString = utf8_romanize($strString);
        $strString = preg_replace($arrSearch, $arrReplace, $strString);

        return trim($strString, '-');;
    }
}

?>