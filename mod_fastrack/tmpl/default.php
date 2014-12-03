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
 * @version 27th November 2014
 */

defined('_JEXEC') or die();

JLoader::import('modules.mod_fastrack.display', JPATH_SITE);

?>
<div class="UsedProductManagement">
<form name="TheForm" id="TheForm" method="post">
<!--<p>Vanderfield are currently listing a total of <?php echo $total; ?> used products for sale.</p>-->
<?php
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_fastrack/tmpl/default.css');

$pagination = ModFastrackHelper::buildPagination($xx, 'submit');
echo $pagination;

$ShowCount = $input->get('pageitems', 10);
if ($count < $input->get('pageitems', 10))
	$ShowCount = $count;

$itemsTemplate = <<<ppp
<div id="SaleItems">
{{warning}}
<p>Vanderfield currently has {{TotalAvailable}} used products in the catalogue.  Your search revealed {{count}} product{{plural}}, displayed {{pageitems}} products to a page.</p>
{{items}}
</div>
ppp;

$items = new modFastrackDisplay();
$items->setTemplate($params->get('items_content', $itemsTemplate));
$items->setAttribute('warning', $warning);
$items->setAttribute('TotalAvailable', $TotalAvailable);
$items->setAttribute('count', $count);
$plural = '';
if ($count> 1)
	$plural = 's';
$items->setAttribute('plural', $plural);
$items->setAttribute('pageitems', $params->get('pageitems', 10));




