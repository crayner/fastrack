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
 * @version 9th February 2015
 * @since 9th February 2015
 */

defined('_JEXEC') or die();
/**
 * Fastrack Helper
 *
 * @version 9th February 2015
 * @since 9th February 2015
 */
class FastrackHelper {
/**
 * Get Actions
 *
 * @version 9th February 2015
 * @since 9th February 2015
 * @param integer File ID
 * @return object
 */
 	public static function getActions($fileId = 0) {

		$user  = JFactory::getUser();
		$result  = new JObject;
		if (empty($fileId)) {
			$assetName = 'com_fastrack';
		}
		else {
			$assetName = 'com_fastrack.file.'.(int) $fileId;
		}

		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete');
		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
/**
 * Get Actions
 *
 * @version 9th February 2015
 * @since 9th February 2015
 */
	public static function loadLibrary ( $libraries = array ( 'jquery' => true ) ) {

		if (JFactory::getDocument()->getType() != 'html') {
			return ;
		}

		$document = JFactory::getDocument();
		if (isset($libraries['jquery'])) {
			JHtml::_('jquery.framework');
			$document->addScript(JURI::root().'components/com_fastrack/libraries/jquery/fastrack/ftNoConflict.js');
		}

		if (isset($libraries['jqueryui'])) {
			$theme = 'bootstrap';
			if (is_string($libraries['jqueryui']) && !empty($theme) && $theme == -1) {
				$theme = $libraries['jqueryui'];
			} else {
				$libraries['bootstrap'] = true;
			}
			$document->addStyleSheet(JURI::root().'components/com_fastrack/libraries/jquery/themes/'.$theme.'/jquery-ui.custom.css');
			$document->addScript(JURI::root().'components/com_fastrack/libraries/jquery/ui/jquery-ui.custom.min.js');
		}

		if (isset($libraries['bootstrap'])) {
			JHtml::_('bootstrap.framework');
		}

		if (isset($libraries['chosen'])) {
			JHtml::_('formbehavior.chosen', 'select');
		}

		if (isset($libraries['fastrack'])) {
			$document->addScript(JURI::root().'components/com_fastrack/libraries/fastrack/fastrack.js');
			$document->addStyleSheet(JURI::root().'components/com_fastrack/libraries/fastrack/fastrack.css');
		}

	}
/**
 * Save File Definition
 *
 * @version 12th February 2015
 * @since 12th February 2015
 * @return integer File Definition ID
 */
  	static function saveFileDefinition() {
		
		$input = new JInput();
		$record = new STDClass();
		$record->id = $input->getInt('id', 0);
		$record->name = $input->getString('name');
		$record->path = $input->getString('path');
		$record->resultPath = $input->getString('resultPath');
		if ( (int) $record->id === 0) 
			$result = JFactory::getDbo()->insertObject("#__fastrack_files", $record, 'id');
		else
			$result = JFactory::getDbo()->updateObject("#__fastrack_files", $record, 'id');
		if ($result)
			$input->set('id', $record->id);
		return $record->id;
	}
/**
 * Save File Definition
 *
 * @version 12th February 2015
 * @since 12th February 2015
 * @param integer File ID
 * @return void
 */
  	static function deleteFileDefinition($id) {
		
		if ( (int) $id < 1)
			return ;
		$sql = JFactory::getDbo();
		$query = $sql->getQuery(true);
		$query->delete($sql->quoteName('#__fastrack_files'));
		$query->where($sql->quoteName('id') . ' = ' . $id);
		$sql->setQuery($query);
		$sql->execute();
		return ;
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
 	static function getFileList($query, $limitstart, $limit) {
	
		if (! is_a($query, 'JDatabaseQueryMysqli'))
			$query = FastrackHelper::getFileListQuery($limitstart, $limit);
		$sql = JFactory::getDbo();
		$sql->setQuery($query);
		$result = $sql->loadObjectList();
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
	static function getFileListQuery($limitstart, $limit) {
	
		$sql = JFactory::getDbo();
		$query = $sql->getQuery(true);
		$query->from($sql->quoteName('#__fastrack_files'));
		$query->select('*');
		$query->order(array($sql->quoteName('name'). ' ASC'));
		return $query;
	}
}

/**
 * Print an Object
 *
 * @version 10th November 2014
 * @since OLD
 * @param mixed The object to be printed
 * @param boolean Stop execution after printing object.
 * @return void
 */
	function printAnObject($object, $stop = false) {
	
		$caller = debug_backtrace();
		echo "<pre>\n";
		echo $caller[0]['line'].': '.$caller[0]['file'];
		//var_dump($caller);
		echo "\n</pre>\n";
		echo "<pre>\n";
		print_r($object);
		//var_dump($object);
		echo "\n</pre>\n";
		if ($stop) 
			trigger_error('Object Print Stop', E_USER_ERROR);
		return ;
	}
/**
 * File an Object
 *
 * @version 10th November 2014
 * @since OLD
 * @param mixed The object to be printed
 * @param string Name of File
 * @return void
 */
	function fileAnObject($object, $name = NULL) {
	
		$config = JFactory::getConfig();
		$logpath = $config->get('log_path');
		if ($name === NULL)
			$fn = '/' . substr(md5(print_r($object, true)), 0, 12).'.dump';
		else
			$fn = '/' . $name . '.dump';
		$caller = debug_backtrace();
		$data = $caller[0]['line'].': '.$caller[0]['file']."\n";
		$data .= print_r($object, true);
		$x = '';
		foreach ($caller as $w) {
			$x =  $w['line'].': '.$w['file'].' '.$w['function']."\n". $x;
		}
		$data .= "\n".$x;
		file_put_contents($logpath.$fn, $data);
		return ;
	}
