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

?>
<div class="UsedProductManagement">
<form name="TheForm" id="TheForm" method="post">
<!--<p>Vanderfield are currently listing a total of <?php echo $total; ?> used products for sale.</p>-->
<?php
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_fastrack/tmpl/default.css');
$yy = array();
$DisplayCount = 0;
$startid = 0;
$PageCount = 0;
foreach($xx as $q=>$w){
	if ($DisplayCount == 0) {
		$StartID = $w['id'];
	}
	$DisplayCount++;
	if ($DisplayCount > PAGEITEMS - 1) {

		$PageCount++;
		$yy[$PageCount] = $StartID;
		$DisplayCount = 0;
	}
}
if ($DisplayCount > 0) {
	$PageCount++;
		$yy[$PageCount] = $StartID;
}
$t = '';
$m = '';

if ($_POST['startKey'] == 0)
	foreach($xx as $q=>$w) {
		$_POST['startKey'] = $w['id'];
		break ;
	}

$pagination = '';
ob_start();
?>
<div style="text-align: center; clear:both">
<p>
<input type="hidden" value="<?php echo $_POST['startKey']; ?>" name="oldStartKey" />
<?php

reset($yy);
$first = key($yy);
end($yy);
$last = key($yy);

foreach ($yy as $q=>$w) {
	if ($q == $first) {
		?><input type="submit" name="startKey" value="<?php echo FIRSTPAGE; ?>" class="Pagination" /> 
		<input type="submit" name="startKey" value="<?php echo PREVPAGE; ?>" class="Pagination" />
    
		<?php	
	}
	?>
    <input type="submit" name="startKey" value="<?php echo $q; ?>"  <?php 
		if (@$_POST['startKey'] == $w) {
			?> class="Pagination, PaginationChecked " <?php
		} else { ?>
         class="Pagination"
        <?php }
	?> />
    <input type="hidden" name="startKeyValues[<?php echo $q; ?>]" value="<?php echo $w; ?>" />
    <?php	
	if ($q == $last) {
		?><input type="submit" name="startKey" value="<?php echo NEXTPAGE; ?>" class="Pagination" /> 
		<input type="submit" name="startKey" value="<?php echo LASTPAGE; ?>" class="Pagination" /><?php	
	}
}
?></p>

</div>
<?php
$pagination = ob_get_contents();
ob_end_clean();
if (count($yy) < 2)
	$pagination = '';
echo $pagination;
?>



<?php
$ShowCount = PAGEITEMS;
if ($count < PAGEITEMS)
	$ShowCount = $count;
?>

