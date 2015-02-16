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
 * @version 14th February 2015
 * @since 14th February 2015
 */

defined('_JEXEC') or die();

JLoader::import('modules.mod_fastrack.display', JPATH_SITE);

$css = <<<CSS
/* CSS Document */

#usedmachinery {
	margin: 0;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #666666;
	text-align: left;
}

#usedmachinery .tb_paginate{
border-width: 1px;
border-style: solid;
}

#usedmachinery .paginate_1{
	background: #EEEEEE;
}                                 

#usedmachinery .paginate_0, #usedmachinery .paginate_1 {
	border-width: 1px;
	border-style: solid;
}                     

#usedmachinery .paginate_0 a, #usedmachinery .paginate_1 a {
	text-decoration: none;
	color: #333333;
	font-size: 80%;
}

#usedmachinery .paginate_0 img, #usedmachinery .paginate_1 img {
	border: none;
}
.highlight-box {
	float: right; 
	text-align: center;
	border-radius: 15px;
	/* border: 1px #CCCCCC solid; */
	background: #DEDEDE;
}
.highlight-box img {
	padding: 5px;
	border-radius: 15px;
}
.titleright h2{
	text-align: right;
}
.rt-grid-3a {
	width: 250px;
}
.gf-menu .item {
    padding: 0 8px;
    }
.item-page h3 {
	color: #7E7E7E;
	font-family: 'SourceSansProRegular',Helvetica,Arial,sans-serif;
	font-size: 1.1em;
	font-weight: normal;
}


/* Used Products CSS added August 2014 */
.SaleItems {
}
.SaleItem {
	margin-top: -1px 2px 2px 2px;
	padding: 0px;
	clear: both ;
}
.Specifications {
	float: left;
    margin: 5px;
	padding: 5px;
	max-width: 55%;
	color: #7e7e7e;
	text-transform: uppercase;
/*	width: 55%; */}
.SaleItemHeader {
	margin: 3px;
    padding-bottom: 8px;
    padding-left: 17px;
    padding-right: 20px;
    padding-top: 14px;
	position: relative;
	background: #1E772B;
	border-radius: 20px;
	color: #ffffff;
	text-transform: uppercase;
}
.SpecHeading {
color: green;
text-transform: uppercase;
   font-size: 130%;
}
.SaleItemHeader li {
display: inline-block;
}
li.PriceTitle  {
float:right;
}
.PriceTitle input {
margin-top: -5px;
}
.MainTitle,  .PriceTitle {
    font-size: 150%;
	font-weight: bold;
}
.audgst {
	font-size: 53%;
}
.SaleItemHeader h3 {
	position: absolute;
	right: 5px;
	width: 30%;
	text-align: right;
	line-height: 35px;
	vertical-align: center;
}
.SaleItem p {
	padding: 1px 0 3px 0;
	margin: 0;

}
#firstImage {
	width: 245px;
	float: none;
	border-radius: 20px;
}
.SaleThumbs {
	clear: both;
/*	width: 95%;  */
}
.thumb {
	float: left; /* must be floated for same cross browser position of larger image */
	position: relative;
	margin: 3px;
}
.thumb img { 
	border: 1px solid #DEDEDE;
	vertical-align: bottom;
	height: 70px ;
	border-radius: 12px;
}
.thumb:hover {
	border: 0; /* IE6 needs this to show large image */
	z-index: 1;
}
.thumb span { 
	position: absolute;
	visibility: hidden;
}
.thumb:hover span { 
	visibility: visible;
	top: -200px; 
	left: 25px; 
}
.thumb:hover span img { 
	height: 245px;	
}
.UsedProductManagement {
}
.Pagination {
	background-color: transparent;
	color: ButtonText; 
	border: 1px solid ButtonFace;
	border-radius: 4px;
}
.PaginationChecked {
	background-color: ButtonFace; 
	color: ButtonText; 
	border: 1px solid ButtonFace;
	border-radius: 4px;
}
div#StartFooter {
	width: 100%;
}
CSS;

?>
<div class="UsedProductManagement">
<form name="TheForm" id="TheForm" method="post">
<!--<p>Vanderfield are currently listing a total of <?php echo $total; ?> used products for sale.</p>-->
<?php
$doc = JFactory::getDocument();
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


$ShowCount = $params->get('pageitems', 10);
if ($count < $params->get('pageitems', 10))
	$ShowCount = $count;

$itemsTemplate = <<<ppp
<div id="SaleItems">
{{warning}}
<p>Vanderfield currently has {{TotalAvailable}} used products in the catalogue.  Your search revealed {{count}} product{{plural}}, displayed {{pageitems}} products to a page.</p>
{{items}}
</div>
ppp;

$items = new FastrackDisplay();
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
			<input class="EnqButton" type="button" value="&nbsp;Send Enquiry&nbsp;" onclick="window.open('{{enquiry}}', '_self')" />
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
        {{#thumbimages}}<div class="SaleThumbs">
            {{thumbimages}}
        </div> <!-- End of SaleThumbs -->{{/thumbimages}}
	</div> <!-- End of FirstImageHolder -->
</div> <!-- End of SaleItem -->
ooo;















$DisplayCount = 0;
$DisplayNow = false;

foreach ($xx as $q=>$w) {
	$ftfile = $fileNames[$w['fileid']];
	if ($w['id'] == $pagin['startKey'])
		$DisplayNow = true;
	if ($DisplayNow) {
		$item = new FastrackDisplay();
		$item->setTemplate($params->get('item_content', $itemTemplate));
		$xx[$q] = FastrackHelper::imageCreator($xx[$q], $ftfile);
		unset($image);
		$item->setAttribute('enquiry', str_replace('{{id}}', $w['id'], $ftfile->enquiryURL));
		$FileID = fopen($ftfile->resultPath.$w['id'].".txt", "w");   
		foreach ($order as $n){
			switch ($n) {
				case "type";
					$item->setAttribute('type', $w[$n]);
					fwrite($FileID, $n.":=".$w[$n]." - ".$w['subtype']."\n");
					break;
				case "price";
					if (! isset (  $w['price']['currency'] ) )
						$w['price']['currency'] = 'AUD';
					$item->setAttribute('currency', $w['price']['currency']); 
					$item->setAttribute('cost', '$'.number_format($w['price']['value'], 2, '.', ',')); 
					$item->setAttribute('gst', $w['price']['gst_value'], 10); 
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
		if (! is_file($ftfile->path.$xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';

		$imageURL = rtrim($ftfile->imageURL, '/').'/';
		$item->setAttribute('firstimage', $imageURL . '/' . $xx[$q]['image'][1]);
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

foreach($fileNames as $ftfile) {
	# Now remove old image files from created stack.
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
?>
