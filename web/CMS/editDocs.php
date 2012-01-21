<?php
header('Content-Type: text/html; charset=iso-8859-1');

require_once("authorize.php");
require_once("../functions/functions.php");
require_once("../functions/documentBase.php");
require_once("../functions/parsing.php");

$filename = "editDocs.php";
$id = "did";

if (isset($_GET[$id])) {
	$_POST[$id] = $_GET[$id];
 }

//if user chose to delete language version:
if (!isset($_POST[$id]) || $_POST[$id] == "") {
	header("location:listDocs.php");
}
/* -------------------------------- if new language chosen ----------------------------- */
if (isset($_GET['langid'])) {
	$_SESSION['langid'] = $_GET['langid'];
	if (isset($_POST[$id])) {
		$params = "?$id=".$_POST[$id];
	}
	header("location:".$filename.$params);
}

//To be used if a regular document is shown
function showRegularDocForm() {
	global $id; 
	global $prop;
	//get translation specific regular document info
 	if (isset($_POST[$id])) {
		$query = "SELECT pagetitle, header, postheader, body ";
		$query .= "FROM doc_regular_v WHERE did=".$_POST['did']." AND langid=".$_SESSION['langid'];
		//echo $query;
		$result = mysql_query($query);
		if (mysql_num_rows($result) > 0) {
			foreach (mysql_fetch_assoc($result) as $var=>$value) {
				$prop[$var] = $value;
			}	
			$prop['body'] = fixQuotes($prop['body']);
			$prop['body'] = readImages($prop['body']);
		}
	}
	?>
	<TABLE>
	<TR><TH>pagetitle: </TH><TD COLSPAN="2"><input TYPE='text' size="80" name="pagetitle" value="<?php echo $prop['pagetitle'] ?>"></TD></TR>
	<TR><TH>header: </TH><TD COLSPAN="2"><input TYPE='text' size="80" name="header" value="<?php echo $prop['header'] ?>"></TD></TR>
	<TR><TH>postheader: </TH><TD COLSPAN="2"><input TYPE='text' size="80" name="postheader" value="<?php echo $prop['postheader'] ?>"></TD></TR>
	<TR><TH COLSPAN=4>
	<textarea name="bodyEditor"><?php echo $prop['body']; ?></textarea>
	<script language="JavaScript" type="text/javascript">
	CKEDITOR.replace( 'bodyEditor' , {toolbar : 'MyToolbar', filebrowserBrowseUrl: "/CMS/kfm/"});
	</script>
	</TH></TR>
	<TR><TH COLSPAN=4 style="text-align:left;">
           <INPUT TYPE="submit" value="save" name="saveDoc">
	</TH></TR>
	</TABLE>
	<?php
}
//handles the part of the form specific to a regular document
function handleRegularDocForm() {
	global $id;
	$_POST['bodyEditor'] = rmNewlines($_POST['bodyEditor']);
	$_POST['bodyEditor'] = fixQuotes($_POST['bodyEditor']);
	$_POST['bodyEditor'] = saveImages($_POST['bodyEditor']);

	$query = "REPLACE doc_regular_v ( did, langid, pagetitle, header, postheader, body ) VALUES ";
	$query .= "( ".$_POST[$id].", ".$_SESSION['langid'].", \"".$_POST['pagetitle']."\", \"".$_POST['header']."\", \"".$_POST['postheader']."\", \"".$_POST['bodyEditor']."\" )";
	//echo $query;
	mysql_query($query);
	header("location:$filename?$id=".$_POST[$id]);
}

function deleteRegularDoc() {
	//delete version of the document
	$query = "DELETE FROM doc_regular_v WHERE did=".$_POST['did']." AND langid=".$_SESSION['langid'];
	//echo $query;
	mysql_query($query);
	gotoAvailableLang("SELECT langid FROM doc_regular_v WHERE did=".$_POST['did']);
}

function gotoAvailableLang($query) {
	$result = mysql_query($query);
	//if no translations available
	if (mysql_num_rows($result) <= 0) {
		//goto default language
		$query = "SELECT langid FROM defaultLangs";
		$result = mysql_query($query);
	}
	$row = mysql_fetch_assoc($result);
	$_SESSION['langid'] = $row['langid'];
	header("location:".$filename."?did=".$_POST['did']);
}