<div class="SaleItems">
<?php echo $warning; ?>
<p>Vanderfield currently has <?php echo $TotalAvailable; ?> used products in the catalogue.  Your search revealed <?php echo $count; ?> product<?php if ($count > 1) echo 's'; ?>, displayed <?php echo PAGEITEMS; ?> products to a page.</p>
<?php
$DisplayCount = 0;
$DisplayNow = false;
if (! in_array($_POST['startKey'], $yy)) {
	$_POST['startKey'] = $yy[1];
}
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
			if (is_file(PRODUCTIMAGES.'toowoomba_'.$w['id'].'_'.strval($count).'.jpg')) {
				$xx[$q]['image'][$count] = PRODUCTIMAGES.'toowoomba_'.$w['id'].'_'.strval($count).'.jpg';
				if (empty($image))
					$image = PRODUCTIMAGES.'toowoomba_'.$w['id'].'_'.strval($count).'.jpg';
				if (! is_file(PRODUCTIMAGES.'store/toowoomba_'.$w['id'].'_'.strval($count).'.jpg')) {
					if (false !== ($im = @getimagesize($xx[$q]['image'][$count]))) {
						$height = 245;
						$y = $im[1]/$height;
						$width = intval($im[0]/$y);
						$thumb = imagecreatetruecolor($width, $height);
						$source = imagecreatefromjpeg($xx[$q]['image'][$count]);
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $im[0], $im[1]);
						imagejpeg($thumb, PRODUCTIMAGES.'store/toowoomba_'.$w['id'].'_'.strval($count).'.jpg');
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
	
	
		$FileID = fopen(PRODUCTIMAGES.'store/'.$w['id'].".txt", "w");    
		if ($w['price']['gst_value'] == 'ex-GST') {
			$w['price']['value'] += $w['price']['value'] * $w['price']['gst'] / 100;
			$w['price']['gst_value'] = 'inc-GST';
		}
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
		if (! is_file($xx[$q]['image'][1]))
			$xx[$q]['image'][1] = PRODUCTIMAGES.'PlaceHolder.png'
		?>
        </div> <!-- End of Specifications -->
		
		<!--<div style="text-align: center; float: left; width: 250px; ">-->
		<div>
        <!--<p style="text-align: center ">--><p><!--<a href="index.php?option=com_rsform&formId=8&productID=<?php echo $xx[$q]['id']; ?>" target="_self">-->
        <img class="firstImage" src='<?php echo str_replace(PRODUCTIMAGES, PRODUCTIMAGES.'store/', $xx[$q]['image'][1]); ?>' alt='' width="245" /><!--</a>-->
        </p>
       

		<!--<div class="SaleThumbs">-->
        <?php
		foreach ($xx[$q]['image'] as $c=>$i) {
			$iv = getimagesize($i);
			$s = $iv[1]/245;
			$h = 245;
			$w = intval($iv[0]/$s);
			$s = $iv[1]/75;
			$th = 75;
			$tw = intval($iv[0]/$s);
			?>
			<a class="thumb" href="#"><img src="<?php echo str_replace(PRODUCTIMAGES, PRODUCTIMAGES.'store/', $i); ?>" alt="" width="<?php echo $tw; ?>" height="<?php echo $th; ?>">
			<span style="width: <?php echo strval($w + 4); ?>px; height: <?php echo strval($h+4); ?>px; "><img src="<?php echo str_replace(PRODUCTIMAGES, PRODUCTIMAGES.'store/', $i); ?>" alt="" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></span></a>
			<?php
		}
		
		echo "</div> <!-- End of SaleThumbs -->\n";
	
		echo"</div> <!-- End of Sale Item -->\n" ;
		$DisplayCount++;
		if ($DisplayCount > PAGEITEMS - 1)
			break;
	}
}
?>
</div>    <!-- End of SaleItems -->



<div id="ProductMenu">
<p style="margin-bottom: 5px;">Keyword Search: Separate by commas:</p>
<div>
<input type="text" name="keywords" size="20" maxlength="75" />&nbsp;
<input class="searchbutton" type="submit" value="Search Now" name="Search"  /></p>
<p><input type="submit" value="&nbsp;Refresh Display&nbsp;"  />&nbsp;&nbsp;<input type="submit" value="&nbsp;New Search&nbsp;" name="New Search" /></p>

	<p>Type/SubType</p>
		
		<ul>
		<li><input type="radio" name="type" value="All Types" onclick="TheForm.submit()">
      All Types (<?php echo $TypeTotal; ?>)</li>
<?php
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
		echo " onclick=\"TheForm.submit()\">
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
				echo " onclick=\"TheForm.submit()\">
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
		<li><input type="radio" name="make" value="All Makes" onclick="TheForm.submit()">
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
		?> onclick="TheForm.submit()">
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
		?> onclick="TheForm.submit()" />
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
 
</div>
<input type="hidden" value="<?php echo @$_POST['make']; ?>" name="OldMake">
<input type="hidden" value="<?php echo @$_POST['type']; ?>" name="OldType">


</div>   <!-- End of ProductMenu -->
<div id="StartFooter">&nbsp;</div>




</div>   <!-- End of UsedProductManagement -->
<?php echo $pagination; ?>
</form>

<?php


# Now remove old image files from  created stack.
$path = PRODUCTIMAGES.'store/';
$images = dir($path);

while (false !== ($entry = $images->read())) {
	if (is_file($path.$entry)) {
		if (in_array(pathinfo($path.$entry, PATHINFO_EXTENSION), array('txt', 'jpg')))
			if (date("Y-m-d", filemtime($path.$entry)) < date('Y-m-d', strtotime('-30 days')))
				unlink($path.$entry);
	}
}





?>
<!--   * @author Craig Rayner Hill Range Services craig (at) hillrange dot com dot au
  * for Webdoor Solutions July 2014

-->