$itemTemplate = <<<ooo
<div class="SaleItem">
	<div class="SaleItemHeader">
		<li class="MainTitle">{{make}} - {{model}}</li>
		<li class="PriceTitle">
			<span class="audgst">{{currency}}</span> {{cost}} <span class="audgst">{{gst}}</span>&nbsp;&nbsp;&nbsp;&nbsp;
			{{#enquiry}}<input class="EnqButton" type="button" value="&nbsp;Send Enquiry&nbsp;" onclick="window.open('{{enquiry}}', '_self')" />{{/enquiry}}
		</li>
		<div style="clear:both;"></div>
	</div> <!-- End of SaleItemHeader -->
	<div class="Specifications">
		<p class="SpecHeading"><b>Type:</b> {{type}} - {{subtype}}</p>
		<p class="SpecHeading"><b>Make:</b> {{make}}</p>
		<p class="SpecHeading"><b>Model:</b> {{model}}</p>
		<p><b>{{config_name}}:</b> {{config_value}}</p>
		{{#listingtype}}<p><b>Listing Type:</b> {{listingtype}}</p>{{/listingtype}}
		{{#condition}}<p><b>Condition:</b> {{condition}}</p>{{/condition}}
		{{#year}}<p><b>Year:</b> {{year}}</p>{{/year}}
		{{#hours}}<p><b>Hours:</b> {{hours}}</p>{{/hours}}
		{{#stockref}}<p><b>Stock Ref #:</b> {{stockref}}</p>{{/stockref}}
		{{#engpower}}<p><b>Eng. Power:</b> {{engpower}}</p>{{/engpower}}
		{{#status}}<p><b>Status:</b> {{status}}</p>{{/status}}
		<!-- <p><b>ID:</b> {{id}}</p> -->
		{{#description}}<p><b>Description:</b> {{description}}</p>{{/description}}
		{{#miscellaneous}}{{miscellaneous}}{{/miscellaneous}}
	</div> <!-- End of Specifications -->
	<div id="FirstImageHolder">
		<p><img id="firstImage" src='{{firstimage}}' alt='' width="245" /></p>
	</div> <!-- End of FirstImageHolder -->
	{{#thumbimages}}<div class="SaleThumbs">
		{{thumbimages}}
	</div> <!-- End of SaleThumbs -->{{/thumbimages}}
</div> <!-- End of SaleItem -->
ooo;















$DisplayCount = 0;
$DisplayNow = false;

foreach ($xx as $q=>$w) {
	if ($w['id'] == $_POST['startKey'])
		$DisplayNow = true;
	if ($DisplayNow) {
		$item = new modFastrackDisplay();
		$item->setTemplate($params->get('item_content', $itemTemplate));
		$xx[$q] = ModFastrackHelper::imageCreator($xx[$q]);
		unset($image);
		$item->setAttribute('enquiry', str_replace('{{id}}', $w['id'], $params->get('enquiry_url', '')));
		$FileID = fopen(IMAGE_PATH_ABS.'store/'.$w['id'].".txt", "w");   
		foreach ($order as $n){
			switch ($n) {
				case "type";
					$item->setAttribute('type', $w[$n]);
					fwrite($FileID, $n.":=".$w[$n]." - ".$w['subtype']."\n");
					break;
				case "price";
					$item->setAttribute('currency', $w['price']['currency']); 
					$item->setAttribute('cost', '$'.number_format($w['price']['value'], 2, '.', ',')); 
					$item->setAttribute('gst', $w['price']['gst_value']); 
					fwrite($FileID, "cost:=".$w['price']['value']."\n");
					fwrite($FileID, "gst:=".$w['price']['gst_value']."\n");
					fwrite($FileID, "currency:=".$w['price']['currency']."\n");
					break;
				case "subtype":
					$item->setAttribute($n, $w[$n]);
					break;
				case "make";
					$item->setAttribute($n, $w[$n]); 
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "model";
					$item->setAttribute($n, $w[$n]); 
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "config";
					if (isset($w[$n])) {
						fwrite($FileID, $w[$n]['name'].":=".$w[$n]['value']."\n");
						$item->setAttribute($n.'_name', $w[$n]['name']); 
						$item->setAttribute($n.'_value', $w[$n]['value']); 
					}
					break;
				case "listingtype";
					if (isset($w[$n])) {
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "condition";
					if (isset($w[$n])){
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "year";
					if (isset($w[$n])) {
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "hours";
					if (isset($w[$n])){
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "stockref";
					if (isset($w[$n])) {
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "engpower";
					if (isset($w[$n])) {
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "status";
					if (isset($w[$n]))
						$item->setAttribute($n, $w[$n]['value']); 
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					break;
				case "id";
					if (isset($w[$n])) {
						$item->setAttribute($n, $w[$n]); 
						fwrite($FileID, $n.":=".$w[$n]."\n");
					}
					break;
				case "description";
					if (isset($w[$n])) {
						$item->setAttribute($n, str_replace (array(", , , ,", ", , ,", ", ,"), ",", str_replace(array("\r\n", "\n\r", "\n", "<br>", "<br />"), ", ", $w[$n]['value']))); 
						fwrite($FileID, $n.":=".str_replace (array(", , , ,", ", , ,", ", ,"), ",", str_replace(array("\r\n", "\n\r", "\n", "<br>", "<br />"), ", ", $w[$n]['value']))."\n");
					}
					break;
				default:
					$item->addToAttribute('miscellaneous', "<p><b>".$n.":</b> ".$w[$n]."</p>\n"); 
					fwrite($FileID, $n.':+'.$w[$n]."\n");
			}
		}
		fclose($FileID);
		if (empty($xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';
		if (! is_file(IMAGE_PATH_ABS.$xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';
		$item->setAttribute('firstimage', JURI::base().IMAGE_PATH_REL.'store/'.$xx[$q]['image'][1]);

		foreach ($xx[$q]['image'] as $c=>$i) {
			$iv = getimagesize(IMAGE_PATH_ABS.$i);
			$s = $iv[1]/245;
			$h = 245;
			$w = intval($iv[0]/$s);
			$s = $iv[1]/75;
			$th = 75;
			$tw = intval($iv[0]/$s);
			$stuff = "<a class=\"thumb\" href=\"#\"><img src=\"".JURI::base().IMAGE_PATH_REL.$i."\" alt=\"\" width=\"".$tw."\" height=\"".$th."\">
			<span style=\"width: ".strval($w + 4)."px; height: ".strval($h+4)."px; \"><img src=\"". JURI::base().IMAGE_PATH_REL.$i . "\" alt=\"\" width=\"".$w."\" height=\"".$h."\"></span></a>";
			$item->addToAttribute('thumbimages', $stuff);
		}
		
		$items->addToAttribute('items', $item->render());
		$DisplayCount++;
		if ($DisplayCount > $params->get('pageitems', 10) - 1)
			break;
	}
}
?>

<?php echo $items->render(); ?>


<div id="StartFooter">&nbsp;</div>

<?php echo $pagination; ?>
</form>

<?php


# Now remove old image files from created stack.
$path = IMAGE_PATH_ABS.'store/';
$images = dir($path);

while (false !== ($entry = $images->read())) {
	if (is_file($path.$entry)) {
		if (in_array(pathinfo($path.$entry, PATHINFO_EXTENSION), array('txt', 'jpg')))
			if (date("Y-m-d", filemtime($path.$entry)) < date('Y-m-d', strtotime('-30 days')))
				unlink($path.$entry);
	}
}?>
