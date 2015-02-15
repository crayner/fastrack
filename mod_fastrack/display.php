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
 * @version 14th February 2015
 * @since 1st December 2014
 */

defined('_JEXEC') or die();

class FastrackDisplay {
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
  * Attribute Tests
  * var object
  */
  	private $templateTestAttrib;
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
  * Set Attribute
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @param string Attribute Name
  * @param mixed Attribute Value
  * @return void
  */
  	public function setAttribute($name, $value) {
	
		$this->attributes->$name = $value;
	}
/**
  * Add to Attribute
  *
  * @version 3rd December 2014	
  * @since 3rd December 2014	
  * @param string Attribute Name
  * @param string Attribute Value
  * @return void
  */
  	public function addToAttribute($name, $value) {
	
		if (isset($this->attributes->$name))
			$value = $this->attributes->$name . $value;
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
		
		/*  If Attribute Tested, then remove failed test values
		or remove test labels.  */
		foreach($this->templateTestAttrib as $name=>$value) {
			if (isset($this->attributes->$name)) {
				$this->output = str_replace(array('{{#'.$name.'}}','{{/'.$name.'}}'), '', $this->output); 
			} else {
				foreach($value as $search)
					$this->output = str_replace($search, '', $this->output);
			}
		}
		
		foreach ($this->templateAttrib as $search) {
			$name = str_replace(array('{{', '}}'), '', $search);
			$replace = '';
			if (isset($this->attributes->$name))
				$replace = $this->attributes->$name;
			$this->output = str_replace($search, $replace, $this->output);
		}

		return $this->output;
	}
/**
  * Define Template Attributes
  *
  * @version 3rd December 2014	
  * @since 1st December 2014	
  * @return void
  */
  	protected function defineTemplateAttributes() {
		
		$x = explode('{{', $this->template);
		$this->templateAttrib = array();
		$this->templateTestAttrib = array();
		$c = preg_match_all("(\{\{(\w*)\}\})", $this->template, $matches);
		$this->templateAttrib = array_unique($matches[0], SORT_REGULAR);
		$c = preg_match_all("(\{\{#(\w*)\}\})", $this->template, $matches);
		foreach($matches[0] as $w) {
			$name = substr($w, 3, -2);
			$length = strlen($w);
			$offset = 0;
			if (false !== ($start = strpos($this->template, "{{#".$name."}}", $offset))){
				$end = strpos($this->template, '{{/'.$name.'}}', $offset);
				if ($end > $start) {
					if (! isset($this->templateTestAttrib[$name]))
						$this->templateTestAttrib[$name]= array();
					$this->templateTestAttrib[$name][$offset] = substr($this->template, $start, $end + $length - $start);
					$offset = $end + $length;
				} else 
					$offset = strlen($this->template);
			}
		}
		return ;
	}
}