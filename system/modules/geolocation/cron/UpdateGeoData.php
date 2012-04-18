<?php //if (!defined('TL_ROOT')) die('You can not access this file directly!');
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
 * Initialize the system
 */
define('TL_MODE', 'BE');
require_once('../../../initialize.php');

/**
 * Class UpdateGeoData
 */
class UpdateGeoData extends Backend
{

	/**
	 * Initialize the controller
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Implement the commands to run by this batch program
	 */
	public function run()
	{
            $this->import("Files");
            $zipname = 'system/tmp/tmp.zip';
            $csvFile = 'system/tmp/geoData.csv';
            
            //get the new zip-file
            copy('http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip',TL_ROOT. '/'.$zipname );
            
            // open zip archive
            $zip = new ZipReader($zipname);
            
            // process files in TL_ROOT and TL_FILES
            while ($zip->next()) {
                if ($zip->file_name == 'GeoIPCountryWhois.csv'){
                    // save file
                    $f = new File($csvFile);
                    if (!$f->write($zip->unzip())) 
                            throw new Exception(sprintf($text['fileerrwrite'], $csvFile));
                    $f->close();
                    
                    //prepare SQL from CSV
                    $f = new File($csvFile);
                    $set = array();
                    
                    $query = "INSERT INTO `tl_geodata` (ip_start, ip_end, ipnum_start, ipnum_end, country_short, country) VALUES ";
                    while(($arrRow = @fgetcsv($f->handle, 1024, ',')) !== false)
                    {
                        $query .= '("'.$arrRow[0].'","'.$arrRow[1].'","'.$arrRow[2].'","'.$arrRow[3].'","'.$arrRow[4].'","'.$arrRow[5].'"),';
                        
                    }
                   
                    //cut off last ","
                    $query = substr($query, 0, -1);
                    //Empty table
                    $this->Database->prepare('TRUNCATE TABLE `tl_geodata`')->execute();
                    //insert new Data
                    $test = $this->Database->prepare($query)->set($set)->execute();
                    //delete file
                    $f->close();
                    $f->delete();

                }
            }
            $this->Files->delete($zipname);
            $this->log('Geodata updated successfully.', 'UpdateGeoData run()', TL_GENERAL);
	}
	
}

/**
 * Instantiate log purger
 */
$objUpdateGeoData = new UpdateGeoData();
$objUpdateGeoData->run();

?>