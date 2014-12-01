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
 * @version 30th November 2014
 * @since 26th November 2014
 */

defined('_JEXEC') or die();

class ModFastrackPreparation {

	static public $params;

/**
  * Execute
  *
  * @version 30th November 2014
  * @since 26th November 2014
  * @retrun void
  */
  	static public function execute() {
		if (empty(self::$params))
			self::$params = new \Joomla\Registry\Registry;
		$input = JFactory::getApplication()->input;
		$xx = ModFastrackHelper::execute(self::$params);
		$TotalAvailable = ModFastrackHelper::getCondition('TotalAvailable');
		$warning = ModFastrackHelper::setCondition('warning', '');
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
				$warning = ModFastrackHelper::setCondition('warning', "<p style='color: red'><strong>Your search for '".implode(',', $search)."' did not find any results.</strong></p>");
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
		if (! empty($_POST)) {
			if (isset($_POST['startKeyValues'][$_POST['startKey']]))
				$_POST['startKey'] = $_POST['startKeyValues'][$_POST['startKey']];
			if ($_POST['startKey'] == $input->get('firstpage'))
				$_POST['startKey'] = $_POST['startKeyValues'][1];
			if ($_POST['startKey'] == $input->get('lasttpage'))
				$_POST['startKey'] = $_POST['startKeyValues'][count($_POST['startKeyValues'])];
			if ($_POST['startKey'] == $input->get('prevpage')) {
				$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) - 1;
				if ($was < 1)
					$was = 1;
				$_POST['startKey'] = $_POST['startKeyValues'][$was];
			}
			if ($_POST['startKey'] == $input->get('nextpage')) {
				$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) + 1;
				if ($was > count($_POST['startKeyValues']))
					$was--;
				$_POST['startKey'] = $_POST['startKeyValues'][$was];
			}
		}
		
		
		ModFastrackHelper::setSearchControls($xx);
		
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
						if ($input->get('showGSTPrice', 1, 'BOOLEAN')) 
							if ($xx[$q]['price']['gst_value'] == 'ex-GST') {
								$xx[$q]['price']['value'] += $xx[$q]['price']['value'] * $xx[$q]['price']['gst'] / 100;
								$xx[$q]['price']['gst_value'] = 'inc-GST';
							}
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
		
		$xx = ModFastrackHelper::setCondition('xx', ModFastrackHelper::SortResults($yy, array('listprice'=>'DESC', 'make'=>'ASC', 'model' => 'ASC')));
		$count = ModFastrackHelper::setCondition('count', count($xx));
		$order = ModFastrackHelper::setCondition('order', $order);
		self::startKeyValues($xx);
	}
/**
  * Execute
  *
  * @version 30th November 2014
  * @since 26th November 2014
  * @param array Items
  * @retrun array
  */
	static private function startKeyValues($xx) {
		
		$_POST['startKeyValues'] = array();
		$pageItems = self::$params->get('pageitems');
		$x = 1;
		$page = 1;
		foreach($xx as $w) {
			if ($x == 1) {
				$_POST['startKeyValues'][$page++] = $w['id'];
			}
			$x++;
			if ($x >= $pageItems)
				$x = 1;
		}
		return $_POST['startKeyValues'];
 	}
}