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
if(version_compare("2.10", VERSION, "<") == TRUE)
{
    $GLOBALS['TL_DCA']['tl_module']['palettes']['geolocation'] = '{title_legend},name,headline,type;{template_legend:hide},geo_template,geo_close,geo_user_change,geo_chosen;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
}
else
{
    $GLOBALS['TL_DCA']['tl_module']['palettes']['geolocation'] = '{title_legend},name,headline,type;{template_legend:hide},geo_template,geo_close,geo_user_change;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
}

/**
 * Fields 
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['geo_template'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['geo_template'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => array('tl_module_geolocation', 'getTemplates'),
    'eval' => array('mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['geo_close'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['geo_close'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['geo_chosen'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['geo_chosen'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr')    
);

$GLOBALS['TL_DCA']['tl_module']['fields']['geo_user_change'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['geo_user_change'],
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50')
);

/**
 * tl_module_geolocation
 */
class tl_module_geolocation extends Backend
{
    public function getTemplates()
    {
        return $this->getTemplateGroup('mod_geo');
    }
}

?>