/* --------------------------------- document form is submitted ---------------------------------------------------------- */
if (isset($_POST['saveDoc'])) {
	//first update the general properties:
	//if we have a valid id
	$query = "UPDATE doc SET priority = ".$_POST['priority'].", typeid=".$_POST['typeid'].", description_img=\"".$_POST['description_img']."\", ident=\"".$_POST['ident']."\" WHERE $id='".$_POST[$id]."'";
	mysql_query($query);
	//update translation specific general properties
	$query = "REPLACE doc_general_v ( did, langid, linktext, description ) VALUES ( ".$_POST[$id].", ".$_SESSION['langid'].", \"".$_POST['linktext']."\", \"".$_POST['description']."\")";
	mysql_query($query);
	//now take care of the rest of the form based on the format:
	if ($_POST['format'] == "regular") {
		//if regular document:
		handleRegularDocForm();
	}
 } else if (isset($_POST['delete'])) {
	//Delete general language specific properties
	$query = "DELETE FROM doc_general_v WHERE $id=".$_POST[$id]." AND langid=".$_SESSION['langid'];
	mysql_query($query);
	//echo $query;
	//Handle the rest of the deletion based on the format:
	if ($_POST['format'] == "regular") {
		//if regular document
		deleteRegularDoc();
	}












/* -------------- categorization ----------------------------------------------- */
 } else if (isset($_POST['addParent']) && $_POST['addp'] != '-1') {
	$mysql = "INSERT INTO hierarchy (parent, did) VALUES (".$_POST['addp'].", ".$_POST[$id].")";
	mysql_query($mysql);
	header("location:$filename?$id=".$_POST[$id]);
 } else if (isset($_POST['delParent']) && $_POST['delp'] != '-1') {
	mysql_query("DELETE FROM hierarchy WHERE did='".$_POST[$id]."' and parent='".$_POST['delp']."'");
	header("location:$filename?$id=".$_POST[$id]);	
} else if (isset($_POST['addChild']) && $_POST['addc'] != '-1') {
	mysql_query("INSERT INTO hierarchy (parent, did) VALUES (".$_POST[$id].", ".$_POST['addc'].")");
	header("location:$filename?$id=".$_POST[$id]);
 } else if (isset($_POST['delChild']) && $_POST['delc'] != '-1') {
	mysql_query("DELETE FROM hierarchy WHERE did='".$_POST['delc']."' and parent='".$_POST[$id]."'");
	header("location:$filename?$id=".$_POST[$id]);	
/* ----------------- if no form is submitted ------------------------------------ */
 } else	if (isset($_POST[$id])) {
	$query = "SELECT doc.did, doc.format, doc.description_img, doc.priority, doc.typeid, doc.ident, linktext, description ";
	$query .= "FROM doc, doc_general_v WHERE doc.did=".$_POST['did']." AND langid=".$_SESSION['langid']." AND doc.did = doc_general_v.did";
	//echo $query;
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		$prop = mysql_fetch_assoc($result);
	//	$prop['body'] = fixQuotes($prop['body']);
	//	$prop['body'] = readImages($prop['body']);
	} else {
		$query = "SELECT did, priority, typeid, format, description_img, ident ";
		$query .= "FROM doc WHERE did=".$_POST['did'];
		$result = mysql_query($query);
		$prop = mysql_fetch_assoc($result);
	}
 }


?>
<HTML>
<HEAD>
<script type="text/javascript" src="functions/jquery.js"></script>	
<script type="text/javascript" src="/CMS/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$().ready(function() {
	var did=<?php echo "'$did'"; ?>;
	//comment
	$('#moduleTablePlaceholder').load("docModuleHandler.php?did="+did, function() {
		$('.visibilityToggler').click(function(o){
			$("#inputfield"+$(this).val()).toggle();
		});
				
	});
});
</script>
<SCRIPT LANGUAGE='javascript'>
function showhide(id) {
	if (document.getElementById(id).style.display == 'none') {
		document.getElementById(id).style.display = 'block';
		document.getElementById("documentInfoSubPlus").style.display = "none";
	} else {
		document.getElementById(id).style.display = 'none';
		document.getElementById("documentInfoSubPlus").style.display = "inline-block";
	}
}
</SCRIPT>

<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1" /> 
	<LINK REL="stylesheet" type="text/css" href="css/general.css">
	<title>Edit/add documents</title>
</HEAD>
	<BODY>
	<TABLE BORDER=0 WIDTH='100%'><TR><TD><H1>Edit/add Documents</H1></TD><TD ALIGN='right'><?php
/* -------------- fix flags ------------------------------------ */
$result = mysql_query("SELECT langid, small FROM lang, images WHERE lang.iid = images.iid ORDER BY priority DESC");
while ($r = mysql_fetch_row($result)) {
	if ($r[0] == $_SESSION['langid']) {
		echo "<IMG SRC='".$publicRoot.$r[1]."' WIDTH='44' HEIGHT='30'>&nbsp;";
	} else {
		echo "<A HREF='$filename?";
		if (isset($_POST[$id])) {
			echo "$id=".$_POST[$id]."&";
		}
		echo "langid=".$r[0]."'><IMG SRC='".$publicRoot.$r[1]."' WIDTH='22' HEIGHT='15' BORDER=0></A>&nbsp;";
	}
 }
