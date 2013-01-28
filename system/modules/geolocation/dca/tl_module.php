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