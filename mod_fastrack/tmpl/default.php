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
echo $items->render();
?>

<div class="SaleItems">
<?php echo $warning; ?>

<p>Vanderfield currently has <?php echo $TotalAvailable; ?> used products in the catalogue.  Your search revealed <?php echo $count; ?> product<?php if ($count > 1) echo 's'; ?>, displayed <?php echo $params->get('pageitems', 10); ?> products to a page.</p>
<?php
$DisplayCount = 0;
$DisplayNow = false;

foreach ($xx as $q=>$w) {
	if ($w['id'] == $_POST['startKey'])
		$DisplayNow = true;
	if ($DisplayNow) {
		$ok = true;
		unset($image);
		$xx[$q]['image'] = array();
		$count = 0;
		do {
			$count++;
			if (is_file(IMAGE_PATH_ABS.IMAGE_NAME.'_'.$w['id'].'_'.strval($count).'.jpg')) {
				$xx[$q]['image'][$count] = IMAGE_NAME.'_'.$w['id'].'_'.strval($count).'.jpg';
				if (! is_file(IMAGE_PATH_ABS.'store/'.IMAGE_NAME.'_'.$w['id'].'_'.strval($count).'.jpg')) {
					if (false !== ($im = @getimagesize(IMAGE_PATH_ABS.$xx[$q]['image'][$count]))) {
						$height = 245;
						$y = $im[1]/$height;
						$width = intval($im[0]/$y);
						$thumb = imagecreatetruecolor($width, $height);
						$source = imagecreatefromjpeg(IMAGE_PATH_ABS.$xx[$q]['image'][$count]);
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $im[0], $im[1]);
						imagejpeg($thumb, IMAGE_PATH_ABS.'store/'.IMAGE_NAME.'_'.$w['id'].'_'.strval($count).'.jpg');
						imagedestroy($source);
						imagedestroy($thumb);
					} else {
						unset($xx[$q]['image'][$count]);
						$ok = false;
					}
				}
			} else {
				$ok = false;
			}
		} while ($ok);

		$FileID = fopen(IMAGE_PATH_ABS.'store/'.$w['id'].".txt", "w");    
		?><div class='SaleItem'><br />
		<div class="SaleItemHeader">
		<li class="MainTitle"><?php echo $w['make']; ?> - <?php echo $w['model']; ?></li>
		<li class="PriceTitle"><span class="audgst"><?php echo $w['price']['currency']; ?></span> $<?php echo number_format($w['price']['value'], 2, ".", ","); ?> <span class="audgst"><?php echo $w['price']['gst_value']; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
		<input class="EnqButton" type="button" value="&nbsp;Send Enquiry&nbsp;" onclick="window.open('index.php?option=com_rsform&formId=8&productID=<?php echo $xx[$q]['id']; ?>', '_self')" /></li>
		<div style="clear:both;"></div>
		</div>
		<div class="Specifications">
		<?php
		foreach ($order as $n){
			switch ($n) {
				case "type";
					echo "<p class=\"SpecHeading\"><b>Type:</b> ".$w[$n]." - ".$w['subtype']."</p>\n";
					fwrite($FileID, $n.":=".$w[$n]." - ".$w['subtype']."\n");
					break;
				case "price";
					fwrite($FileID, "cost:=".$w['price']['value']."\n");
					fwrite($FileID, "gst:=".$w['price']['gst_value']."\n");
					fwrite($FileID, "currency:=".$w['price']['currency']."\n");
					break;
				case "subtype":
					break;
				case "make";
					echo "<p class=\"SpecHeading\"><b>Make:</b> ".$w[$n]."</p>\n";
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "model";
					echo "<p class=\"SpecHeading\"><b>Model:</b> ".$w[$n]."</p>\n";
					fwrite($FileID, $n.":=".$w[$n]."\n");
					break;
				case "config";
					if (isset($w[$n])) {
						fwrite($FileID, $w[$n]['name'].":=".$w[$n]['value']."\n");
						echo "<p><b>".$w[$n]['name'].":</b> ".$w[$n]['value']."</p>\n";
					}
					break;
				case "listingtype";
					if (isset($w[$n])) {
						echo "<p><b>Listing Type:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "condition";
					if (isset($w[$n])){
						echo "<p><b>Condition:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "year";
					if (isset($w[$n])) {
						echo "<p><b>Year:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "hours";
					if (isset($w[$n])){
						echo "<p><b>Hours:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "stockref";
					if (isset($w[$n])) {
						echo "<p><b>Stock Ref #:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "engpower";
					if (isset($w[$n])) {
						echo "<p><b>Eng. Power:</b> ".$w[$n]['value']."</p>\n";
						fwrite($FileID, $n.":=".$w[$n]['value']."\n");
					}
					break;
				case "status";
					if (isset($w[$n]))
						echo "<p><b>Status:</b> ".$w[$n]['value']."</p>\n";
					break;
				case "id";
					if (isset($w[$n])) {
						echo "<!-- <p><b>ID:</b> ".$w[$n]."</p>\n -->\n";
						fwrite($FileID, $n.":=".$w[$n]."\n");
					}
					break;
				case "description";
					if (isset($w[$n])) {
						echo "<p><b>Description:</b> ".str_replace (array(", , , ,", ", , ,", ", ,"), ",", str_replace(array("\r\n", "\n\r", "\n", "<br>", "<br />"), ", ", $w[$n]['value']))."</p>\n";
						fwrite($FileID, $n.":=".str_replace (array(", , , ,", ", , ,", ", ,"), ",", str_replace(array("\r\n", "\n\r", "\n", "<br>", "<br />"), ", ", $w[$n]['value']))."\n");
					}
					break;
				default:
					echo "<p><b>".$n.":</b> ".$w[$n]."</p>\n";
					fwrite($FileID, $n.':+'.$w[$n]."\n");
			}
		}
		fclose($FileID);
		if (empty($xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';
		if (! is_file(IMAGE_PATH_ABS.$xx[$q]['image'][1]))
			$xx[$q]['image'][1] = 'PlaceHolder.png';
		?>
        </div> <!-- End of Specifications -->
		
		<!--<div style="text-align: center; float: left; width: 250px; ">-->
		<div>
        <p>
        <img class="firstImage" src='<?php echo JURI::base().IMAGE_PATH_REL.'store/'.$xx[$q]['image'][1]; ?>' alt='' width="245" />
        </p>
       

		<!--<div class="SaleThumbs">-->
        <?php
		foreach ($xx[$q]['image'] as $c=>$i) {
			$iv = getimagesize(IMAGE_PATH_ABS.$i);
			$s = $iv[1]/245;
			$h = 245;
			$w = intval($iv[0]/$s);
			$s = $iv[1]/75;
			$th = 75;
			$tw = intval($iv[0]/$s);
			?>
			<a class="thumb" href="#"><img src="<?php echo JURI::base().IMAGE_PATH_REL.$i; ?>" alt="" width="<?php echo $tw; ?>" height="<?php echo $th; ?>">
			<span style="width: <?php echo strval($w + 4); ?>px; height: <?php echo strval($h+4); ?>px; "><img src="<?php echo JURI::base().IMAGE_PATH_REL.$i; ?>" alt="" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></span></a>
			<?php
		}
		
		echo "</div> <!-- End of SaleThumbs -->\n";
	
		echo"</div> <!-- End of Sale Item -->\n" ;
		$DisplayCount++;
		if ($DisplayCount > $params->get('pageitems', 10) - 1)
			break;
	}
}
?>
</div>    <!-- End of SaleItems -->




</div>   <!-- End of ProductMenu -->
<div id="StartFooter">&nbsp;</div>




   <!-- End of UsedProductManagement -->
<?php echo $pagination; ?>
</form>

<?php


# Now remove old image files from  created stack.
$path = IMAGE_PATH_ABS.'store/';
$images = dir($path);

while (false !== ($entry = $images->read())) {
	if (is_file($path.$entry)) {
		if (in_array(pathinfo($path.$entry, PATHINFO_EXTENSION), array('txt', 'jpg')))
			if (date("Y-m-d", filemtime($path.$entry)) < date('Y-m-d', strtotime('-30 days')))
				unlink($path.$entry);
	}
}?>
