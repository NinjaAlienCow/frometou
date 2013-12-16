<?php
require_once("functions/cms_general.php");

//../CMS/navigator
/* if user decided to create a new document */
if (isset($_POST['new'])) {
	//adding defaults
	$module_signature = $_POST['signature'];

	//create a stub for the new document (the non-language specific)
	$query = "INSERT INTO doc (did, module_signature, description_img, ident, priority) VALUES ";
	$query .= "( '', '".$module_signature."', '', 'new ".$module_signature."', '100')";
	mysql_query($query);

	//get new id:
	$mysql = "SELECT did FROM doc WHERE module_signature='$module_signature' AND ident='new ".$module_signature."' AND priority='100' ORDER BY did DESC LIMIT 1";
	$row = mysql_fetch_row(mysql_query($mysql));
	$newID = $row[0];

	echo "<script>window.location = 'navigator.php?newPage=$newID';</script>";
}


if (isset($_GET['newPage'])) {
	//lastly edit the new document in editDoc.php in frame
	echo "<SCRIPT>setTimeout('document.reloadFrame.submit()',0);</script>";
	echo "<form name='reloadFrame' method='post' action='http:doc_edit.php?did=".$_GET["newPage"]."'></form>";
	echo $_GET['newPage'];
}


	$query = "SELECT * FROM module WHERE module_type='page' AND enabled=1";
	$res = mysql_query($query);
	while ($row = mysql_fetch_assoc($res)) {
		echo "<FORM method='POST' NAME='newDoc' target='_self'>\n";
			echo "<INPUT class='new' TYPE='submit' value='+ new ".$row['module_name']."' name='new'><br>\n";
			echo "<INPUT TYPE='hidden' value='".$row['module_signature']."' name='signature'><br>\n";
		echo "</FORM>\n";

	}
echo $_POST['signature'];
print_r($_POST);

?>


	