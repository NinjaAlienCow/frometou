<?php
//Ensure we have the required entry in the module table
ensure_module("filepage", "filepage", "modules/filepage.php", "modules/filepage.php", "page"); 

//create filpage table
ensure_module_props("url", "filepage", "filepage_url");
?>
