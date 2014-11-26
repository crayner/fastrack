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
JLoader::import('modules.mod_fastrack.xmlparser', JPATH_SITE);

define('PAGEITEMS', $params->get('pageitems', 10, 'INT'));
const FIRSTPAGE = '<<';
const LASTPAGE = '>>';
const PREVPAGE = '<';
const NEXTPAGE = '>';


$xx = ModFastrackHelper::execute($params);
$TotalAvailable = ModFastrackHelper::getCondition('TotalAvailable');
$warning = '';
# $_POST Management
if (isset($_POST['New_Search']))
	unset($_POST);
if (isset($_POST['keywords']) AND ! empty($_POST['keywords'])) {
	$search = explode(',', strtoupper($_POST['keywords']));
	foreach ($search as $q=>$w)
		$search[$q] = trim($w);
	unset($_POST);
	$DisplayList = array();
	foreach ($xx as $q=>$w) {
		$test = strtoupper(serialize($w));
		$result = 0;
		$found = true;
		foreach ($search as $s) {
			str_replace($s, $s, $test, $result);
			if ($result == 0){
				$found = false;
				break;
			}
		}
		if ($found)
			$DisplayList[$q] = $w;
	}
	if (count($DisplayList) > 0) {
		$xx = $DisplayList;
	} else {
		$warning = "<p style='color: red'><strong>Your search for '".implode(',', $search)."' did not find any results.</strong></p>";
	}
	unset($DisplayList);
}
if (empty($_POST['OldMake']) AND isset($_POST['OldMake']))
	unset($_POST['OldMake']);
if (empty($_POST['OldType']) AND isset($_POST['OldType']))
	unset($_POST['OldType']);
if (@$_POST['make'] == "All Makes")
	unset($_POST['make'], $_POST['startKey'],$_POST['startKeyValues']);
if (@$_POST['type'] == "All Types")
	unset($_POST['type'], $_POST['startKey']);
if (@$_POST['OldMake'] !== @$_POST['make'] AND isset($_POST['make']))
	unset($_POST['model'], $_POST['startKey'],$_POST['startKeyValues']);
if (@$_POST['OldType'] !== @$_POST['type'])
	unset($_POST['subtype'], $_POST['startKey'],$_POST['startKeyValues']);
if (!isset($_POST['startKey']))
	$_POST['startKey'] = 1;
if (strval(intval($_POST['startKey'])) == $_POST['startKey'])
	$_POST['startKey'] = @$_POST['startKeyValues'][$_POST['startKey']];
if ($_POST['startKey'] == FIRSTPAGE)
	$_POST['startKey'] = $_POST['startKeyValues'][1];
if ($_POST['startKey'] == LASTPAGE)
	$_POST['startKey'] = $_POST['startKeyValues'][count($_POST['startKeyValues'])];
if ($_POST['startKey'] == PREVPAGE) {
	$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) - 1;
	if ($was < 1)
		$was = 1;
	$_POST['startKey'] = $_POST['startKeyValues'][$was];
}
if ($_POST['startKey'] == NEXTPAGE) {
	$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) + 1;
	if ($was > count($_POST['startKeyValues']))
		$was--;
	$_POST['startKey'] = $_POST['startKeyValues'][$was];
}


$type = array();
$make = array();
$menu = array();
$total = 0;
$MakeTotal = 0;
$TypeTotal = 0;

foreach($xx as $q=>$w) {
	//Limit the Make/model based on type selection
	if ( isset($_POST['type']) AND empty ($_POST['subtype'])) {
		if ($w['type'] == $_POST['type']) {
			@$menu['make'][] = $w['make']."::".$w['model'];
			@$make[$w['make']]['count']++;
			@$make[$w['make']][$w['model']]['count']++;
			$MakeTotal++;
		}
	} elseif ( isset($_POST['type']) AND isset ($_POST['subtype'])) {
		if ($w['type'] == $_POST['type'] AND $w['subtype'] == $_POST['subtype']) {
			@$menu['make'][] = $w['make']."::".$w['model'];
			@$make[$w['make']]['count']++;
			@$make[$w['make']][$w['model']]['count']++;
			$MakeTotal++;
		}
	} else {
		@$menu['make'][] = $w['make']."::".$w['model'];
		@$make[$w['make']]['count']++;
		@$make[$w['make']][$w['model']]['count']++;
		$MakeTotal++;
	}

	//Limit the Type/subtype based on make selection
	if ( isset($_POST['make']) AND empty ($_POST['model'])) {
		if ($w['make'] == $_POST['make']) {
			@$menu['type'][] = $w['type']."::".$w['subtype'];
			@$type[$w['type']]['count']++;
			@$type[$w['type']][$w['subtype']]['count']++;
			$TypeTotal++;
		}
	} elseif ( isset($_POST['make']) AND isset ($_POST['model'])) {
		if ($w['make'] == $_POST['make'] AND $w['model'] == $_POST['model']) {
			@$menu['type'][] = $w['type']."::".$w['subtype'];
			@$type[$w['type']]['count']++;
			@$type[$w['type']][$w['subtype']]['count']++;
			$TypeTotal++;
		}
	} else {
		@$menu['type'][] = $w['type']."::".$w['subtype'];
		@$type[$w['type']]['count']++;
		@$type[$w['type']][$w['subtype']]['count']++;
		$TypeTotal++;
	}

	if (! isset($type[$w['type']]['image']))
		@$type[$w['type']]['image'] = $image;
	$total++;
}
$menu['make'] = array_unique($menu['make']);
$menu['type'] = array_unique($menu['type']);
sort($menu['make']);
sort($menu['type']);





