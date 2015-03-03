<?php
/**
 * Fastrack Reader/Viewer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Fastrack Reader/Viewer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GiCalReader.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @version 3rd March 2015
 * @since 13th February 2015
 */

defined('_JEXEC') or die();

JLoader::import('components.com_fastrack.libraries.parser', JPATH_ADMINISTRATOR);



/** 
 * Fastrack Models List Files
 * 
 * @version 3rd March 2015
 * @since 13th February 2015
 */
class FastrackModelsTest extends FastrackModelsDefault {


	protected $config;
	protected $parse;
/**
 * Execute
 *
 * @version 3rd March 2015
 * @since 13th February 2015
 * @return array Results
 */
 	public function execute() {
		
		$this->ftfile = $this->loadFile();
		$results = array();
		$this->parse = new stdClass ;
		$results['name'] = 'Ok';
		$results['path'] = 'Ok';
		$results['resultPath'] = 'Ok';
		$results['enquiryURL'] = 'Ok';
		if (! is_file($this->ftfile->path.$this->ftfile->name))
			$result['name'] = JText::_('COM_FASTRACK_ERROR_NAME');
			
		if (! is_dir($this->ftfile->path)) {
			FastrackHelper::buildPath($this->ftfile->path);
			if (! is_dir($this->ftfile->path))
				$result['path'] = JText::_('COM_FASTRACK_ERROR_PATH');
		}
		if (! is_dir($this->ftfile->resultPath)) {
				FastrackHelper::buildPath($this->ftfile->resultPath);
			if (! is_dir($this->ftfile->resultPath))
				$result['resultPath'] = JText::_('COM_FASTRACK_ERROR_RESULTPATH');
		}
		if (strtolower(substr($this->ftfile->enquiryURL, 0, 4)) == 'http')
			$file_headers = @get_headers( $this->ftfile->enquiryURL );
		else 
			$file_headers = @get_headers( JUri::root().$this->ftfile->enquiryURL );
		if ($file_headers[0] != "HTTP/1.1 200 OK")
			$results['enquiryURL'] = $file_headers[0].' '.JText::_('COM_FASTRACK_ERROR_ENQUIRYURL');
		
		$this->config = $this->getConfig();
	
		$reader = new xmlParser();
		try {
			$x = file_get_contents($this->config->filename);
		} catch (Exception $e) {
			sleep ( 2 );
			$x = file_get_contents($this->config->filename);
		}
			
		$xx = $reader->parseString($x);
		$xx = $reader->optXml($xx['dealer'][0]['listing']);
		$this->parse->TotalAvailable = count($xx);		
		$this->parse->xx = $xx;
		$results['parserTotal'] = sprintf(JText::_('COM_FASTRACK_PARSERTOTAL_MESSAGE'), $this->ftfile->name, $this->parse->TotalAvailable);



		return $results;
	} 
/**
 * get Configuration
 *
 * @version 14th February 2015
 * @since 13th February 2015
 * @return object
 */
 	protected function getConfig() {
	
		$config = new stdClass() ;
		// Absolute path of the xml file to parse

		$config->parsename = 'toowoomba';
		$config->filename = $this->ftfile->path.$this->ftfile->name;
		// Absolute path of the ftp directory
		$config->ftpdir = $this->ftfile->path;
		$config->hostroot = str_replace(JURI::base(true), '', JURI::base()); ;
		$config->documentroot = JPATH_ROOT;
		$config->datastore = $this->ftfile->resultPath;
		
		$config->pageitems = 10;
		$config->firstpage = '<<';
		$config->lastpage = '>>';
		$config->prevpage = '<';
		$config->nextpage = '>';
		
		//The order is the list order for displayiong the information on the screen.
		$order = array();
		$order[] = "type";
		$order[] = 'subtype';
		$order[] = "make";
		$order[] = "model";
		$order[] = "config";
		$order[] = "listingtype";
		$order[] = "condition";
		$order[] = "year";
		$order[] = "hours";
		$order[] = "stockref";
		$order[] = "status";
		$order[] = "engpower";
		$order[] = "id";
		$order[] = "description";
		$order[] = 'price';
		$config->order = $order;

		
		return $config;
	}
}