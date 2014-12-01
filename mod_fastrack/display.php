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
 * @since 1st December 2014
 */

defined('_JEXEC') or die();

class modFastrackDisplay {
/**
  * Attributes
  * var object
  */
  	private $attributes;
/**
  * Attributes
  * var array
  */
  	private $templateAttrib;
/**
  * template
  * var string
  */
  	private $template;
/**
  * Output
  * var string
  */
  	private $output;
/**
  * Construct
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @return void
  */
  	public function __construct() {
	
		$this->attributes = new stdClass;
		$this->template = '';
		$this->output = '';
	}
/**
  * Set Attributes
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @param string Attribute Name
  * @param string Attribute Value
  * @return void
  */
  	public function setAttribute($name, $value) {
	
		$this->attributes->$name = $value;
	}
/**
  * Set Template
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @param string Template
  * @return void
  */
  	public function setTemplate($value) {
	
		$this->template = $value;
	}
/**
  * Render Display
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @param string Template
  * @return string (HTML OUTPUT)
  */
  	public function render($template = '') {
		
		if ($template !== '')
			$this->setTemplate($template);
		$this->output = '';
		$this->defineTemplateAttributes();
		$this->output = $this->template;
		foreach ($this->templateAttrib as $name) {
			$search = '{{'.$name.'}}';
			$replace = '';
			if (isset($this->attributes->$name))
				$replace = $this->attributes->$name;
			$this->output = str_replace($search, $replace, $this->output);
		}
printAnObject($this, true);
		return $this->output;
	}
/**
  * Define Template Attributes
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @return void
  */
  	protected function defineTemplateAttributes() {
		
		$x = explode('{{', $this->template);
		$temp = '';
		$this->templateAttrib = array() ;
		foreach($x as $w) {
			$y = explode('}}', $w);
			if (count($y) == 2)
				$this->templateAttrib[] = $y[0];
		}
	}
}