# Analyse Attributes and convert to items

foreach ($xx as $q=>$w) {
	foreach ($w['attributes']['attribute'] as $e=>$r){
		switch ($r['name']){
			case "Price":
				$xx[$q]['price']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['price'][$t] = $y;
				}
				$xx[$q]['listprice'] = $xx[$q]['price']['value'];
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['price'][0]);
				break;
			case "Listing Type":
				$xx[$q]['listingtype']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['listingtype'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['listingtype'][0]);
				break;
			case "Item Condition":
				$xx[$q]['condition']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['condition'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['condition'][0]);
				break;
			case "Stock/Ref #":
				$xx[$q]['stockref']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['stockref'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['stockref'][0]);
				break;
			case "Status":
				$xx[$q]['status']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['status'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['status'][0]);
				break;
			case "Description":
				$xx[$q]['description']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['description'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['description'][0]);
				break;
			case "Hours":
				$xx[$q]['hours']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['hours'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['hours'][0]);
				break;
			case "Year":
				$xx[$q]['year']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['year'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['year'][0]);
				break;
			case "Eng Power":
				$xx[$q]['engpower']['value'] = $r[0];
				foreach ($r as $t=>$y){
					$xx[$q]['engpower'][$t] = $y;
				}
				unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['engpower'][0]);
				break;
			default:
				if (strpos($r['name'], "Config")) {
					$xx[$q]['config']['value'] = $r[0];
					foreach ($r as $t=>$y){
						$xx[$q]['config'][$t] = $y;
					}
					unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['config'][0]);
				}
				
		}
	}
}


# now build display

$order = array();
$order[] = "type";
$order[] = 'subtype';
$order[] = "make";
$order[] = "model";
$order[] = "config";
$order[] = "listingtype";
$order[] = "condition";
$order[] = "year";
$order[] = "hours";
$order[] = "stockref";
$order[] = "status";
$order[] = "engpower";
$order[] = "id";
$order[] = "description";
$order[] = 'price';






$yy = array();

foreach ($xx as $q=>$w) {
	$display = false;
	if (!isset($_POST['make']) AND !isset($_POST['type'])) 
		$display = true;
	elseif (isset($_POST['make']) AND !isset($_POST['type'])) {
		if (isset($_POST['model'])) {
			if ($w['make'] == $_POST['make'] AND $w['model'] == $_POST['model'])
				$display = true;
		} else {
			if ($w['make'] == $_POST['make'])
				$display = true;
		}
			
	} elseif (!isset($_POST['make']) AND isset($_POST['type'])) {
		if (isset($_POST['subtype'])) {
			if ($w['type'] == $_POST['type'] AND $w['subtype'] == $_POST['subtype'])
				$display = true;
		} else {
			if ($w['type'] == $_POST['type'])
				$display = true;
		}
	} else {
		$x = 0;
		if($_POST['make'] == $w['make'] )
			$x++;
		if( @$_POST['model'] == $w['model'] OR !isset( $_POST['model'] ) )
			$x++;
		if($_POST['type'] == $w['type'] )
			$x++;
		if( @$_POST['subtype'] == $w['subtype'] OR !isset( $_POST['subtype'] ) )
			$x++;
		if ($x == 4)
			$display = true;
	}
	if ($display)
		$yy[$q] = $xx[$q];
}

$xx = SortResults($yy, array('listprice'=>'DESC', 'make'=>'ASC', 'model' => 'ASC'));

$count = count($xx);

/**  Do work here for pagination preparation */


require( JModuleHelper::getLayoutPath( 'mod_fastrack' ) );