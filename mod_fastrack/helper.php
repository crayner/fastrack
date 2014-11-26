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
 * @version 26th November 2014
 */

defined('_JEXEC') or die();


class ModFastrackHelper {
	static $params;
	private static $Conditions;
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
		self::$Conditions = new stdClass();
		$x = self::$Conditions;

		$reader = new xmlParser();
		try {
			$x = file_get_contents(self::fileName());
		} catch (Exception $e) {
			sleep ( 2 );
			$x = file_get_contents(self::fileName());
		}
			
		$xx = $reader->parseString($x);
		$xx = $reader->optXml($xx['dealer'][0]['listing']);
		self::$Conditions->TotalAvailable = count($xx);
		return $xx;
	}
/**	
  * return File Name
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @return string FileName and Path
  */
	public static function fileName(){
	
		if (self::$fileName != '')
			return self::$fileName;
		$path = rtrim(self::$params->get('filepath', 'modules/mod_fastrack/data'), '/').'/';
		define('PRODUCTIMAGES', $path);
		self::$fileName = $path . self::$params->get('filename');
		return self::$fileName;
	}
/**	
  * get Condition
  *
  * @version 27th November 2014
  * @since 27th November 2014
  * @param string Condition Name
  * @return mixed ConditionValue
  */
	public static function getCondition($name){
	
		return self::$Conditions->$name;
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
  * Sort Results
  *
  * @version 25th July 2014
  * @since 25th July 2014
  * @param array The Results from Table Search
  * @param array The fields in order for sort (first in array has highest priority, key is the field name and value = ASC or DESC.
  * @return array The Results
  */
  	function SortResults($Result, $Sort){
		
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
				$ss[$v] = SortResults($s, $Sort);
			}
		}
		$Result = array();
		foreach($ss as $v=>$s) {
			foreach ($s as $item=>$values)
				$Result[$item] = $values;
		}
		return $Result ;
	}
