<?php
/**
 * GiCalReader is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GiCalReader is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GiCalReader.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		GiCalReader
 * @author		Hill Range Services http://gicalreader.hillrange.com.au
 * Code altered from original on G-Calendars Copyright (C) 2007 - 2013 Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('textarea');

class JFormFieldTextarea2 extends JFormFieldTextarea {
	
	protected $type = 'Textarea2';

	public function getInput(){
		$buffer = parent::getInput();
		if(isset($this->element->description)){
			$buffer .= '<label></label>';
			$buffer .= '<div style="float:left;">'.JText::_($this->element->description).'</div>';
		}
		return $buffer;
	}

	public function setup(SimpleXMLElement $element, $value, $group = NULL ){
		if(isset($element->content) && empty($value)){
			$value = $element->content;
		}
		return parent::setup($element, $value, $group);
	}
}