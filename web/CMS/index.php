<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("functions/authorize.php");
?>
<html>
<head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
        <LINK REL="stylesheet" type="text/css" href="css/general.css">

<title><?php echo $SITE_INFO_WEB_NAME; ?>  - administrative pages</title>
</head>
<frameset cols="153, *">
<frame name="navigation" src="navigator.php" marginheight="0" marginwidth="0">
<frame name="main" src="main.php">
</html>
