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


JLoader::import('modules.mod_fastrack.helper', JPATH_SITE);
JLoader::import('modules.mod_fastrack.preparation', JPATH_SITE);

ModFastrackPreparation::execute();

$xx = ModFastrackHelper::getCondition('xx');
$input = JFactory::getApplication()->input;
$count = ModFastrackHelper::getCondition('count');
$warning = ModFastrackHelper::getCondition('warning');
$TotalAvailable = ModFastrackHelper::getCondition('TotalAvailable');
$yy = ModFastrackHelper::getCondition('yy');
$order = ModFastrackHelper::getCondition('order');

require( JModuleHelper::getLayoutPath( 'mod_fastrack' ) );