<?php
/**
  * @author Craig Rayner Hill Range Services craig@hillrange.com.au
  * for Webdoor Solutions July 2014
  * @version 12th February 2015
  * @since July 2014
  */

/**
  * Print an Object
  *
  * @version 10th November 2014
  * @since OLD
  * @param mixed The object to be printed
  * @param boolean Stop execution after printing object.
  * @return void
  */
	function printAnObject($object, $stop = false) {
	
		$caller = debug_backtrace();
		echo "<pre>\n";
		echo $caller[0]['line'].': '.$caller[0]['file'];
		//var_dump($caller);
		echo "\n</pre>\n";
		echo "<pre>\n";
		print_r($object);
		//var_dump($object);
		echo "\n</pre>\n";
		if ($stop) 
			trigger_error('Object Print Stop', E_USER_ERROR);
		return ;
	}

/**
 * get Configuration
 *
 * @version 12th February 2015
 * @since 12th February 2015
 * @return object
 */
 	function getConfig() {
	
		$config = new stdClass() ;
		// Absolute path of the xml file to parse
		$config->parsename = 'toowoomba';
		$config->filename = '/home/vanderfi/public_html/fastrack/'.$config->parsename.'.xml';
		// Absolute path of the ftp directory
		$config->ftpdir = '/home/vanderfi/public_html/fastrack/';
		$config->hostroot = 'http://www.vanderfield.com.au/';
		$config->documentroot = "/home/vanderfi/public_html/";
		$config->datastore = "/home/vanderfi/public_html/fastrack-include/".$config->parsename."/";

		
		$config->pageitems = 10;
		$config->firstpage = '<<';
		$config->lastpage = '>>';
		$config->prevpage = '<';
		$config->nextpage = '>';
		
		//The order is the list order for displayiong the information on the screen.
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
		$config->order = $order;

		//Test config.
		if (! is_file($config->filename))
			exit('The filename given in the configuration section is not available.');
			
		if (! is_dir($config->ftpdir))
			exit('The ftp directory given in the configuration section is not available.');
			
		if (filter_var($config->hostroot, FILTER_VALIDATE_URL) === FALSE) 
			exit('The host root given in the configuration section is not available.');
			
		if (! is_dir($config->documentroot))
			exit('The document root given in the configuration section is not available.');
			
		if (! is_dir($config->datastore)) {
			mkdir($config->datastore);
			if (! is_dir($config->datastore))
				exit('Not able to create data store directory.');
		}
		
		return $config;
	}

/**
  * Sort Results
  *
  * @version 25th July 2014
  * @since 25th July 2014
  * @param array The Results from Table Search
  * @param array The fields in order for sort (first in array has highest priority, key is the field name and value = ASC or DESC.
  * @return array The Results
  */
  	function SortResults($Result, $Sort){
		
		reset($Sort);
		$direction = $y[key($Sort)] = current($Sort);
		$name = key($Sort);
		unset($Sort[$name]);
		$t = array();
		foreach($Result as $q=>$w) {
			$t[$q] = $w[$name];
		}
		if (strtoupper($direction) == 'DESC') {
			arsort($t);
		} else {
			asort($t);
		}
		$ss = array();
		foreach($t as $item=>$value)
			$ss[$value][$item] = $Result[$item];
		if (count($Sort) >= 1) {
			foreach($ss as $v=>$s) {
				$ss[$v] = SortResults($s, $Sort);
			}
		}
		$Result = array();
		foreach($ss as $v=>$s) {
			foreach ($s as $item=>$values)
				$Result[$item] = $values;
		}
		return $Result ;
	}


	
