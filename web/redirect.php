<?php
require_once("functions/connect.php");
require_once("functions/siteInfo.php");

if (isset($_GET['page'])) {
	$_GET['page'] = str_replace("'", "-", $_GET['page']);
	$query = "SELECT did FROM mapping WHERE path='".$_GET['page']."'";
	if ($row = mysql_fetch_row(mysql_query($query))) {
		if (isset($_GET['lang'])) {
			$location = $SITE_INFO_PUBLIC_ROOT.$_GET['lang']."/";
		} else {
			$location = $SITE_INFO_PUBLIC_ROOT."en/";
		}
		$location .= "page".$row[0];	
		header("location:".$location);
	} else {
		echo "document not found:<BR>";
		var_dump($_GET);
	}
 }
?>