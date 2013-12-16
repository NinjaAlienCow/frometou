<?php
require_once($SITE_INFO_LOCAL_ROOT."functions/parse_functions.php");

//get data:
$fields = [];

$query = "SELECT * FROM doc_module_v as dmv, module_props as mp ".
    "WHERE dmv.prop_signature = mp.signature AND dmv.did=".$props->get("did")." ".
    "AND dmv.langid='".$props->get('lang')."' AND mp.module_signature='filepage'";

$result = mysql_query($query);
if ($result && mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $fields[$row['prop_signature']] = $row['value'];
    }
}

if (isset($fields['url'])) echo "<H1 CLASS='docheader'>".$fields['url']."</H1>";
?>