class xmlParser {
	
/**
  * Method for loading Xml Data from string
  *
  * @version 6th August 2011
  * @since 6th August 2011
  * @author godseth (AT) o2.pl & rein_baarsma33 (AT) hotmail.com (Bugfixes in parseXml Method)
  * @uses XMLReader
  * @param string XML Data
  * @param bool 
  */
    public function parseString( $sXml , $bOptimize = false) {

        $oXml = new XMLReader();
        $this -> bOptimize = (bool) $bOptimize;
        try {

            // Set String Containing XML data
            $oXml->XML($sXml);

            // Parse Xml and return result
            return $this->parseXml($oXml);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
/**
  * XML Parser
  *
  * @version 6th August 2011
  * @since 6th August 2011
  * @author godseth (AT) o2.pl & rein_baarsma33 (AT) hotmail.com (Bugfixes in parseXml Method)
  * @uses XMLReader
  * @param XMLReader 
  * @return array
  */
    protected function parseXml( XMLReader $oXml ) {

		$aAssocXML = null;
        $iDc = -1;

        while($oXml->read()){
            switch ($oXml->nodeType) {

                case XMLReader::END_ELEMENT:

                    if ($this->bOptimize) {
                        $aAssocXML = $this->optXml($aAssocXML);
                    }
                    return $aAssocXML;

                case XMLReader::ELEMENT:

                    if(!isset($aAssocXML[$oXml->name])) {
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name] = '';
                            } else {
                                $aAssocXML[$oXml->name] = $this->parseXML($oXml);
                            }
                        }
                    } elseif (is_array($aAssocXML[$oXml->name])) {
                        if (!isset($aAssocXML[$oXml->name][0]))
                        {
                            $temp = $aAssocXML[$oXml->name];
                            foreach ($temp as $sKey=>$sValue)
                            unset($aAssocXML[$oXml->name][$sKey]);
                            $aAssocXML[$oXml->name][] = $temp;
                        }

                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    } else {
                        $mOldVar = $aAssocXML[$oXml->name];
                        $aAssocXML[$oXml->name] = array($mOldVar);
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    }

                    if($oXml->hasAttributes) {
                        $mElement =& $aAssocXML[$oXml->name][count($aAssocXML[$oXml->name]) - 1];
                        while($oXml->moveToNextAttribute()) {
                            $mElement[$oXml->name] = $oXml->value;
                        }
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:

                    $aAssocXML[++$iDc] = $oXml->value;

            }
        }

        return $aAssocXML;
    }
/**
  * Method to optimize assoc tree.
  * ( Deleting 0 index when element
  *  have one attribute / value )
  *
  * @version 7th August 2011
  * @since 6th August 2011
  * @author godseth (AT) o2.pl & rein_baarsma33 (AT) hotmail.com (Bugfixes in parseXml Method)
  * @param array Data to be corrected.
  * @return array corrected data.
  */
    public function optXml($mData) {
	
		if (is_array($mData)) {
            if (isset($mData[0]) && count($mData) == 1 ) {
                $mData = $mData[0];
				if (is_array($mData))
					$mData = $this->optXml($mData);
            } else {
                foreach ($mData as $q=>$aSub) {
                    $mData[$q] = $this->optXml($aSub);
                }
            }
        }
		return $mData;
    }
}

$config = getConfig();

$reader = new xmlParser();
try {
	$x = file_get_contents($config->filename);
} catch (Exception $e) {
	sleep ( 2 );
	$x = file_get_contents($config->filename);
}
	
$xx = $reader->parseString($x);
$xx = $reader->optXml($xx['dealer'][0]['listing']);
$TotalAvailable = count($xx);

$warning = '';
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
		$warning = "<p style='color: red'><strong>Your search for '".implode(',', $search)."' did not find any results.</strong></p>";
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
if (!isset($_POST['startKey']))
	$_POST['startKey'] = 1;
if (strval(intval($_POST['startKey'])) == $_POST['startKey'])
	$_POST['startKey'] = @$_POST['startKeyValues'][$_POST['startKey']];
if ($_POST['startKey'] == $config->firstpage)
	$_POST['startKey'] = $_POST['startKeyValues'][1];
