<?php

class new_module {

    //holds module info
    //var $props = [];

    function load($did, $lang) {
        //loads module data from database
        
        //example:
        //$this->did = $did;
        //$query = "SELECT * FROM doc_module_v as dmv, module_props as mp ".
        //   "WHERE dmv.prop_signature = mp.signature AND dmv.did=$did ".
        //    "AND dmv.langid='$lang' AND mp.module_signature='new_module'";
        //$result = mysql_query($query);
        
        //if ($result && mysql_num_rows($result) > 0) {
        //    while ($row = mysql_fetch_assoc($result)) {
        //        $this->props[$row['prop_signature']] = $row['value'];
        //    }
        //} else {
        //   $this->props = [];
        //}
    }
    
    function get($key) {
        return (isset($this->props[$key])) ? $this->props[$key] : "";
    }

    function printEditArea() {
        //prints the visual parts of the module
        //example: echo "this new module";
    }

    function save($post, $lang) {
        // saves module datain chosen language, when editing file
        //example: $did = $post['did'];
    }

    function delete() {
        //delete module from db
        //example: mysql_query("DELETE FROM doc_module_v WHERE module='new_module' AND did='$this->did'");
    }


    function deleteLang($langid) {
        //delete language version of module
        //example: mysql_query("DELETE FROM doc_module_v WHERE module='new_module' AND did='$this->did' AND langid='$langid'");
    }
}

?>