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
 * along with Fastrack.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @version 	18th May 2015
 * @since 		26th November 2014
 */

defined('_JEXEC') or die();
/**
 * Mod Fastrack Preparation
 *
 * @version 	18th May 2015
 */
class ModFastrackPreparation {

	static public $params;

/**
  * Execute
  *
  * @version 	18th May 2015
  * @since 		26th November 2014
  * @param object Registry
  * @return void
  */
  	static public function execute($params) {

		if (empty(self::$params))
			self::$params = $params;
		$input = JFactory::getApplication()->input;
		$xx = FastrackHelper::mod_fastrack(self::$params);
		$TotalAvailable = FastrackHelper::getCondition('TotalAvailable');
		$warning = FastrackHelper::setCondition('warning', '');
		$pagin = FastrackHelper::getCondition('pagin', array());
		$control = FastrackHelper::getCondition('control', array());
		if (isset($control['NewSearch']))  {
			$control = FastrackHelper::setCondition('control', array());
			$pagin = FastrackHelper::setCondition('pagin', array());
		}
		if (! empty($control['keywords'])) {
			$search = explode(',', strtoupper($control['keywords']));
			$oldKw = array();
			if (isset($control['oldKeywords']) )
				$oldKw = explode(',', strtoupper($control['oldKeywords']));
			foreach ($search as $q=>$w)
				$search[$q] = trim($w);
			$control = FastrackHelper::setCondition('control', array());
			$control['keywords'] = $search;
			if ($control['keywords'] != $oldKw) {
				$pagin = FastrackHelper::setCondition('pagin', array());
			}
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
				$warning = FastrackHelper::setCondition('warning', "<p style='color: red'><strong>Your search for '".implode(',', $search)."' did not find any results.</strong></p>");
			}
			unset($DisplayList);
		}
		if (empty($control['OldMake']) AND isset($control['OldMake']))
			unset($control['OldMake']);
		if (empty($control['OldType']) AND isset($control['OldType']))
			unset($control['OldType']);
		if (isset($control['subtype']))
			if ($control['subtype'] === 'allsubtypes')
				unset($control['subtype'], $pagin);
		if (isset($control['model']))
			if ($control['model'] === 'allmodels')
				unset($control['model'], $pagin);
		if (@$control['make'] == "All Makes")
			unset($control['make'], $pagin, $control['models']);
		if (@$control['type'] == "All Types")
			unset($control['type'], $pagin, $control['subtypes']);
		if (@$control['OldMake'] !== @$control['make'] AND isset($control['make']))
			unset($control['model'], $pagin);
		if (@$control['OldType'] !== @$control['type'])
			unset($control['subtype'], $pagin);
		if (! empty($pagin)) {
			if (isset($pagin['startKeyValues'][$pagin['startKey']]))
				$pagin['startKey'] = $pagin['startKeyValues'][$pagin['startKey']];
			if ($pagin['startKey'] == $input->get('firstpage'))
				$pagin['startKey'] = $pagin['startKeyValues'][1];
			if ($pagin['startKey'] == $input->get('lasttpage'))
				$pagin['startKey'] = $pagin['startKeyValues'][count($pagin['startKeyValues'])];
			if ($pagin['startKey'] == $input->get('prevpage')) {
				$was = intval(array_search($pagin['oldStartKey'], $pagin['startKeyValues'])) - 1;
				if ($was < 1)
					$was = 1;
				$pagin['startKey'] = $pagin['startKeyValues'][$was];
			}
			if ($pagin['startKey'] == $input->get('nextpage')) {
				$was = intval(array_search($pagin['oldStartKey'], $pagin['startKeyValues'])) + 1;
				if ($was > count($pagin['startKeyValues']))
					$was--;
				$pagin['startKey'] = $pagin['startKeyValues'][$was];
			}
		}
		if (! isset($pagin))
			$pagin = array();
		$pagin = FastrackHelper::setCondition('pagin', $pagin);
		$control = FastrackHelper::setCondition('control', $control);
		
		FastrackHelper::setSearchControls($xx);

