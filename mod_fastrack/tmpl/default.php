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
 * along with Fastrack.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		Fastrack
 * @author		Hill Range Services http://fastrack.hillrange.com.au
 * @copyright	Copyright (C) 2014  Hill Range Services  All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @version 	12th July 2016
 * @since 		14th February 2015
 */

defined('_JEXEC') or die();

JLoader::import('components.com_fastrack.libraries.display', JPATH_ADMINISTRATOR);
JLoader::import('components.com_fastrack.libraries.mustache.src.Mustache.Autoloader', JPATH_ADMINISTRATOR);
Mustache_Autoloader::register();
JLoader::import('modules.mod_fastrack_search.search', JPATH_SITE);

$css = file_get_contents(JPATH_SITE.'/modules/mod_fastrack/tmpl/default.css');
$css = $params->get('css_contents', $css);?>

<form name="TheForm" id="TheForm" method="post"><?php


$doc = JFactory::getDocument();
$doc->addStyleDeclaration($css);

$input = JFactory::getApplication()->input;
$xx = FastrackHelper::getCondition('xx');
$count = FastrackHelper::getCondition('count');
$warning = FastrackHelper::getCondition('warning');
$TotalAvailable = FastrackHelper::getCondition('TotalAvailable');
$order = FastrackHelper::getCondition('order');
$total = FastrackHelper::getCondition('total');
$fileNames = FastrackHelper::getCondition('FileNames');
$ftfile = FastrackHelper::getCondition('ftfile');

$doc->addStyleDeclaration($params->get('css_contents', $css));

$pagination = FastrackHelper::buildPagination($xx, 'submit', $params);

echo $pagination;

$pagin = FastrackHelper::getCondition('pagin');
$control = FastrackHelper::getCondition('control');

$ShowCount = $params->get('pageitems', 10);
if ($count < $params->get('pageitems', 10))
	$ShowCount = $count;

$items = array();
$items['warning'] = $warning;
$items['TotalAvailable'] = $TotalAvailable;
$items['count'] = $count;
$plural = '';
if ($count> 1)
	$plural = 's';
$items['plural'] = $plural;
$items['pageitems'] = $params->get('pageitems', 10);

$DisplayCount = 0;
$DisplayNow = false;

