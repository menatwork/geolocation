<?php 

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    geolocation
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Register hook 
 */
if(TL_MODE == 'FE')
{
    if($GLOBALS['TL_HOOKS']['getContentElement'] == null)
    {
        $GLOBALS['TL_HOOKS']['getContentElement'][] = array('Geolocation', 'checkContentelement');
    }
    else
    {
        $GLOBALS['TL_HOOKS']['getContentElement'] = array_merge(array(array('Geolocation', 'checkContentelement')), $GLOBALS['TL_HOOKS']['getContentElement'] );
    }

    if($GLOBALS['TL_HOOKS']['parseFrontendTemplate'] == null)
    {
        $GLOBALS['TL_HOOKS']['parseFrontendTemplate'][] = array('Geolocation', 'checkModuleelement');
    }
    else
    {
        $GLOBALS['TL_HOOKS']['parseFrontendTemplate'] = array_merge(array(array('Geolocation', 'checkModuleelement')), $GLOBALS['TL_HOOKS']['parseFrontendTemplate'] );
    }
}

$GLOBALS['TL_HOOKS']['dispatchAjax'][] = array('Geolocation', 'dispatchAjax');


/**
 * Add FE Mod
 */
$GLOBALS['FE_MOD']['miscellaneous']['geolocation'] = 'ModuleGeolocation';

/**
 * Blacklist tables for syncCto
 */
$GLOBALS['SYC_CONFIG']['table_hidden'] = array_merge( (array) $GLOBALS['SYC_CONFIG']['table_hidden'], array
(
    'tl_geodata',
));

?>