if ($_POST['startKey'] == $config->lastpage)
	$_POST['startKey'] = $_POST['startKeyValues'][count($_POST['startKeyValues'])];
if ($_POST['startKey'] == $config->prevpage) {
	$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) - 1;
	if ($was < 1)
		$was = 1;
	$_POST['startKey'] = $_POST['startKeyValues'][$was];
}
if ($_POST['startKey'] == $config->nextpage) {
	$was = intval(array_search($_POST['oldStartKey'], $_POST['startKeyValues'])) + 1;
	if ($was > count($_POST['startKeyValues']))
		$was--;
	$_POST['startKey'] = $_POST['startKeyValues'][$was];
}




$type = array();
$make = array();
$menu = array();
$total = 0;
$MakeTotal = 0;
$TypeTotal = 0;

foreach($xx as $q=>$w) {
	//Limit the Make/model based on type selection
	if ( isset($_POST['type']) AND empty ($_POST['subtype'])) {
		if ($w['type'] == $_POST['type']) {
			@$menu['make'][] = $w['make']."::".$w['model'];
			@$make[$w['make']]['count']++;
			@$make[$w['make']][$w['model']]['count']++;
			$MakeTotal++;
		}
	} elseif ( isset($_POST['type']) AND isset ($_POST['subtype'])) {
		if ($w['type'] == $_POST['type'] AND $w['subtype'] == $_POST['subtype']) {
			@$menu['make'][] = $w['make']."::".$w['model'];
			@$make[$w['make']]['count']++;
			@$make[$w['make']][$w['model']]['count']++;
			$MakeTotal++;
		}
	} else {
		@$menu['make'][] = $w['make']."::".$w['model'];
		@$make[$w['make']]['count']++;
		@$make[$w['make']][$w['model']]['count']++;
		$MakeTotal++;
	}

	//Limit the Type/subtype based on make selection
	if ( isset($_POST['make']) AND empty ($_POST['model'])) {
		if ($w['make'] == $_POST['make']) {
			@$menu['type'][] = $w['type']."::".$w['subtype'];
			@$type[$w['type']]['count']++;
			@$type[$w['type']][$w['subtype']]['count']++;
			$TypeTotal++;
		}
	} elseif ( isset($_POST['make']) AND isset ($_POST['model'])) {
		if ($w['make'] == $_POST['make'] AND $w['model'] == $_POST['model']) {
			@$menu['type'][] = $w['type']."::".$w['subtype'];
			@$type[$w['type']]['count']++;
			@$type[$w['type']][$w['subtype']]['count']++;
			$TypeTotal++;
		}
	} else {
		@$menu['type'][] = $w['type']."::".$w['subtype'];
		@$type[$w['type']]['count']++;
		@$type[$w['type']][$w['subtype']]['count']++;
		$TypeTotal++;
	}

	if (! isset($type[$w['type']]['image']))
		@$type[$w['type']]['image'] = $image;
	$total++;
}
$menu['make'] = array_unique($menu['make']);
$menu['type'] = array_unique($menu['type']);
sort($menu['make']);
sort($menu['type']);





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
$order = $config->order;






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

$xx = SortResults($yy, array('listprice'=>'DESC', 'make'=>'ASC', 'model' => 'ASC'));

$count = count($xx);

/**  Do work here for pagination preparation */


