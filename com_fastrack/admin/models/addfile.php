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
 * @version 12th February 2015
 * @since 9th February 2015
 */

defined('_JEXEC') or die();
/** 
 * Fastrack Models Add File Class
 * 
 * @version 12th February 2015
 * @since 9th February 2015
 */
class FastrackModelsAddfile extends FastrackModelsDefault {
/** 
 * Construct
 * 
 * @version 12th February 2015
 * @since 11th February 2015
 * @return void
 */
	public function __construct() {
	
		$this->ftfile = $this->loadFile();
		return;
	}	
}