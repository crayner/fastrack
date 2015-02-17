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

JLoader::import('modules.mod_fastrack.display', JPATH_SITE);

$input = JFactory::getApplication()->input;
$doc = JFactory::getDocument();
$css = <<<CSS
/* CSS Document */
#ProductMenu {
	background: #dddddd;
	border-radius: 20px;
	padding: 20px 2px 20px 2px;
}
#ProductMenu h3 {
	line-height: unset;
}

#ProductMenu ul {
	list-style-type: none;	
}
#ProductMenu label {
	font-size: 80%;	
}
#ProductMenu input[type="submit"], .EnqButton  {
	border: 1px solid #C6C6C6;
	background-color: #FBD701;
	border-radius: 5px;	
	padding: 2px;
	color: #777777;
	width: 110px;
/*	text-shadow: 0 1px 0 #3c61ab;*/
}
.EnqButton {
margin-top: 2px;
}
#ProductMenu input[type="submit"]:hover {
/*	border: none;
	background-color: transparent;	
	text-decoration: underline ; */
}

.searchbutton {
	position:relative;
	padding 0;
	margin: 0;
}
CSS;

$css = $params->get('css_search', $css);

$doc->addStyleDeclaration($css);

$control = FastrackHelper::getCondition('control', array());
if ( empty($control['keywords']))
	$keywords = NULL;
else
	$keywords = rtrim(implode(",", $control['keywords']), ",");

$TypeTotal =  FastrackHelper::getCondition('TypeTotal', 0);
$MakeTotal = FastrackHelper::getCondition('MakeTotal', 0);
$menu = FastrackHelper::getCondition('menu', array());
$type = FastrackHelper::getCondition('type', array());
$make = FastrackHelper::getCondition('make', array());

$template = file_get_contents(JPATH_SITE.'/modules/mod_fastrack_search/tmpl/display.html');
$display = new FastrackDisplay();
$display->setAttribute('keywords', $keywords);



?>
<div id="ProductMenu">
<p style="margin-bottom: 5px;">Keyword Search: Separate by commas:</p>
<div>
<form name="TheSearchForm" id="TheSearchForm" method="post">

<p><input type="text" name="control[keywords]" size="10" maxlength="75" style="width: 80%" value="<?php echo $keywords; ?>" />
<input class="searchbutton" type="submit" value="Search Now" name="control[Search]"  />
<input type="submit" value="&nbsp;Refresh Display&nbsp;"  />
<input type="submit" value="&nbsp;New Search&nbsp;" name="control[NewSearch]" /></p>
<?php if ($keywords === NULL): 
	$display->setSubAttribute('ProductTypes'); ?>
	//<p>Type/SubType</p>
		
		//<ul>
		//<li><input type="radio" name="control[type]" value="All Types" onclick="TheSearchForm.submit()">
      All Types (<?php echo $TypeTotal; ?>)</li>
<?php
$xx = FastrackHelper::getCondition('xx');
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
		//	echo "</ul>\n</li>\n";	
		}
		$display->addSubItemtoAttribute('PropertyTypes.type.'.FastrackHelper::createSafeKey($m), 'value', $m);
	//	echo  "<li><input type=\"radio\" name=\"control[type]\" value=\"".$m."\" ";
		if (@$control['type'] == $m) {
			echo " checked";
			$display->addSubItemtoAttribute('PropertyTypes.type.'.FastrackHelper::createSafeKey($m), 'checked', ' checked');
			$displaysubtype = true;
		}
		echo " onclick=\"TheSearchForm.submit()()\">
      ".$m." (".$type[FastrackHelper::createSafeKey($m)]['count'].")";
		if (@$control['type'] == $m)
			echo "<ul>\n";
		else
			echo "</li>\n";
	} 
	
	if (@$control['type'] == $m) {
		echo  "<li><input type=\"radio\" name=\"control[subtype]\" value=\"".$model."\" ";
		if (@$control['subtype'] == $model)
			echo " checked";
				echo " onclick=\"TheSearchForm.submit()()\">
	  ".$model." (".$type[FastrackHelper::createSafeKey($m)][FastrackHelper::createSafeKey($model)]['count'].")</li>\n";
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
		<li><input type="radio" name="control[make]" value="All Makes" onclick="TheSearchForm.submit()">
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
      			<input type="radio" name="control[make]" value="<?php echo $m; ?>"<?php
		if (@$control['make'] == $m)
			echo " checked";
		?> onclick="TheSearchForm.submit()()">
      <?php echo $m; ?> (<?php echo $make[FastrackHelper::createSafeKey($m)]['count']; ?>)
      <?php
		if (@$control['make'] == $m) {
			$displaysubtype = true;
			?> <ul>
            <?php
		} else {
			?> </li>
            <?php
		}
	} 
	if (@$control['make'] == $m) {
		?> <li><input type="radio" name="control[model]" value="<?php echo $model; ?>" <?php
		if (@$control['model'] == $model)
			echo " checked";
		?> onclick="TheSearchForm.submit()()" />
	  <?php echo $model; ?> (<?php echo $make[FastrackHelper::createSafeKey($m)][FastrackHelper::createSafeKey($model)]['count']; ?>)</li>
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
<?php endif ?>
<input type="hidden" value="<?php echo @$control['make']; ?>" name="control[OldMake]">
<input type="hidden" value="<?php echo @$control['type']; ?>" name="control[OldType]">
<?php echo ModFastrackPreparation::hiddenPagin(); ?>
 </form>
</div>
<?php
printAnObject($display, true);
/*$display->setTemplate($template);
$items->setAttribute('warning', $warning);
$items->setAttribute('TotalAvailable', $TotalAvailable);
$item->addToAttribute('miscellaneous', "<p><b>".$n.":</b> ".$w[$n]."</p>\n");  */