?>
<div class="UsedProductManagement">
<form name="TheForm" id="TheForm" method="post">
<!--<p>Vanderfield are currently listing a total of <?php echo $total; ?> used products for sale.</p>-->
<?php
$yy = array();
$DisplayCount = 0;
$startid = 0;
$PageCount = 0;
foreach($xx as $q=>$w){
	if ($DisplayCount == 0) {
		$StartID = $w['id'];
	}
	$DisplayCount++;
	if ($DisplayCount > $config->pageitems - 1) {

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
		?><input type="submit" name="startKey" value="<?php echo $config->firstpage; ?>" class="Pagination" /> 
		<input type="submit" name="startKey" value="<?php echo $config->prevpage; ?>" class="Pagination" />
    
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
		?><input type="submit" name="startKey" value="<?php echo $config->nextpage; ?>" class="Pagination" /> 
		<input type="submit" name="startKey" value="<?php echo $config->lastpage; ?>" class="Pagination" /><?php	
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
$ShowCount = $config->pageitems;
if ($count < $config->pageitems)
	$ShowCount = $count;
?>

<div class="SaleItems">
<?php echo $warning; ?>
<p>Vanderfield currently has <?php echo $TotalAvailable; ?> used products in the catalogue.  Your search revealed <?php echo $count; ?> product<?php if ($count > 1) echo 's'; ?>, displayed <?php echo $config->pageitems; ?> products to a page.</p>
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
			if (is_file($config->ftpdir.$config->parsename.'_'.$w['id'].'_'.strval($count).'.jpg')) {
				$xx[$q]['image'][$count] = $config->ftpdir.$config->parsename.'_'.$w['id'].'_'.strval($count).'.jpg';
				if (empty($image))
					$image = $config->ftpdir.$config->parsename.'_'.$w['id'].'_'.strval($count).'.jpg';
				if (! is_file( $config->datastore .$config->parsename.'_'.$w['id'].'_'.strval($count).'.jpg')) {
					if (false !== ($im = @getimagesize($xx[$q]['image'][$count]))) {
						$height = 245;
						$y = $im[1]/$height;
						$width = intval($im[0]/$y);
						$thumb = imagecreatetruecolor($width, $height);
						$source = imagecreatefromjpeg($xx[$q]['image'][$count]);
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $im[0], $im[1]);
						imagejpeg($thumb,  $config->datastore .$config->parsename.'_'.$w['id'].'_'.strval($count).'.jpg');
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
	
	
		$FileID = fopen($config->datastore . $w['id'].".txt", "w");  
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
		?>
        </div> <!-- End of Specifications -->
		
		<!--<div style="text-align: center; float: left; width: 250px; ">-->
		<div>
        <!--<p style="text-align: center ">--><p><!--<a href="index.php?option=com_rsform&formId=8&productID=<?php echo $xx[$q]['id']; ?>" target="_self">-->
        <img class="firstImage" src='<?php echo str_replace(array($config->documentroot), array(''), @$xx[$q]['image'][1]); ?>' alt='' width="245" /><!--</a>-->
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
			<a class="thumb" href="#"><img src="<?php echo str_replace(array($config->documentroot), array(''), $i); ?>" alt="" width="<?php echo $tw; ?>" height="<?php echo $th; ?>">
			<span style="width: <?php echo strval($w + 4); ?>px; height: <?php echo strval($h+4); ?>px; "><img src="<?php echo str_replace(array($config->documentroot), array(''), $i); ?>" alt="" width="<?php echo $w; ?>" height="<?php echo $h; ?>"></span></a>
			<?php
		}
		
		echo "</div> <!-- End of SaleThumbs -->\n";
	
		echo"</div> <!-- End of Sale Item -->\n" ;
		$DisplayCount++;
		if ($DisplayCount > $config->pageitems - 1)
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
$images = dir($config->datastore);

while (false !== ($entry = $images->read())) {
	if (is_file($config->datastore.$entry)) {
		if (in_array(pathinfo($config->datastore.$entry, PATHINFO_EXTENSION), array('txt', 'jpg')))
			if (date("Y-m-d", filemtime($config->datastore.$entry)) < date('Y-m-d', strtotime('-30 days')))
				unlink($config->datastore.$entry);
	}
}


echo "<!--   * @author Craig Rayner Hill Range Services craig (at) hillrange dot com dot au";
echo "  * for Webdoor Solutions July 2014";
echo "";
echo "-->";

?>