<?php

if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'gp_protected_overwrite';
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

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['gp_protected'] = 'gp_protected_overwrite ';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['gp_protected_overwrite'] = 'gp_mode,gp_countries';

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_protected'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_protected'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_protected_overwrite'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_protected_overwrite'],
    'exclude' => true,    
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
        'gp_show' => &$GLOBALS['TL_LANG']['MSC']['hiddenShow'],
        'gp_hide' => &$GLOBALS['TL_LANG']['MSC']['hiddenHide']
    ),
    'reference' => &$GLOBALS['TL_LANG']['tl_content'],
    'eval' => array('mandatory' => true, 'includeBlankOption' => true)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['gp_countries'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_content']['gp_countries'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'options_callback' => array('GeoProtection', 'getCountriesByContinent'),
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
        //print_r($arrRow);
        //exit();

        $key = $arrRow['invisible'] ? 'unpublished' : 'published';
        $strGP = '';
        if ($arrRow['gp_protected'])
        {
            $strGP = ' (';
            $strGP .= ($arrRow['gp_mode'] == 'gp_show') ? ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenShow']) : ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenHide']);
            $strGP .= ':';

            if ($arrRow['gp_countries'] != "")
            {
                foreach (deserialize($arrRow['gp_countries']) as $c)
                {
                    $strGP .= ' ' . $c;
                }
            }

            $strGP .= ')';
        }

        return '
<div class="cte_type ' . $key . '">' . $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] . (($arrRow['type'] == 'alias') ? ' ID ' . $arrRow['cteAlias'] : '') . ($arrRow['protected'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ')' : ($arrRow['guests'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['guests'] . ')' : '')) . $strGP . '</div>
<div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : '') . ' block">
' . $this->getContentElement($arrRow['id']) . '
</div>' . "\n";
    }

}

?>