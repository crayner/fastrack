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
 * @since 9th February 2015
 */

defined('_JEXEC') or die();
/**
 * Fastrack Helper
 *
 * @version 14th February 2015
 * @since 9th February 2015
 */
class FastrackHelper {
/**
 * Parameters
 * @var Registry object
 */
 	static $params;
/**
 * Conditions
 * @var Standardobject
 */
 	static $Conditions;
/**
 * File Name
 * @var string
 */
 	static $fileName;
/**
 * File Names
 * @var array
 */
 	static $fileNames;
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
			$document->addScript(JPATH_ADMINISTRATOR.'components/com_fastrack/libraries/jquery/fastrack/ftNoConflict.js');
		}

		if (isset($libraries['jqueryui'])) {
			$theme = 'bootstrap';
			if (is_string($libraries['jqueryui']) && !empty($theme) && $theme == -1) {
				$theme = $libraries['jqueryui'];
			} else {
				$libraries['bootstrap'] = true;
			}
			$document->addStyleSheet(JPATH_ADMINISTRATOR.'components/com_fastrack/libraries/jquery/themes/'.$theme.'/jquery-ui.custom.css');
			$document->addScript(JPATH_ADMINISTRATOR.'components/com_fastrack/libraries/jquery/ui/jquery-ui.custom.min.js');
		}

		if (isset($libraries['bootstrap'])) {
			JHtml::_('bootstrap.framework');
		}

		if (isset($libraries['chosen'])) {
			JHtml::_('formbehavior.chosen', 'select');
		}

		if (isset($libraries['fastrack'])) {
			$document->addScript(JPATH_ADMINISTRATOR.'components/com_fastrack/libraries/fastrack/fastrack.js');
			$document->addStyleSheet(JPATH_ADMINISTRATOR.'components/com_fastrack/libraries/fastrack/fastrack.css');
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
 	static function getFileList($query = NULL, $limitstart = 0, $limit = 10) {
	
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
/**	
  * Execute mod_fastrack
  *
  * @version 14th February 2015
  * @since 27th November 2014
  * @param object mod_fastrack Registry
  * @return array Machinery
  */
	public static function mod_fastrack($params){

		self::loadModFastrackParams($params);
		self::$Conditions = new stdClass();

		$reader = new xmlParser();
		$filename = self::fileName();
		try {
			$x = file_get_contents($filename);
		} catch (Exception $e) {
			sleep ( 2 );
			if (is_file($filename)) {
				$x = file_get_contents($filename);
			} else
				return array();
		}
			
		$xx = $reader->parseString($x);
		$xx = $reader->optXml($xx['dealer'][0]['listing']);
		self::setCondition('TotalAvailable', count($xx));
		self::setCondition('xx', $xx);
		return $xx;
	}
/**
 * Load mod Fastrack Params
 *
 * @version 27th November 2014
 * @since 27th November 2014
 * return void
 */
  	private static function loadModFastrackParams($params) {

		self::$params = $params;
		return ;
	}
/**	
  * return File Name
  *
  * @version 1st December 2014
  * @since 27th November 2014
  * @return string FileName and Path
  */
	public static function fileName(){
	
		if (self::$fileName != '')
			return self::$fileName;
		$fileNames = self::getFileNames();
		self::$fileName = reset($fileNames);
		self::$fileName = self::$fileName->fileName;
		return self::$fileName;
	}
/**	
  * get File Names
  *
  * @version 14th February 2015
  * @since 14th February 2015
  * @return array 
  */
	public static function getFileNames(){
	
		$x = FastrackHelper::getFileList();
		$result = array();
		foreach ($x as $w) {
			$result[$w->id] = $w;
			$result[$w->id]->fileName = $w->path.$w->name;
		}
		return $result;
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
