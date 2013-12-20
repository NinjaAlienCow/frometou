<?php
require_once("functions.php");

/* insert flags on the page to change the current language. Used when editing pages, etc. */


function cms_insert_flags($id, $postid) {
	global $SITE_INFO_PUBLIC_ROOT, $SITE_INFO_LANGS_ENABLED;
	//only show flags if we have multiple languages enabled for website. This is a setting in siteInfo.php
	if ($SITE_INFO_LANGS_ENABLED) {
		//inner join is made in order to find out wich languege page there is in use
		$mysql = "SELECT id,thumbnail_path, langid FROM lang LEFT JOIN doc_general_v ON did = ".$_SESSION['did']." AND id=langid ORDER BY priority DESC";
		$result = mysql_query($mysql);

		while ($row = mysql_fetch_assoc($result)) {
			if ($row['id'] == $_SESSION['lang']) {
				echo "<div class='selectedflagdiv document".$row['id']."'><IMG class='selectedflag' SRC='".$SITE_INFO_PUBLIC_ROOT.$row['thumbnail_path']."' border=1 WIDTH='44' HEIGHT='30'></div>&nbsp;";
			} else {
				if ($row['langid'] == $row['id']){
					//this language version exist
					echo "<div class='genFlagDiv document".$row['id']."'><A HREF='?";
					if (isset($postid)) {
						echo "$id=$postid&";
					}
					echo "lang=".$row['id']."'><IMG SRC='".$SITE_INFO_PUBLIC_ROOT.$row['thumbnail_path']."' class='genflag_active' WIDTH='22' HEIGHT='15' BORDER=1></A></div>&nbsp;";
				} else {
					//this languge version does not exist
					echo "<div class='genFlagDiv document".$row['id']."'><img SRC='http://localhost/frometou/web/layout/imgs/plus.png' class='newLanguage'><A HREF='?";
					if (isset($postid)) {
						echo "$id=$postid&";
					}
					echo "lang=".$row['id']."'><IMG SRC='".$SITE_INFO_PUBLIC_ROOT.$row['thumbnail_path']."' class='genflag_inactive' WIDTH='22' HEIGHT='15' BORDER=1></A></div>&nbsp;";
				}
			}
 		}
	}
}

?>
