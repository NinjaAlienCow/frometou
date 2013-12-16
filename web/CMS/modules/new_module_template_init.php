<?php
//init file is mandatory in order to install the module.
// the init file must contain all database information, and module properties.

//Ensure we have the required entry in the module table
//eks. ensure_module("mod_filepage", "filepage", "modules/filepage.php", "modules/filepage.php", "page"); 
ensure_module("module_signature", "module_name", "display_path", "cms_path", "module_type"); 


//if you module has proberties then it has to be activated with module-probs in the database
//eks ensure_module_props("normal_page_header", "normal_page", "Header");
ensure_module_props("signature", "module_signature", "property_name");

?>