		$pagin = FastrackHelper::getCondition('pagin', $pagin);
		$control = FastrackHelper::getCondition('control', $control);

		
		# Analyse Attributes and convert to items
		foreach ($xx as $q=>$w) {
			foreach ($w['attributes']['attribute'] as $e=>$r){
				switch ($r['id']){
					case "35":
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
					case "119":
						$xx[$q]['listingtype']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['listingtype'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['listingtype'][0]);
						break;
					case "236":
						$xx[$q]['condition']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['condition'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['condition'][0]);
						break;
					case "25":
						$xx[$q]['stockref']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['stockref'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['stockref'][0]);
						break;
					case "21":
						$xx[$q]['status']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['status'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['status'][0]);
						break;
					case "19":
						$xx[$q]['description']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['description'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['description'][0]);
						break;
					case "10":
						$xx[$q]['hours']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['hours'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['hours'][0]);
						break;
					case "36":
						$xx[$q]['year']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['year'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['year'][0]);
						break;
					case "32":
						$xx[$q]['engpower']['value'] = $r[0];
						foreach ($r as $t=>$y){
							$xx[$q]['engpower'][$t] = $y;
						}
						unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['engpower'][0]);
						break;
					default:
						switch ($r['type']) {
							case "Decimal":
							case "Integer":
								$xx[$q]['measurements'] = $r['name'].':'.$r[0].$r['uom'].', ';
								unset($xx[$q]['attributes']['attribute'][$e]);
								break ;
							case "Text":
							case "Text30":
								$xx[$q]['text'] = $r['name'].':'.$r[0].', ';
								unset($xx[$q]['attributes']['attribute'][$e]);
								break ;
							case "Option":
								if (strpos($r['name'], "Config")) {
									$xx[$q]['config']['value'] = $r[0];
									foreach ($r as $t=>$y){
										$xx[$q]['config'][$t] = $y;
									}
									unset($xx[$q]['attributes']['attribute'][$e],$xx[$q]['config'][0]);
								} else {
									$xx[$q]['option'] = $r['name'].':'.$r[0].', ';
									unset($xx[$q]['attributes']['attribute'][$e]);
								}
								break ;
							default:
								//  Nothing to do here
						}
				}
			}
		}
		
	
		
		$yy = array();
		foreach ($xx as $q=>$w) {
			$display = false;
			if (!isset($control['make']) AND !isset($control['type'])) 
				$display = true;
			elseif (isset($control['make']) AND !isset($control['type'])) {
				if (isset($control['model'])) {
					if ($w['make'] == $control['make'] AND $w['model'] == $control['model'])
						$display = true;
				} else {
					if ($w['make'] == $control['make'])
						$display = true;
				}
					
			} elseif (!isset($control['make']) AND isset($control['type'])) {
				if (isset($control['subtype'])) {
					if ($w['type'] == $control['type'] AND $w['subtype'] == $control['subtype'])
						$display = true;
				} else {
					if ($w['type'] == $control['type'])
						$display = true;
				}
			} else {
				$x = 0;
				if($control['make'] == $w['make'] )
					$x++;
				if( @$control['model'] == $w['model'] OR !isset( $control['model'] ) )
					$x++;
				if($control['type'] == $w['type'] )
					$x++;
				if( @$control['subtype'] == $w['subtype'] OR !isset( $control['subtype'] ) )
					$x++;
				if ($x == 4)
					$display = true;
			}
			if ($display)
				$yy[$q] = $xx[$q];
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
		$order[] = 'option';
		$order[] = 'measurements';

		$xx = FastrackHelper::setCondition('xx', FastrackHelper::SortResults($yy, array('listprice'=>'DESC', 'make'=>'ASC', 'model' => 'ASC')));

		$pagin = FastrackHelper::setCondition('pagin', $pagin);
		$control = FastrackHelper::setCondition('control', $control);
		
		FastrackHelper::setSearchControls($xx);

		$pagin = FastrackHelper::getCondition('pagin', $pagin);
		$control = FastrackHelper::getCondition('control', $control);


		$count = FastrackHelper::setCondition('count', count($xx));
		$order = FastrackHelper::setCondition('order', $order);
		return ;
	}
/**
  * Execute
  *
  * @version 23rd February 2015
  * @since 26th November 2014
  * @param array Items
  * @retrun array
  */
	static public function startKeyValues($xx) {
		
		$pagin['startKeyValues'] = array();
		$pageItems = self::$params->get('pageitems');
		$x = 1;
		$page = 1;
		foreach($xx as $w) {
			if ($x == 1) {
				$pagin['startKeyValues'][$page++] = $w['id'];
			}
			$x++;
			if ($x > $pageItems)
				$x = 1;
		}
		FastrackHelper::setCondition('startKeyValues', $pagin['startKeyValues']); 
		return $pagin['startKeyValues'];
 	}
/**
  * Hidden Pagination Controls
  *
  * @version 17th February 2015
  * @since 17th February 2015
  * @retrun string
  */
	static public function hiddenPagin() {
		
		return '';
		$pagin = FastrackHelper::getCondition('pagin', array());
		return ModFastrackPreparation::hiddenPaginInput($pagin, 'pagin');
	}
/**
  * Hidden Pagination Controls
  *
  * @version 17th February 2015
  * @since 17th February 2015
  * @retrun string
  */
	static public function hiddenPaginInput($data, $name) {
		
		if (! is_array($data))
			return '';
		$result = '';
		foreach($data as $q=>$w) {
			if (is_array($w))
				$result .= ModFastrackPreparation::hiddenPaginInput($w, $name.'['.$q.']');
			else {
				$p = $q;
				switch ($p) {
					case 'oldStartKey':
						break;
					case 'startKey':
						$p = 'oldStartKey';
					default:
						$result .= '<input type="hidden" name="'.$name.'['.$p.']'.'" value="'.$w.'" />'."\n";
				}
			}
		}
		return $result;
	}
}