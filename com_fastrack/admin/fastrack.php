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
 * along with Fastrack.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_fastrack.libraries.helper', JPATH_ADMINISTRATOR);

JLoader::registerPrefix('Fastrack', JPATH_COMPONENT_ADMINISTRATOR);

$lang = JFactory::getLanguage();
$lang->load('com_fastrack', JPATH_ADMINISTRATOR);

$input = JFactory::getApplication()->input;
$params = JFactory::getConfig('com_fastrack');

$path = JPATH_ADMINISTRATOR.'/components/com_fastrack/fastrack.xml';
if(file_exists($path)){
	$manifest = simplexml_load_file($path);
	$input->set('FASTRACK_VERSION', (string)$manifest->version);
}else{
	$input->set('FASTRACK_VERSION', '');
}

// Require specific controller if requested
if ( $controller = $input->get( 'controller', 'fastrack' ) ) 
	JLoader::import ( 'controllers.' . $controller, JPATH_COMPONENT );

// Create the controller
$classname  = 'FastrackController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute();

