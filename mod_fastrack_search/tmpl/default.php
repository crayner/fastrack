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
 * @version 17th February 2015
 */

defined('_JEXEC') or die();

JLoader::import('modules.mod_fastrack.helper', JPATH_SITE);

JLoader::import('components.com_fastrack.libraries.display', JPATH_ADMINISTRATOR);
JLoader::import('components.com_fastrack.libraries.mustache.src.Mustache.Autoloader', JPATH_ADMINISTRATOR);
Mustache_Autoloader::register();

$input = JFactory::getApplication()->input;
$doc = JFactory::getDocument();
$css = file_get_contents(JPATH_SITE.'/modules/mod_fastrack_search/tmpl/default.css');
$css = $params->get('css_search', $css);
$doc->addStyleDeclaration($css);

$control = FastrackHelper::getCondition('control', array());
if ( empty($control['keywords']))
	$keywords = NULL;
else
	$keywords = rtrim(implode(",", $control['keywords']), ",");

$TypeTotal = FastrackHelper::getCondition('TypeTotal', 0);
$MakeTotal = FastrackHelper::getCondition('MakeTotal', 0);
$menu = FastrackHelper::getCondition('menu', array());
$type = FastrackHelper::getCondition('type', array());
$make = FastrackHelper::getCondition('make', array());



$ProductMenu = array();
$ProductMenu['pagination'] = ModFastrackPreparation::hiddenPagin();
$ProductMenu['keywords'] = $keywords;
$ProductMenu['type'] = @$control['type'];
$ProductMenu['make'] = @$control['make'];

?>

<form name="TheSearchForm" id="TheSearchForm" method="post">

<?php if ($keywords === NULL) {

	if (!isset($control['menu'] )) {
		$TypeMenu = array();
		$TypeMenu['typeTotal'] = $TypeTotal; 
		$xx = FastrackHelper::getCondition('xx');
		$m = '';
		$displaysubtype = false;
		$value = array();
		foreach ($menu['type'] as $w) {
			$q = explode("::", $w);
			$q[0] = trim($q[0]);
			$model = trim($q[1]);
			if ($q[0] !== $m) {
				$m = $q[0];
				if (! empty($value))
					$TypeMenu['typelist'][] = $value;
				$mk = FastrackHelper::getSafeKey($m);
				if ($displaysubtype) 
					$displaysubtype = false;
				$value = array();
				$models = array();
				$value['key'] = $mk;
				$value['name'] = $m;
				if (@$control['type'] == $m) {
					$value['checked'] = ' checked';
					$displaysubtype = true;
				}
				$value['count'] = $type[$mk]['count'];
			} 
			if (@$control['type'] == $m) {
				$mv = array();
				$mv['name'] = $model;
				if (@$control['subtype'] == $model)
					$mv['selected'] = ' checked';
				$mv['count'] = $type[$mk][FastrackHelper::getSafeKey($model)]['count'];
				$models[] = $mv;
			}
			if (! empty ($models)) 
				$value['sublist']['subtype'] = $models;
		}
		
		if (! empty($value))
			$TypeMenu['typelist'][] = $value;
		
		if ($displaysubtype) 
			$displaysubtype = false;
		
	
		$ProductMenu['ProductTypes'] = $TypeMenu;
	}


	if (! isset($control['type'])) {
		$MakeMenu = array();
		$MakeMenu['makeTotal'] = $MakeTotal; 
		  
		$m = '';
		$displaysubtype = false;
		$value = array();
		foreach ($menu['make'] as $w) {
			$q = explode("::", $w);
			$q[0] = trim($q[0]);
			$model = trim($q[1]);
			if ($q[0] !== $m) {
				$m = $q[0];
				if (! empty($value))
					$MakeMenu['makelist'][] = $value;
				$mk = FastrackHelper::getSafeKey($m);
				if ($displaysubtype) 
					$displaysubtype = false;
				$value = array();
				$models = array();
				$value['key'] = $mk;
				$value['name'] = $m;
				$value['count'] = $make[$mk]['count'];
				if (@$control['make'] == $m) {
					$displaysubtype = true;
					$value['checked'] = ' checked';
				}
			} 
			if (@$control['make'] == $m) {
				$mv = array();
				$mv['name'] = $model;
				if (@$control['model'] == $model)
					$mv['selected'] = ' checked';
				$mv['count'] = $make[$mk][FastrackHelper::getSafeKey($model)]['count'];
				$models[] = $mv;
			}
			if (! empty ($models)) 
				$value['modellist']['models'] = $models;
		}
		if (! empty($value))
			$MakeMenu['makelist'][] = $value;
		if ($displaysubtype) 
			$displaysubtype = false;
		$ProductMenu['ProductMakes'] = $MakeMenu;
	}


} 

$m = new Mustache_Engine ;
$template = file_get_contents(JPATH_SITE.'/modules/mod_fastrack_search/tmpl/default.html');
echo $m->render($params->get('content_search', $template),  $ProductMenu);
?>
</form>