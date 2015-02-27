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
 * @version 27th February 2015
 * @since 27th February 2015
 */

defined('_JEXEC') or die();

/**
 * Print an Object
 *
 * @version 16th February 2015
 * @since OLD
 * @param mixed The object to be printed
 * @param boolean Stop execution after printing object.
 * @param boolean Stop execution after printing object.
 * @return void
 */
	function printAnObject($object, $stop = false, $full = false) {
	
		$caller = debug_backtrace();
		echo "<pre>\n";
		echo $caller[0]['line'].': '.$caller[0]['file'];
		echo "\n</pre>\n";
		echo "<pre>\n";
		print_r($object);
		if ($full) 
			print_r($caller);
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
