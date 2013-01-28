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
// Both fields, geo_single_page and geo_child_page
$arrPalettes = array('default', 'regular');

foreach ($arrPalettes as $PaletteName)
{
    if (key_exists($PaletteName, $GLOBALS['TL_DCA']['tl_page']['palettes']))
    {
        $mixPalette = trimsplit(";", $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName]);
        array_insert($mixPalette, 2, array("{geo_legend},geo_single_page,geo_child_page"));
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName] = implode(";", $mixPalette);
    }
}

// Only geo_child_page
$arrPalettes = array('root');

foreach ($arrPalettes as $PaletteName)
{
    if (key_exists($PaletteName, $GLOBALS['TL_DCA']['tl_page']['palettes']))
    {
        $mixPalette = trimsplit(";", $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName]);
        array_insert($mixPalette, 2, array("{geo_legend},geo_child_page"));
        $GLOBALS['TL_DCA']['tl_page']['palettes'][$PaletteName] = implode(";", $mixPalette);
    }
}

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'geo_single_page';
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'geo_child_page';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['geo_single_page'] = 'geo_single_choose';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['geo_child_page']  = 'geo_child_choose';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['geo_single_page'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_single_page'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_child_page'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_child_page'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_single_choose'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_choose'],
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => array('w3c' => 'geo_w3c', 'ip' => 'geo_ip', 'fallback' => 'geo_fallback'),
    'reference' => &$GLOBALS['TL_LANG']['tl_page'],
    'eval' => array('multiple' => true, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['geo_child_choose'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_page']['geo_choose'],
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => array('w3c' => 'geo_w3c', 'ip' => 'geo_ip', 'fallback' => 'geo_fallback'),
    'reference' => &$GLOBALS['TL_LANG']['tl_page'],
    'eval' => array('multiple' => true, 'mandatory' => true)
);

/**
 * Hooks
 */
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = array("tl_page_geolocation", "onload");

class tl_page_geolocation extends Backend
{

    /**
     * Show an error msg, if the cache is activated.
     * 
     * @param DC_Table $table
     * @return DC_Table 
     */
    public function onload(DC_Table $table)
    {
        // If error session is not an array return DC_Table
        if(!is_array($_SESSION['TL_ERROR']))
        {
                return $table;
        }
        
        // Check if we hav a id
        if ($table->id == null || $table->id == "")
        {
            unset($_SESSION['TL_ERROR']['geo_includeCache']);
            return $table;
        }

        // Get current tl_page
        $arrPage = $this->Database
                ->prepare("SELECT * FROM tl_page WHERE id=?")
                ->execute($table->id)
                ->fetchAllAssoc();
        
        // Check if we have a result
        if (count($arrPage) == 0)
        {
            unset($_SESSION['TL_ERROR']['geo_includeCache']);
            return $table;
        }

        // Check if the cache is activate
        if ($arrPage[0]['includeCache'] == 1 && $arrPage[0]['cache'] != 0 )
        {
            $_SESSION['TL_ERROR']['geo_includeCache'] = $GLOBALS['TL_LANG']['ERR']['GEO']['includeCache'];
        }
        else
        {
            unset($_SESSION['TL_ERROR']['geo_includeCache']);   
        }
        
        return $table;
    }

}

?>