<?php

class filepage {

    //holds module info
    var $props = [];

    function load($did, $lang) {
        //loads module data from database

        $this->did = $did;
        $query = "SELECT * FROM doc_module_v as dmv, module_props as mp ".
           "WHERE dmv.prop_signature = mp.signature AND dmv.did=$did ".
            "AND dmv.langid='$lang' AND mp.module_signature='filepage'";
        $result = mysql_query($query);
        
        if ($result && mysql_num_rows($result) > 0) {
            while ($row = mysql_fetch_assoc($result)) {
                $this->props[$row['prop_signature']] = $row['value'];
            }
        } else {
           $this->props = [];
        }
    }
    

    //returns the value of the object properties, in this case $props=[], 
    function get($key) {
        return (isset($this->props[$key])) ? $this->props[$key] : "";
    }

    //prints the visual parts of the module
    function printEditArea() {
        echo "this new module and there is a need to make a text box and a bottom here<br>\n";
        echo "<input type='text' name='url' value='".$this->get('url')."'><br>\n";
        echo "current filepath is:".$this->get('url')."\n";
    }

    function save($post, $lang) {
        //saves module datain chosen language, when editing file
        //example: 

        $query = "REPLACE doc_module_v ( `did` , `module`, `prop_signature` , `langid` , `value`) VALUES ".
            "( '$post[did]', 'filepage', 'url', '$lang', '$post[url]')";
        //echo $query."<br/>";
        mysql_query($query);
    }

    function delete() {
        //delete module from db
        mysql_query("DELETE FROM doc_module_v WHERE module='filepage' AND did='$this->did'");
    }


    function deleteLang($langid) {
        //delete language version of module
        mysql_query("DELETE FROM doc_module_v WHERE module='filepage' AND did='$this->did' AND langid='$langid'");
    }
}

?>