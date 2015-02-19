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
 * @version 19th February 2015
 * @since 1st December 2014
 */

defined('_JEXEC') or die();

JLoader::import('components/com_fastrack/libraries/mustache/src/mustache/Autoloader', JPATH_ADMINISTRATOR);
/**
 * Fastrack Display
 *
 * @version 19th February 2015
 * @since 1st December 2014
 */

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
  * Template Sections
  * var array
  */
  	private $sections;
/**
  * Template Section Test
  * var boolean
  */
  	private $sectionTest;
/**
  * Template Lists
  * var array
  */
  	private $templateLists;
/**
  * Output
  * var string
  */
  	private $output;
/**
  * Attribute Types
  * var array
  */
  	private $attributeType = array();
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
	
		$this->attributes->$name = $value ;
	}
/**
  * Get Attribute
  *
  * @version 1st December 2014	
  * @since 1st December 2014	
  * @param string Attribute Name
  * @return mixed Value
  */
  	private function getAttribute($name) {
	
		return $this->attributes->$name ;
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
  * @version 19th February 2015	
  * @since 1st December 2014	
  * @param string Template
  * @return string (HTML OUTPUT)
  */
  	public function render($template = '') {
		
$this->attributes->typelist[1]['checked'] = ' checked';
$this->attributes->typelist[1]['sublist'][0]['models'][0]['count'] = 3;
$this->attributes->typelist[1]['sublist'][0]['models'][0]['key'] = '_model0';
$this->attributes->typelist[1]['sublist'][0]['models'][0]['model'] = 'Model 0';
$this->attributes->typelist[1]['sublist'][0]['models'][1]['count'] = 2;
$this->attributes->typelist[1]['sublist'][0]['models'][1]['key'] = '_model1';
$this->attributes->typelist[1]['sublist'][0]['models'][1]['model'] = 'Model 1';
		if ($template !== '')
			$this->setTemplate($template);
		$this->output = '';
		$this->defineAttributeTypes();
		$this->defineTemplateAttributes();
		$this->template = $this->parseTemplateSections();
		$this->output = $this->template;
		
		
		foreach ($this->templateAttrib as $search) {
			$name = str_replace(array('{{', '}}'), '', $search);
			$replace = '';
			if (isset($this->attributes->$name))
				$replace = $this->attributes->$name;
			$this->output = str_replace($search, $replace, $this->output);
		}
foreach($this->sections as $f)
	printAnObject(htmlspecialchars($f[0]));
printAnObject($this->attributes);
printAnObject($this->templateAttrib);
printAnObject($this->attributeType);
printAnObject($this->templateTestAttrib);
printAnObject($this->output, true);

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
		$this->templateLists = $matches[1];
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
/**
  * Define Template Attributes
  *
  * @version 18th February 2015
  * @since 18th February 2015	
  * @param string Attribute Name
  * @param mixed Attribute Value
  * @return void
  */
	public function setListAttribute($name, $value){
		
		if (! isset ( $this->attributes->$name))
			$this->setAttribute($name, array());
		$current = $this->getAttribute($name);
		$current[] = $value;
		$this->setAttribute($name, $current);
		return ;
	}
/**
  * Define Attribute Types
  *
  * defines the attribute types within the attributes.
  * @version 19th February 2015
  * @since 19th February 2015	
  * @return void
  */
	private function defineAttributeTypes(){
		
		$this->attributeType = array();
		foreach ((array) $this->attributes as $k=>$v) {
			if (is_array($v)) {
					$this->attributeType[$k]['type'] = 'list';
					$this->attributeType[$k]['list'] = array();
					$this->attributeType[$k]['list'] = $this->defineAttributeList($this->attributeType[$k]['list'], $v);
			} else
				$this->attributeType[$k]['type'] = 'single';
		}
		return ;
	}
/**
  * Define Attribute List
  *
  * defines the attribute types within the attributes.
  * @version 19th February 2015
  * @since 19th February 2015	
  * @param mixed array Key
  * @param array Value
  * @return void
  */
	private function defineAttributeList($attrib,  $value){
		
		foreach ($value as $k=>$v) 
			$attrib = $this->defineAttributeSubTypeValues($attrib, $k, $v);
		return $attrib;
	}
/**
  * Define Attribute Sub Type Values
  *
  * defines the attribute types within the attributes.
  * @version 19th February 2015
  * @since 19th February 2015	
  * @param array Attibute Type part.
  * @param mixed Value
  * @return array
  */
	private function defineAttributeSubTypeValues($attrib, $key, $value){
		
		if ( is_array ( $value ) ) {
			foreach($value as $k=>$v) {
				if (is_array($v)) {
					$attrib[$k]['type'] = 'list';
					$attrib[$k]['list'] = array();
					$attrib[$k]['list'] = $this->defineAttributeList($attrib[$k]['list'], $v);
				} else {
					$attrib[$k]['type'] = 'single';
				}
			}
		} else {
			$attrib[$k]['type'] = 'single';
		}
		return $attrib;
	}
/**
 * Parse Template Sections
 *
 * @version 19th February 2015
 * @since 19th February 2015
 * @return string base Template
 */
 	private function parseTemplateSections() {
		
		$this->sections = array();
		$template = $this->template;
		foreach ($this->templateLists as $listName) {
			while (false !== ($s = mb_strpos($template, '{{#'.$listName.'}}'))) {
				$l = strlen('{{/'.$listName.'}}') ;
				$f = mb_strpos($template, '{{/'.$listName.'}}') + $l;
				$sec = substr($template, $s, $f - $s);
				$this->sections[$listName][] = substr($sec, $l, -$l);
				$template = str_replace($sec, '{{'.$listName.'}}', $template);
			}
		}
		$this->sectionTest = true;
		while ($this->sectionTest) {
			$this->sectionTest = $this->parseSections();
		}
		return $template;
	}
/**
 * Parse Sections
 *
 * @version 19th February 2015
 * @since 19th February 2015
 * @return boolean Found matches
 */
 	private function parseSections() {
	
		$this->sectionTest = false;
		foreach ($this->sections as $pList=>$ans) {
			foreach($ans as $key=>$text) {
				foreach ($this->templateLists as $listName) {
					while (false !== ($s = mb_strpos($text, '{{#'.$listName.'}}'))) {
							$l = strlen('{{/'.$listName.'}}') ;
							$f = mb_strpos($text, '{{/'.$listName.'}}') + $l;
							$sec = substr($text, $s, $f - $s);
							$this->sections[$listName][] = substr($sec, $l, -$l);
							$text = str_replace($sec, '{{'.$listName.'}}', $text);
							$this->sections[$pList][$key] = $text;
							$this->sectionTest = true;
					}
				}
			}
		}
		return $this->sectionTest;
	}
}