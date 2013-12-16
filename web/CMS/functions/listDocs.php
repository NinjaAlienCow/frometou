<?php
require_once("functions/cms_general.php");

function availableLang(){
	$availableLang = [];
	$mysql = "SELECT id, thumbnail_path FROM lang ORDER BY priority DESC";
	$result = mysql_query($mysql);
	while ($row = mysql_fetch_assoc($result)) {
		$availableLang[$row['thumbnail_path']] = $row['id'];
	}
	return $availableLang;
}

/* insert flags on the page to change the current language. Used when editing pages, etc. */
function printDocFlags($did, $docSelected) {
	$availableLang = availableLang();
	global $SITE_INFO_PUBLIC_ROOT, $SITE_INFO_LANGS_ENABLED;
	$flagRes = "";
	foreach ($availableLang as $langImg => $langId) {
		$mysql = "SELECT langid, did FROM doc_general_v WHERE did = '$did' AND langid = '$langId' ORDER BY did DESC";
		$result = mysql_query($mysql);

		$translationExist = (mysql_num_rows($result) > 0);
		$isCurrentLang = ($langId == $_SESSION['lang']);

		$flagRes .= "<a href='doc_edit.php?did=".$did."&lang=".$langId;
		$flagRes .= ($translationExist) ? "" : "&new=true";
		$flagRes .= "'><IMG SRC='".$SITE_INFO_PUBLIC_ROOT.$langImg."' ";
		$flagRes .= ($translationExist) ? "WIDTH='22' HEIGHT='14'" : "WIDTH='11' HEIGHT='7'";
		$flagRes .= ($isCurrentLang && $docSelected) ? " BORDER=2" : "BORDER=0";
		$flagRes .= "/> </a> ";
	}
	return $flagRes;
}


//childArray saves all data in array (so it only opens db once), the selection of array data is made in printChildArray
function childArray($thisDid){
	$query = "SELECT doc.did, doc.ident, doc.module_signature, hierarchy.parent, hierarchy.hid FROM doc, hierarchy WHERE hierarchy.parent IS NOT NULL AND doc.did = hierarchy.did ORDER BY doc.priority DESC ";
	$result = mysql_query($query);
	while ($row = mysql_fetch_assoc($result)) {
		$hid = $row['hid'];
		$childArray[$hid]['did'] = $row['did'];
		$childArray[$hid]['parent'] = $row['parent'];
		$childArray[$hid]['link'] = "";
		if ($thisDid == $row['did']) {
			$childArray[$hid]['link'] .= "<li class='selectedDoc'>";
			$childArray[$hid]['link'] .= moduleIllustration($row['module_signature']);
			$childArray[$hid]['link'] .= "<A HREF='doc_edit.php?did=".$row['did']."'>".$row['ident']."</A>";
			$childArray[$hid]['link'] .= printDocFlags($row['did'], $_SESSION['did']);
			$childArray[$hid]['link'] .= "</li>\n";
		}else{
			$childArray[$hid]['link'] .= "<li>";
			$childArray[$hid]['link'] .= moduleIllustration($row['module_signature']);
			$childArray[$hid]['link'] .= "<A HREF='doc_edit.php?did=".$row['did']."'>".$row['ident']."</A>";
			$childArray[$hid]['link'] .= printDocFlags($row['did'],NULL);
			$childArray[$hid]['link'] .= "</li>\n";
		}
	}
 	return $childArray;
//print_r($childArray);
}

//printChildArray  selects data to be shown from childArray
function printChildArray($childArray,$parrent){
	$childs = "<ul class='childlist'>\n";
	foreach ($childArray as $value) {
		if ($value['parent'] == $parrent){
			$childs .= $value['link'];			
		} 
  	}
	$childs .= "</ul>\n";

 	return $childs;
}

//loads the grafig related to module
function moduleIllustration($module){
	global $SITE_INFO_PUBLIC_ROOT, $SITE_INFO_LANGS_ENABLED;
	$img = "<b><img src='".$SITE_INFO_PUBLIC_ROOT."CMS/layout/imgs/".$module.".png' WIDTH='10' HEIGHT='14' alt='Smiley face'></b>";
	return $img;
}

function ListDocs($did){
	$notInUse = "";
	$inUse = "<ul class='mainmenu'>\n";
	$query = "SELECT doc.did, doc.ident, doc.module_signature, hierarchy.did AS hdid FROM doc LEFT JOIN hierarchy on doc.did = hierarchy.did WHERE hierarchy.parent IS NULL ORDER BY ident";
	$result = mysql_query($query) or die(mysql_error());
	//Saves data in $inUse for active pages, and $noInUse for all inactive pages
	while ($row = mysql_fetch_array($result)) {
		if (isset($row['hdid'])) {
			$isSelected = ($did == $row['hdid']);
			$inUse .= $isSelected ? "<li class='selectedDoc'>" : "<li>";
			$inUse .= moduleIllustration($row['module_signature']);
			$inUse .= "<a href='doc_edit.php?did=".$row['did']."'> ".$row['ident']."</a>";
			$inUse .= printDocFlags($row['did'], $isSelected);
			$inUse .= "</li>\n";
			$inUse .=  printChildArray(childArray($did),$row['did']);
			//runs through childlist for every page wich is showen on mainmenu.
			//$inUse .= childArray($isSelected,'link');
		}else{
			$isSelected = ($did == $row['did']);
			$notInUse .= $isSelected ? "<li class='selectedDoc'>" : "<li>";
			$notInUse .= moduleIllustration($row['module_signature']);
			$notInUse .= "<a href='doc_edit.php?did=".$row['did']."'> ".$row['ident']."</a> ";
			$notInUse .= printDocFlags($row['did'], $isSelected);
			$notInUse .= "</li>". $row['hdid'];			
		}

	}
	$inUse .= "</ul>\n";

// Save data in 2 seperate arrays, 1 for files showen on page
	echo "<b>shown in menu: </b><br>" . $inUse;	
	echo "<br><br><b>Not shown in mainmenu: </b><br>".$notInUse;
	echo "<br><br>";
}

?>
