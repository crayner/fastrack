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
 * @version 14th February 2015
 * @since 13th February 2015
 */

defined('_JEXEC') or die();
/** 
 * Fastrack Models List Files
 * 
 * @version 14th February 2015
 * @since 13th February 2015
 */
class FastrackModelsListfiles extends FastrackModelsDefault {
/**
 * Execute
 *
 * @version 14th February 2015
 * @since 13th February 2015
 * @param integer Limit Start
 * @param integer LImit
 * @return void
 */
 	public function execute($limitStart, $limit) {
		
		return $this->getFileList(NULL, $limitStart, $limit);
	} 
/**
 * get File Total
 *
 * @version 14th February 2015
 * @since 13th February 2015
 * @return integer
 */
 	public function getFileTotal() {
		
		return count($this->getFileList(NULL, 0, 10));
	} 
/**
 * get File List
 *
 * @version 14th February 2015	
 * @since 13th February 2015	
 * @param object JQuery
 * @param integer LimitStart
 * @param integer limit
 * @return object 
 */
 	protected function getFileList($query, $limitstart, $limit) {
	
		return FastrackHelper::getFileList($query, $limitstart, $limit);
	}
}