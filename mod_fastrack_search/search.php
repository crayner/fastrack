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
 * @version 3rd March 2015
 * @since 17th February 2015
 */

defined('_JEXEC') or die();
/**
 * Module Fastrack Search
 *
 * @version 3rd March 2015
 * @since 17th February 2015
 */
class ModFastrackSearch {
/**
 * Class Properties
 */
 	static protected $params = NULL;
	static protected $input = NULL;
/**
 * Execute
 *
 * @version 3rd March 2015
 * @since 17th February 2015
 * @param object \Joomla\Registry\Registry
 * @return void
 */
	static public function execute($params = NULL) {
	
		self::$input = JFactory::getApplication()->input;
		if (is_a($params, 'Joomla\Registry\Registry'))
			self::$params = $params;
		else 
			self::$params = new Joomla\Registry\Registry;
		$sql = JFactory::getDbo();
		$query = $sql->getQuery(true);
		$query->from($sql->quoteName('#__modules'));
		$query->select($sql->quoteName('params'));
		$query->where($sql->quoteName('module') . ' = ' . $sql->quote('mod_fastrack'));
		$query->where($sql->quoteName('title') . ' = ' . $sql->quote($params->get('module_id')));
		$sql->setQuery($query);
		$mfp = $sql->loadResult();
		$mfp = json_decode($mfp);
		unset($mfp->moduleclass_sfx, $mfp->module_tag, $mfp->bootstrap_size, $mfp->header_tag, $mfp->header_class, $mfp->style);
		$mfp = json_encode($mfp);
		self::$params->loadString($mfp);
		ModFastrackPreparation::execute(self::$params);
		return ;
	}
/**
 * Hidden Search Display
 *
 * @version 22nd February 2015
 * @since 17th February 2015
 * @return string Form Inputs
 */
	static public function hiddenSearch() {
	
		$control = FastrackHelper::getCondition('control', array());
		$result = '';
		foreach ($control as $q=>$w) {
			switch ($q) {
				case 'OldMake':
					break;
				case 'make':
					$result .= "<input type=\"hidden\" name=\"control[OldMake]\" value=\"".$w."\" />\n";
					$result .= "<input type=\"hidden\" name=\"control[".$q."]\" value=\"".$w."\" />\n";
					break;
				case 'OldType':
					break;
				case 'type':
					$result .= "<input type=\"hidden\" name=\"control[OldType]\" value=\"".$w."\" />\n";
					$result .= "<input type=\"hidden\" name=\"control[".$q."]\" value=\"".$w."\" />\n";
					break;
				case 'oldKeywords':
					break ;
				case 'keywords':
					if (is_array($w)) {
						$result .= "<input type=\"hidden\" name=\"control[".$q."]\" value=\"".rtrim(implode(',', $w), ',')."\" />\n";
						$result .= "<input type=\"hidden\" name=\"control[oldKeywords]\" value=\"".rtrim(implode(',', $w), ',')."\" />\n";
					}
					break;
				default:
					$result .= "<input type=\"hidden\" name=\"control[".$q."]\" value=\"".$w."\" />\n";
			}
		}
		return $result;
	}
}