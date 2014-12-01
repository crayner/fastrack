<?php
/**
 * Fastrack Reader is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Fastrack Reader is distributed in the hope that it will be useful,
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
 * @version 1st December 2014
 * @since 26th November 2014
 */

defined('_JEXEC') or die();

JLoader::import('modules.mod_fastrack.xmlparser', JPATH_SITE);


class ModFastrackHelper {
	static $params;
	public static $Conditions;
	private static $fileName			=	'';
/**	
  * Execute
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param object mod_fastrack Registryt
  * @return array Machinery
  */
	public static function execute($params){

		self::$params = $params;
		if (null === $params->get('fileName', null)) {
			self::loadModFastrackParams();
		}
		self::$Conditions = new stdClass();

		$reader = new xmlParser();
		$filename = self::fileName();
		try {
			$x = file_get_contents(JPATH_SITE.$filename);
		} catch (Exception $e) {
			sleep ( 2 );
			$x = file_get_contents(JPATH_SITE.$filename);
		}
			
		$xx = $reader->parseString($x);
		$xx = $reader->optXml($xx['dealer'][0]['listing']);
		self::setCondition('TotalAvailable', count($xx));
		return $xx;
	}
/**	
  * return File Name
  *
  * @version 30th November 2014
  * @since 27th November 2014
  * @return string FileName and Path
  */
	public static function fileName(){
	
		if (self::$fileName != '')
			return self::$fileName;
		$path = '/modules/mod_fastrack/data/';
		if ($path === "/modules/mod_fastrack/data/") {
			define('IMAGE_PATH_REL', $path);
			define('IMAGE_PATH_ABS', JPATH_SITE.$path);
		} else {
		}
		self::$fileName = $path . self::$params->get('filename');
		return self::$fileName;
	}
/**	
  * get Condition
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param string Condition Name
  * @return mixed Condition Value
  */
	public static function getCondition($name){
	
		return self::$Conditions->$name;
	}
/**	
  * get Condition
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param string Condition Name
  * @param mixed Condition Value
  * @return mixed Condition Value
  */
	public static function setCondition($name, $value){
	
		self::$Conditions->$name = $value;
		return self::$Conditions->$name;
	}
/**	
  * set Search Controls
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param string Condition Name
  * @return mixed ConditionValue
  */
	public static function setSearchControls($xx){

		$type = array();
		$make = array();
		$menu = array();
		$total = 0;
		$MakeTotal = 0;
		$TypeTotal = 0;
		
		foreach($xx as $q=>$w) {
			//Limit the Make/model based on type selection
			if ( isset($_POST['type']) AND empty ($_POST['subtype'])) {
				if ($w['type'] == $_POST['type']) {
					@$menu['make'][] = $w['make']."::".$w['model'];
					@$make[$w['make']]['count']++;
					@$make[$w['make']][$w['model']]['count']++;
					$MakeTotal++;
				}
			} elseif ( isset($_POST['type']) AND isset ($_POST['subtype'])) {
				if ($w['type'] == $_POST['type'] AND $w['subtype'] == $_POST['subtype']) {
					@$menu['make'][] = $w['make']."::".$w['model'];
					@$make[$w['make']]['count']++;
					@$make[$w['make']][$w['model']]['count']++;
					$MakeTotal++;
				}
			} else {
				@$menu['make'][] = $w['make']."::".$w['model'];
				@$make[$w['make']]['count']++;
				@$make[$w['make']][$w['model']]['count']++;
				$MakeTotal++;
			}
		
			//Limit the Type/subtype based on make selection
			if ( isset($_POST['make']) AND empty ($_POST['model'])) {
				if ($w['make'] == $_POST['make']) {
					@$menu['type'][] = $w['type']."::".$w['subtype'];
					@$type[$w['type']]['count']++;
					@$type[$w['type']][$w['subtype']]['count']++;
					$TypeTotal++;
				}
			} elseif ( isset($_POST['make']) AND isset ($_POST['model'])) {
				if ($w['make'] == $_POST['make'] AND $w['model'] == $_POST['model']) {
					@$menu['type'][] = $w['type']."::".$w['subtype'];
					@$type[$w['type']]['count']++;
					@$type[$w['type']][$w['subtype']]['count']++;
					$TypeTotal++;
				}
			} else {
				@$menu['type'][] = $w['type']."::".$w['subtype'];
				@$type[$w['type']]['count']++;
				@$type[$w['type']][$w['subtype']]['count']++;
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
		$type = ModFastrackHelper::setCondition('type', $type);
		$make = ModFastrackHelper::setCondition('make', $make);
		$menu = ModFastrackHelper::setCondition('menu', $menu);
		$total = ModFastrackHelper::setCondition('total', $total);
		$MakeTotal = ModFastrackHelper::setCondition('MakeTotal', $MakeTotal);
		$TypeTotal = ModFastrackHelper::setCondition('TypeTotal', $TypeTotal);
	}
/**
  * Load mod Fastrack Params
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * return void
  */
  	private static function loadModFastrackParams() {
		
		$sql = JFactory::getDbo();
		$query = $sql->getQuery(true);
		$query->from($sql->quoteName('#__modules'));
		$query->select($sql->quoteName('params'));
		$query->where($sql->quoteName('module') . ' = ' .$sql->quote('mod_fastrack'));
		$sql->setQuery($query);
		$x = explode('","', trim($sql->loadResult(), '{}'));
		$y = array();
		foreach ($x as $w) {
			$q = explode('":"', $w);
			$n = ltrim($q[0], '"');
			$v = self::$params->get($n);
			if (empty ($v))
				self::$params->set($n, rtrim($q[1], '"'));
		}
	}
/**
  * Build Pagination Form Elements
  *
  * @version 1st December 2014
  * @since 27th November 2014
  * @param array Items
  * @param string Input Element Types
  * return string HTML
  */
  	public static function buildPagination($xx, $inputType = 'submit') {
	

		$yy = $_POST['startKeyValues'];
		$t = '';
		$m = '';
		if (empty($_POST['startKey']))
			$_POST['startKey'] = 0;
		if ($_POST['startKey'] == 0)
			foreach($xx as $q=>$w) {
				$_POST['startKey'] = $w['id'];
				break ;
			}
		
		$pagination = '';
		ob_start(); ?>
		<div style="text-align: center; clear:both">
		<p>
		<input type="hidden" value="<?php echo $_POST['startKey']; ?>" name="oldStartKey" />
		<?php
		
		$first = reset($yy);
		$last = end($yy);
		$_POST['startKeyValues'] = $yy;
		$x = 1;
		foreach ($yy as $q=>$w) {
			if ($w == $first) {
				?><input type="<?php echo $inputType; ?>" name="startKey" value="<?php echo self::$params->get('firstpage'); ?>" class="Pagination" /> 
				<input type="<?php echo $inputType; ?>" name="startKey" value="<?php echo self::$params->get('prevpage'); ?>" class="Pagination" />
			
				<?php	
			}
			?>
			<input type="<?php echo $inputType; ?>" name="startKey" value="<?php echo $q; ?>"  <?php 
				if ($w == $_POST['startKey']) {
					?> class="Pagination PaginationChecked " <?php
				} else { ?>
				 class="Pagination"
				<?php }
			?> />
			<input type="hidden" name="startKeyValues[<?php echo $q; ?>]" value="<?php echo $w; ?>" />
			<?php	
			if ($w == $last) {
				?><input type="<?php echo $inputType; ?>" name="startKey" value="<?php echo self::$params->get('nextpage'); ?>" class="Pagination" /> 
				<input type="<?php echo $inputType; ?>" name="startKey" value="<?php echo self::$params->get('lastpage'); ?>" class="Pagination" /><?php	
			}
		}
		?></p>
		
		</div>
		<?php
		$pagination = ob_get_contents();
		ob_end_clean();
		if (count($yy) < 2)
			$pagination = '';
		self::setCondition('yy', $yy);
		return $pagination;
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
