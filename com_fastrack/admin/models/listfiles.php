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
 * @version 13th February 2015
 * @since 13th February 2015
 */

defined('_JEXEC') or die();
/** 
 * Fastrack Models List Files
 * 
 * @version 13th February 2015
 * @since 13th February 2015
 */
class FastrackModelsListfiles extends FastrackModelsDefault {
/**
 * Execute
 *
 * @version 13th February 2015
 * @since 13th February 2015
 * @param integer Limit Start
 * @param integer LImit
 * @return void
 */
 	public function execute($limitStart, $limit) {
		
		$query = $this->getFileListQuery($limitStart, $limit);
		return $this->getFileList($query, $limitStart, $limit);
	} 
/**
 * get File Total
 *
 * @version 13th February 2015
 * @since 13th February 2015
 * @return integer
 */
 	public function getFileTotal() {
		
		return $this->total;
	} 
/**
 * get File LIst
 *
 * @version 13th February 2015	
 * @since 13th February 2015	
 * @param object JQuery
 * @param integer LimitStart
 * @param integer limit
 * @return object 
 */
 	protected function getFileList($query, $limitstart, $limit) {
	
		$sql = JFactory::getDbo();
		$sql->setQuery($query);
		$result = $sql->loadObjectList();
		$this->total = $sql->getAffectedRows();
		return $result ;
	}
/**
 * get File List Query
 *
 * @version 13th February 2015	
 * @since 13th February 2015	
 * @param integer LimitStart
 * @param integer limit
 * @return object JDatabaseQuery 
 */
	protected function getFileListQuery($limitstart, $limit) {
	
		$sql = JFactory::getDbo();
		$query = $sql->getQuery(true);
		$query->from($sql->quoteName('#__fastrack_files'));
		$query->select('*');
		$query->order(array($sql->quoteName('name'). ' ASC'));
		return $query;
	}
}