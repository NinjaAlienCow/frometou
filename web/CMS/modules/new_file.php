<?php
require_once("functions/cms_general.php");

//../CMS/navigator
/* if user decided to create a new document */
if (isset($_POST['new_file'])) {
	//adding defaults
	$module_signature = "normal_page";

	//create a stub for the new File (the non-language specific)
	$query = "INSERT INTO doc (did, module_signature, description_img, ident, priority) VALUES ";
	$query .= "( '', '".$module_signature."', '', 'new file', '100')";
	mysql_query($query);

	//get new id:
	$mysql = "SELECT did FROM doc WHERE module_signature='$module_signature' AND ident='new document' AND priority='100' ORDER BY did DESC LIMIT 1";
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

?>
	<FORM method="POST" NAME="newFile" target="_self">
		<INPUT class="new" TYPE="submit" value="+ new file" name="new_file">
	</FORM>