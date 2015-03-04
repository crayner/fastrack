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
 * @version 14th February 2015
 * @since 14th February 2015
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