foreach ($xx as $q=>$w) {
	$ftfile = $fileNames[$w['fileid']];
	if ($w['id'] == $pagin['startKey'])
		$DisplayNow = true;
	if ($DisplayNow) {
		$item = array();
		$xx[$q] = FastrackHelper::imageCreator($xx[$q], $ftfile);
		unset($image);
		$item['enquiry'] = str_replace('productID={{id}}', '', $ftfile->enquiryURL);
		if (strpos($item['enquiry'], '?') !== false)
			$item['enquiry'] .= '&fileName='.$ftfile->name;
		else
			$item['enquiry'] .= '?fileName='.$ftfile->name;
		if (strpos($item['enquiry'], '?') !== false)
			$item['enquiry'] .= '&productID='.$w['id'];
		else
			$item['enquiry'] .= '?productID='.$w['id'];
		$FileID = fopen($ftfile->resultPath.$w['id'].".txt", "w");   
		foreach ($order as $n){
			switch ($n) {
				case "type";
					$item['type'] = $w[$n];
					fwrite($FileID, $n.":=".$w[$n]." - ".$w['subtype']."\n");
					break;
				case "price";
					if (! isset (  $w['price']['currency'] ) )
						$w['price']['currency'] = 'AUD';
					$w['price']['cost'] = number_format($w['price']['value'], 2, '.', ',');
					$w['price']['gst'] =$w['price']['gst_value'];
					$item['price'] = $w['price'];
					fwrite($FileID, "cost:=".$w['price']['value']."\n");
					fwrite($FileID, "gst:=".$w['price']['gst_value']."\n");
					fwrite($FileID, "currency:=".$w['price']['currency']."\n");
					break;
				case "subtype":
					$item[$n] =  $w[$n];
					break;
				case "make";
					$item[$n] =  $w[$n]; 
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "model";
					$item[$n] =  $w[$n]; 
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "configuration";
				case "config";
					if (isset($w[$n])) {
						fwrite($FileID, $w[$n]['name'].":=".$w[$n]['value']."\n");
						$item[$n] =  array('name'=>$w[$n]['name'],'value'=>$w[$n]['value']);
					}
					break;
				case "listingtype";
					if (isset($w[$n])) {
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "condition";
					if (isset($w[$n])){
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "year";
					if (isset($w[$n])) {
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "hours";
					if (isset($w[$n])){
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "stockref";
					if (isset($w[$n])) {
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "engpower";
					if (isset($w[$n])) {
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "status";
					if (isset($w[$n]))
						$item[$n] =  $w[$n]['value']; 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					break;
				case "id";
					if (isset($w[$n])) {
						$item[$n] =  $w[$n]; 
						fwrite($FileID, $n.":=".$w[$n]."\n");
					}
					break;
				case "description";
					if (isset($w[$n])) {
						$desc = str_replace (array(", , , ,", ", , ,", ", ,"), ",", str_replace(array("\r\n", "\n\r", "\n", "<br>", "<br />"), ", ", html_entity_decode($w[$n]['value']))); 
						$item[$n] =  $desc; 
						fwrite($FileID, $n.":=".$desc."\n");
					}
					break;
				case 'measurements':
				case 'text':
				case 'option':
					if (isset($w[$n])) {
						$item[$n] = $w[$n]; 
						fwrite($FileID, $n.":=".$w[$n]."\n");
					}
					break;
				default:
					if (isset($w[$n])) {
						$item['miscellaneous'][] = "<p><b>".$n.":</b> ".$w[$n]."</p>\n"; 
						fwrite($FileID, $n.':+'.$w[$n]."\n");
					}
			}
		}
		fclose($FileID);
		if (empty($xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';
		if (! is_file($ftfile->path.$xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';

		$imageURL = rtrim($ftfile->imageURL, '/').'/';
		$item['firstimage'] = $imageURL . '/' . $xx[$q]['image'][1];
		$images = array();
		foreach ($xx[$q]['image'] as $c=>$i) {
			$iv = getimagesize($ftfile->path . $i);
			$s = $iv[1]/245;
			$h = 245;
			$w = intval($iv[0]/$s);
			$s = $iv[1]/75;
			$th = 75;
			$tw = intval($iv[0]/$s);
			$stuff = "<a class=\"thumb\" href=\"#\"><img src=\"".$imageURL.$i."\" alt=\"\" width=\"".$tw."\" height=\"".$th."\">
			<span style=\"width: ".strval($w + 4)."px; height: ".strval($h+4)."px; \"><img src=\"". $imageURL.$i . "\" alt=\"\" width=\"".$w."\" height=\"".$h."\"></span></a>";
			$stuff = array();
			$stuff['imageURL'] = $imageURL.$i;
			$stuff['thumbwidth'] = $tw;
			$stuff['width'] = $w;
			$stuff['width4'] = $w + 4;
			$stuff['thumbheight'] = $th;
			$stuff['height'] = $h;
			$stuff['height4'] = $h + 4;
			$images[] = $stuff;
		}
		$ti['images'] = $images;
		$item['thumbimages'] = $ti;		
		$items['items'][] = $item;
		$DisplayCount++;
		if ($DisplayCount > $params->get('pageitems', 10) - 1)
			break;
	}
}

$items['search']  = ModFastrackSearch::hiddenSearch();
$m = new Mustache_Engine ;
$template = file_get_contents(JPATH_SITE.'/modules/mod_fastrack/tmpl/default.html');
echo $m->render($params->get('content', $template), $items); ?>

<div id="StartFooter">&nbsp;</div>

<?php echo $pagination; ?>

</form>
<div style="clear:both;">&nbsp;</div>
<?php
foreach($fileNames as $ftfile) {
	# Now remove old image files from created stack.
	if (! is_dir($ftfile->resultPath)) {
		FastrackHelper::buildPath($ftfile->resultPath);
		if (! is_dir($ftfile->resultPath))
			JError::raiseWarning('42', JText::_('COM_FASTRACK_ERROR_RESULTPATH'));
	}
	$path = rtrim($ftfile->resultPath, '/').'/';
	$images = dir($path);
	
	while (false !== ($entry = $images->read())) {
		if (is_file($path.$entry)) {
			if (in_array(pathinfo($path.$entry, PATHINFO_EXTENSION), array('txt', 'jpg')))
				if (date("Y-m-d", filemtime($path.$entry)) < date('Y-m-d', strtotime('-30 days')))
					unlink($path.$entry);
		}
	}
}