/* ------------------------------------------------------------ */
?> 
</TD></TR></TABLE>
<BR><A HREF='listDocs.php'>Back to list of documents</A>
<HR>
<FORM name="f1" target="_self" method="post" action="<?php echo $filename; ?>" onSubmit="return submitForm();">

<FIELDSET ID="documentInfo"><LEGEND><B>
	<?php
	//Show the basic php stuff
	?>
	<A HREF="#" onClick="showhide('documentInfoSub'); showhide('cke_bodyEdit'); return false;">
		Document properties <font id="documentInfoSubPlus" style="display:none;">+</font>
	</A></B></LEGEND>
	<input type='hidden' name="<?php echo $id; ?>" value="<?php echo $_POST[$id]; ?>">
	<input type='hidden' name="format" value="<?php echo $prop['format']; ?>">
	<TABLE BORDER=0 id="standardInfo" WIDTH=100%>
		<TR><TH>identifier: </TH><TD><input TYPE='text' size="50" name="ident" value="<?php echo $prop['ident']; ?>"></TD>
	   	    <TH STYLE="width:0; text-align:right;">priority:&nbsp; </TH><TD WIDTH=100%><input TYPE='text' size="3" name="priority" value="<?php echo $prop['priority']; ?>"></TD>
		    <TD WIDTH=0><INPUT TYPE="submit" value="&nbsp;save&nbsp;" name="saveDoc"></TD></TR>

		<TR><TH STYLE="width:0;">type: </TH><TD style="width:0;"> <?php echo selectType("typeid", 1, (isset($_POST[$id])) ? $prop['typeid'] : null); ?></TD>
	   	    <TH style="text-align:right; vertical-align:top">image:&nbsp; </TH><TD ROWSPAN=3 STYLE="vertical-align:top; text-align:left;"><A HREF="#" style="font-size:11px;">select image</A></TD>
	   	    <TD style="text-align:right"><INPUT TYPE="submit" value="delete" onSubmit="return confirm('Really delete document?');" name="delete"></TD>
		</TR>
	
		<TR><TH>linktext:</TH><TD><input size="50" name="linktext" value="<?php echo $prop['linktext']; ?>"></TD>
	   	    <TD></TD><TD></TD>
		</TR>
		<TR><TH>description: </TH><TD><TEXTAREA COLS=50 ROWS=3 NAME='description'><?php echo $prop['description'] ?></TEXTAREA></TD>
		    <TD></TD><TD></TD>
		</TR>
	</TABLE>
	<?php
	//all the common stuff has been printed above .. now for the specific stuff for language and document type:
	//chech if it's a regular document:
	if ($prop['format'] == "regular") {
		showRegularDocForm();
	} else if ($prop['format'] == "module") {
		echo "<DIV id='moduleTablePlaceholder'>Loading information...</DIV>";
	}
	?>
	</FIELDSET>
</form>
	<BR>
	
<?php
	if (isset($_POST[$id])) {
       ?>
	   <FIELDSET><LEGEND><B>Categorization</B></LEGEND>
	   <TABLE><TBODY>
	   <TR><TD>
	   <?php 
	   $sql = "SELECT hierarchy.parent, ident FROM hierarchy, doc WHERE ";
       	   $sql .= " doc.did = hierarchy.parent AND hierarchy.did = ".$prop['did'] ;
           $sql .= " ORDER BY ident ASC";
	   //echo $sql;
	   $result=mysql_query($sql);
	   if (mysql_num_rows($result) != null) {
		   while ($row = mysql_fetch_assoc($result)) {
			echo "<A HREF='?did=".$prop['did']."&parent=".$row['parent']."&rmParent=1'>[remove]</A> <A HREF='?did=".$row['parent']."'>".$row['ident']."</A><BR>";
		   }
	   }
	   ?>
	   </TD></TR></TABLE>
	   <FORM METHOD="POST">
	   <?php echo selectDocument("- SELECT PARENT TO ADD -", "addp", 1, null); ?>	<INPUT TYPE="submit" NAME="addParent" VALUE="Set as parent"><BR>
	   <?php echo selectParent(" - SELECT PARENT TO DELETE -", "delp", 1, $_POST[$id]); ?> <INPUT TYPE="submit" NAME="delParent" VALUE="Remove as parent">

	   <?php //if index
		   echo "<HR>";
		   echo selectDocument("- SELECT CHILD TO ADD -", "addc", 1, null);
		   echo "<INPUT TYPE='submit' NAME='addChild' VALUE='Set as child'>";
		   echo "<BR>";
		   echo selectChild("- SELECT CHILD TO REMOVE -", "delc", 1, $_POST[$id]); 
		   echo "<INPUT TYPE='submit' NAME='delChild' VALUE='Remove as child'>";
	   ?>
	   </FORM>
	   </FIELDSET>
	   <?php
	}
?>
<A HREF='listDocs.php'>Back to list of documents</A></BODY>
</HTML>
