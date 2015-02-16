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

$input = JFactory::getApplication()->input;
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_fastrack_search/tmpl/default.css');



$TypeTotal = $input->get('TypeTotal');
$MakeTotal = $input->get('MakeTotal');
$menu = $input->get('menu', array(), 'ARRAY');
$type = $input->get('type', array(), 'ARRAY');
$make = $input->get('make', array(), 'ARRAY');
?>
<div id="ProductMenu">
<p style="margin-bottom: 5px;">Keyword Search: Separate by commas:</p>
<div>
<form name="TheSearchForm" id="TheSearchForm" method="post">

<p><input type="text" name="keywords" size="10" maxlength="75" style="width: 80%" />
<input class="searchbutton" type="submit" value="Search Now" name="Search"  />
<input type="submit" value="&nbsp;Refresh Display&nbsp;"  />
<input type="submit" value="&nbsp;New Search&nbsp;" name="New Search" /></p>

	<p>Type/SubType</p>
		
		<ul>
		<li><input type="radio" name="type" value="All Types" onclick="TheSearchForm.submit()">
      All Types (<?php echo $TypeTotal; ?>)</li>
<?php
echo FastrackHelper::buildPagination($xx, 'hidden');
$m = '';
$displaysubtype = false;
foreach ($menu['type'] as $w) {
	$q = explode("::", $w);
	$q[0] = trim($q[0]);
	$model = trim($q[1]);
	if ($q[0] !== $m) {
		$m = $q[0];
		if ($displaysubtype) {
			$displaysubtype = false;
			echo "</ul>\n</li>\n";	
		}
		echo  "<li><input type=\"radio\" name=\"type\" value=\"".$m."\" ";
		if (@$_POST['type'] == $m) {
			echo "checked";
			$displaysubtype = true;
		}
		echo " onclick=\"TheSearchForm.submit()()\">
      ".$m." (".$type[$m]['count'].")";
		if (@$_POST['type'] == $m)
			echo "<ul>\n";
		else
			echo "</li>\n";
	} 
	
	if (@$_POST['type'] == $m) {
		echo  "<li><input type=\"radio\" name=\"subtype\" value=\"".$model."\" ";
		if (@$_POST['subtype'] == $model)
			echo "checked";
				echo " onclick=\"TheSearchForm.submit()()\">
	  ".$model." (".$type[$m][$model]['count'].")</li>\n";
	}
}
if ($displaysubtype) {
	$displaysubtype = false;
	?></ul>
    </li>
    <?php	
}
?>
</ul>



<p>Make/Model</p>
<ul>
		<li><input type="radio" name="make" value="All Makes" onclick="TheSearchForm.submit()">
      All Makes (<?php echo $MakeTotal; ?>)</li>
      <?php
$m = '';
$displaysubtype = false;
foreach ($menu['make'] as $w) {
	$q = explode("::", $w);
	$q[0] = trim($q[0]);
	$model = trim($q[1]);
	if ($q[0] !== $m) {
		$m = $q[0];
		if ($displaysubtype) {
			$displaysubtype = false;
			?></ul>
            </li>
            <?php	
		}
		?>  <li>
      			<input type="radio" name="make" value="<?php echo $m; ?>"<?php
		if (@$_POST['make'] == $m)
			echo "checked";
		?> onclick="TheSearchForm.submit()()">
      <?php echo $m; ?> (<?php echo $make[$m]['count']; ?>)
      <?php
		if (@$_POST['make'] == $m) {
			$displaysubtype = true;
			?> <ul>
            <?php
		} else {
			?> </li>
            <?php
		}
	} 
	if (@$_POST['make'] == $m) {
		?> <li><input type="radio" name="model" value="<?php echo $model; ?>" <?php
		if (@$_POST['model'] == $model)
			echo "checked";
		?> onclick="TheSearchForm.submit()()" />
	  <?php echo $model; ?> (<?php echo $make[$m][$model]['count']; ?>)</li>
      <?php
	}
}
if ($displaysubtype) {
	$displaysubtype = false;
	?> </ul>
    </li>
    <?php
}
?>
</ul>

<input type="hidden" value="<?php echo @$_POST['make']; ?>" name="OldMake">
<input type="hidden" value="<?php echo @$_POST['type']; ?>" name="OldType">
 </form>
</div>
<?php
$input->set('TypeTotal', $TypeTotal);
$input->set('menu', $menu);
$input->set('type', $type);
