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
 * @since 9th February 2015
 */

defined('_JEXEC') or die();

if (! function_exists('printAnObject') ) JLoader::import('components.com_fastrack.libraries.debug', JPATH_ADMINISTRATOR);

/**
 * Fastrack Helper
 *
 * @version 3rd March 2015
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
 * File Identifier
 * @var string
 */
 	static $fileID;
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
 * @version 16th February 2015
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
		$record->imageURL = $input->getString('imageURL');
		$record->enquiryURL = $input->getString('enquiryURL');
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
  * @version 16th February 2015
  * @since 27th November 2014
  * @param object mod_fastrack Registry
  * @return array Machinery
  */
	public static function mod_fastrack($params){

		self::loadModFastrackParams($params);
		self::$Conditions = new stdClass();
		$input = Jfactory::getApplication()->input;
		$pagin = $input->post->get('pagin', array(), 'ARRAY');
		$pagin = self::setCondition('pagin', $pagin );
		$control = $input->post->get('control', array(), 'ARRAY');
		$control = self::setCondition('control', $control );
		$fileids = 	self::$params->get('filenames');
		self::setCondition('FileNames', self::getFileNames());
		self::setCondition('TotalAvailable', 0);
		self::setCondition('xx', array());
		foreach($fileids as $id) {
			$save = false;
			$reader = new xmlParser();
			$filename = self::fileName($id);
			try {
				$x = file_get_contents($filename);
			} catch (Exception $e) {
				sleep ( 2 );
				if (is_file($filename)) {
					$x = file_get_contents($filename);
				} else
					$save = true;
			}
			
			$xx = $reader->parseString($x);
			$xx = $reader->optXml($xx['dealer'][0]['listing']);
			foreach($xx as $q=>$w) {
				$xx[$q]['fileid'] = self::getCondition('ftfile')->id;
			}
			$xx = array_merge(self::getCondition('xx'), $xx);
			self::setCondition('TotalAvailable', count($xx));
			self::setCondition('xx', $xx);
		}
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
  * @version 16th February 2015
  * @since 27th November 2014
  * @param integer FileID
  * @return string FileName and Path
  */
	public static function fileName($id){
	
		$fileNames = self::getFileNames();
		$fileid = self::$params->get('filenames');
		$files = self::getFileNames();
		self::$fileName = $fileNames[$id];
		self::setCondition('ftfile', self::$fileName);
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
/**	
  * get Condition
  *
  * @version 16th February 2015
  * @since 27th November 2014
  * @param string Condition Name
  * @param mixed Default Value
  * @return mixed Condition Value
  */
	public static function getCondition($name, $default = NULL) {
	
		if ( isset ( self::$Conditions->$name ) )
			return self::$Conditions->$name;
		return self::setCondition( $name, $default );
	}
/**	
  * set Condition
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param string Condition Name
  * @param mixed Condition Value
  * @return mixed Condition Value
  */
	public static function setCondition($name, $value) {
	
		self::$Conditions->$name = $value;
		return self::$Conditions->$name;
	}
/**	
  * set Search Controls
  *
  * @version 16th February 2015
  * @since 27th November 2014
  * @param string Condition Name
  * @return mixed Condition Value
  */
	public static function setSearchControls($xx){

		$pagin = self::getCondition('pagin', array());
		$control = self::getCondition('control', array());
		$type = array();
		$make = array();
		$menu = array();
		$total = 0;
		$MakeTotal = 0;
		$TypeTotal = 0;
		if (isset($pagin['startKey']) and (isset($pagin['oldStartKey']))) {
			switch ($pagin['startKey']){
				case '<':
					$k = array_search($pagin['oldStartKey'], $pagin['startKeyValues']) - 1;
					if ( $k < 1 )
						$k = 1;
					$pagin['startKey'] = $pagin['startKeyValues'][$k];
					break;
				case '>':
					$k = array_search($pagin['oldStartKey'], $pagin['startKeyValues']) + 1;
					if ( $k > count($pagin['startKeyValues']) )
						$k = count($pagin['startKeyValues']);
					$pagin['startKey'] = $pagin['startKeyValues'][$k];
					break;
				case '>>':
					$pagin['startKey'] = $pagin['startKeyValues'][count($pagin['startKeyValues'])];
					break;
				case '<<':
					$pagin['startKey'] = $pagin['startKeyValues'][1];
					break;
			}
		}
		$pagin = self::setCondition('pagin', $pagin);
		foreach($xx as $q=>$w) {
			//Limit the Make/model based on type selection
			if ( isset($control['type']) AND empty ($control['subtype'])) {
				if ($w['type'] == $control['type']) {
					@$menu['make'][] = $w['make']."::".$w['model'];
					@$make[FastrackHelper::getSafeKey($w['make'])]['count']++;
					@$make[FastrackHelper::getSafeKey($w['make'])][FastrackHelper::getSafeKey($w['model'])]['count']++;
					$MakeTotal++;
				}
			} elseif ( isset($control['type']) AND isset ($control['subtype'])) {
				if ($w['type'] == $control['type'] AND $w['subtype'] == $control['subtype']) {
					@$menu['make'][] = $w['make']."::".$w['model'];
					@$make[FastrackHelper::getSafeKey($w['make'])]['count']++;
					@$make[FastrackHelper::getSafeKey($w['make'])][FastrackHelper::getSafeKey($w['model'])]['count']++;
					$MakeTotal++;
				}
			} else {
				@$menu['make'][] = $w['make']."::".$w['model'];
				@$make[FastrackHelper::getSafeKey($w['make'])]['count']++;
				@$make[FastrackHelper::getSafeKey($w['make'])][FastrackHelper::getSafeKey($w['model'])]['count']++;
				$MakeTotal++;
			}
		
			//Limit the Type/subtype based on make selection
			if ( isset($control['make']) AND empty ($control['model'])) {
				if ($w['make'] == $control['make']) {
					@$menu['type'][] = $w['type']."::".$w['subtype'];
					@$type[FastrackHelper::getSafeKey($w['type'])]['count']++;
					@$type[FastrackHelper::getSafeKey($w['type'])][FastrackHelper::getSafeKey($w['subtype'])]['count']++;
					$TypeTotal++;
				}
			} elseif ( isset($control['make']) AND isset ($control['model'])) {
				if ($w['make'] == $control['make'] AND $w['model'] == $control['model']) {
					@$menu['type'][] = $w['type']."::".$w['subtype'];
					@$type[FastrackHelper::getSafeKey($w['type'])]['count']++;
					@$type[FastrackHelper::getSafeKey($w['type'])][FastrackHelper::getSafeKey($w['subtype'])]['count']++;
					$TypeTotal++;
				}
			} else {
				@$menu['type'][] = $w['type']."::".$w['subtype'];
				@$type[FastrackHelper::getSafeKey($w['type'])]['count']++;
				@$type[FastrackHelper::getSafeKey($w['type'])][FastrackHelper::getSafeKey($w['subtype'])]['count']++;
				$TypeTotal++;
			}
		
			if (! isset($type[$w['type']]['image']))
				@$type[$w['type']]['image'] = $image;
			$total++;
		}
		$menu['make'] = array_unique($menu['make']);
		$menu['type'] = array_unique($menu['type']);
		sort($menu['make']);
		sort($menu['type']);
		$type = FastrackHelper::setCondition('type', $type);
		$make = FastrackHelper::setCondition('make', $make);
		$menu = FastrackHelper::setCondition('menu', $menu);
		$total = FastrackHelper::setCondition('total', $total);
		$MakeTotal = FastrackHelper::setCondition('MakeTotal', $MakeTotal);
		$TypeTotal = FastrackHelper::setCondition('TypeTotal', $TypeTotal);
		return ;
	}
/**
  * Sort Results
  *
  * @version 25th July 2014
  * @since 25th July 2014
  * @param array The Results from Table Search
  * @param array The fields in order for sort (first in array has highest priority, key is the field name and value = ASC or DESC.
  * @return array The Results
  */
  	public static function SortResults($Result, $Sort){
		
		reset($Sort);
		$direction = $y[key($Sort)] = current($Sort);
		$name = key($Sort);
		unset($Sort[$name]);
		$t = array();
		foreach($Result as $q=>$w) {
			$t[$q] = $w[$name];
		}
		if (strtoupper($direction) == 'DESC') {
			arsort($t);
		} else {
			asort($t);
		}
		$ss = array();
		foreach($t as $item=>$value)
			$ss[$value][$item] = $Result[$item];
		if (count($Sort) >= 1) {
			foreach($ss as $v=>$s) {
				$ss[$v] = self::SortResults($s, $Sort);
			}
		}
		$Result = array();
		foreach($ss as $v=>$s) {
			foreach ($s as $item=>$values)
				$Result[$item] = $values;
		}
		return $Result ;
	}
/**
  * Build Pagination Form Elements
  *
  * @version 16th February 2015
  * @since 27th November 2014
  * @param array Items
  * @param string Input Element Types
  * return string HTML
  */
  	public static function buildPagination($xx, $inputType = 'submit') {
	

		$pagin = self::getCondition('pagin', array());
		$yy = ModFastrackPreparation::startKeyValues($xx);
		$t = '';
		$m = '';
		if (empty($pagin['startKey']))
			$pagin['startKey'] = 0;
		if ($pagin['startKey'] == 0)
			foreach($xx as $q=>$w) {
				$pagin['startKey'] = $w['id'];
				break ;
			}
		
		$pagination = '';
		ob_start(); ?>
		<div style="text-align: center; clear:both">
		<p><input type="hidden" name="pagin[oldStartKey]" value="<?php echo $pagin['startKey']; ?>" />
		<?php

		if (self::$params->get('firstpage', '') == "") {
			self::$params->def('firstpage', '<<');
			self::$params->set('firstpage', '<<');
		}
		if (self::$params->get('prevpage', '') == "") {
			self::$params->def('prevpage', '<');
			self::$params->set('prevpage', '<');
		}
		$first = reset($yy);
		$last = end($yy);
		$pagin['startKeyValues'] = $yy;
		$x = 1;
		foreach ($yy as $q=>$w) {
			if ($w == $first) {
				?><input type="<?php echo $inputType; ?>" name="pagin[startKey]" value="<?php echo self::$params->get('firstpage'); ?>" class="Pagination" /> 
				<input type="<?php echo $inputType; ?>" name="pagin[startKey]" value="<?php echo self::$params->get('prevpage'); ?>" class="Pagination" />
			
				<?php	
			}
			?>
			<input type="<?php echo $inputType; ?>" name="pagin[startKey]" value="<?php echo $q; ?>"  <?php 
				if ($w == $pagin['startKey']) {
					?> class="Pagination PaginationChecked " <?php
				} else { ?>
				 class="Pagination"
				<?php }
			?> />
			<input type="hidden" name="pagin[startKeyValues][<?php echo $q; ?>]" value="<?php echo $w; ?>" />
			<?php	
			if ($w == $last) {
				?><input type="<?php echo $inputType; ?>" name="pagin[startKey]" value="<?php echo self::$params->get('nextpage'); ?>" class="Pagination" /> 
				<input type="<?php echo $inputType; ?>" name="pagin[startKey]" value="<?php echo self::$params->get('lastpage'); ?>" class="Pagination" /><?php	
			}
		}
		?></p>
		
		</div>
		<?php
		$pagination = ob_get_contents();
		ob_end_clean();
		if (count($yy) < 2)
			$pagination = '';
		self::setCondition('pagin', $pagin);
		return $pagination;
	}
/**
  * Image Creator
  *
  * Resize Images so speed up normal page render
  * @version 3rd March 2015
  * @since 3rd December 2014
  * @param array Item Details
  * @param object File Details
  * @return array Item Details
  */
  	public static function imageCreator($w, $ftfile) {
  
		$w['image'] = array();
		$count = 0;
		$ok = true;
		$imageName = substr($ftfile->name, 0, -4);
		do {
			$count++;
			if (is_file($ftfile->path.$imageName.'_'.$w['id'].'_'.strval($count).'.jpg')) {
				$w['image'][$count] = $imageName.'_'.$w['id'].'_'.strval($count).'.jpg';
				if (! is_file($ftfile->path.'store/'.$imageName.'_'.$w['id'].'_'.strval($count).'.jpg')) {
					if (false !== ($im = @getimagesize($ftfile->path.$w['image'][$count]))) {
						$height = 245;
						$y = $im[1]/$height;
						$width = intval($im[0]/$y);
						$thumb = imagecreatetruecolor($width, $height);
						$source = imagecreatefromjpeg($ftfile->path.$w['image'][$count]);
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $im[0], $im[1]);
						if (! is_dir($ftfile->resultPath)) {
							FastrackHelper::buildPath($ftfile->resultPath);
							if (! is_dir($ftfile->resultPath))
								JError::raiseWarning('42', JText::_('COM_FASTRACK_ERROR_RESULTPATH'));
						}
						imagejpeg($thumb, $ftfile->resultPath.$imageName.'_'.$w['id'].'_'.strval($count).'.jpg');
						imagedestroy($source);
						imagedestroy($thumb);
					} else {
						unset($w['image'][$count]);
						$ok = false;
						if ($count == 1) 
							$w['image'][1] = 'PlaceHolder.png';
					}
				}
			} else {
				$ok = false;
				if ($count == 1) 
					$w['image'][1] = 'PlaceHolder.png';
			}
		} while ($ok);
		return $w;
	}
/**
 * get Safe Key
 *
 * @version 18th February 2015
 * @since 18th February 2015
 * @param string un-safe Key
 * @return string Safe Key
 */
 	static public function getSafeKey($key) {
		
		return strtolower('_'.trim(str_replace(array(' ', '&', "\n", "\r", "\t", '.', ',', '/'), '', $key)));
	}
/**
  * Build Path
  * @version 3rd March 2015
  * @since 3rd March 2015
  * @param sting Path to Build
  * @return void
  */
  	static public function buildPath($path) {
		
		$p = pathinfo($path);
		if (! is_dir($p['dirname']))
			FastrackHelper::buildPath($p['dirname']);
		mkdir($path);
		return ;
	}
}

