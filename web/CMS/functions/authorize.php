<?php
require_once("../functions/system/siteInfo.php");
header('Content-type: text/html; charset=iso-8859-1');

session_start(); // Starts the session

if (!($_SESSION['uname'] == "$SITE_INFO_CMS_UNAME" && $_SESSION['pass'] == "$SITE_INFO_CMS_PASS")) {
	echo "<HTML><BODY><SCRIPT LANGUAGE='javascript'>";
	echo "document.location = 'login.php?errorMsg=An error occured.. please login again'";
	echo "</SCRIPT></BODY></HTML>";
	die();
 } else {
	require_once("{$SITE_INFO_LOCALROOT}functions/system/connect.php");
	if (!isset($_SESSION['lang'])) {
		$result = mysql_query("SELECT lang FROM lang ORDER BY priority DESC");
		$row = mysql_fetch_row($result);
		$_SESSION['lang'] = $row[0];
	}
 }
?>