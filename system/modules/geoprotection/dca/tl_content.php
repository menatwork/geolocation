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
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'gp_protected';
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('gp_tl_content', 'addGpType');

/**
 * Palettes
 */
// replace palettes
foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $palette => $v)
{
    if ($palette == '__selector__')
    {
        continue;
    }
    $GLOBALS['TL_DCA']['tl_content']['palettes'][$palette] = str_replace('{expert_legend:hide}', '{gp_protection_legend:hide},gp_protected;{expert_legend:hide}', $GLOBALS['TL_DCA']['tl_content']['palettes'][$palette]);
}

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['gp_protected'] = 'gp_mode,gp_countries';

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_protected'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_protected'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_mode'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_mode'],
    'default' => 'show',
    'exclude' => true,
    'inputType' => 'select',
    'options' => array(
        'gp_show' => &$GLOBALS['TL_LANG']['MSC']['hiddenHide'],
        'gp_hide' => &$GLOBALS['TL_LANG']['MSC']['hiddenShow']
    ),
    'reference' => &$GLOBALS['TL_LANG']['tl_content'],
    'eval' => array('mandatory' => true, 'includeBlankOption' => true)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_countries'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_countries'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => array('gp_tl_content', 'getCountriesByContinent'),
    'eval' => array('multiple' => true, 'size' => 8, 'mandatory' => true)
);

/**
 * Class gp_tl_content
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  MEN AT WORK 2011 
 * @author     MEN AT WORK <cms@men-at-work.de> 
 * @package    Controller
 */
class gp_tl_content extends Controller
{

    /**
     * Add the gp type of the content element
     * @param array
     * @return string
     */
    public function addGpType($arrRow)
    {
        $key = $arrRow['invisible'] ? 'unpublished' : 'published';
        $strGP = '';
        if ($arrRow['gp_protected'])
        {
            $strGP = ' (';
            $strGP .= ($arrRow['gp_mode'] == 'gp_show') ? ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenShow']) : ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenHide']);
            $strGP .= ':';
            foreach (deserialize($arrRow['gp_countries']) as $c)
            {
                $strGP .= ' ' . $c;
            }
            $strGP .= ')';
        }

        return '
<div class="cte_type ' . $key . '">' . $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] . (($arrRow['type'] == 'alias') ? ' ID ' . $arrRow['cteAlias'] : '') . ($arrRow['protected'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ')' : ($arrRow['guests'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['guests'] . ')' : '')) . $strGP . '</div>
<div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : '') . ' block">
' . $this->getContentElement($arrRow['id']) . '
</div>' . "\n";
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
            $arrAux[$strConKey] = $strConKeyTranslated;
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
            $strOrgKey = array_search($strConKey, $arrAux);
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