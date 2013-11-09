<?php
/*
This modules adds a check button to the edit document page, marking whether it should
be shown on the 'main menu' or not. 

TODO: Describe how to show the 'main menu' on the homepage
*/
if (!class_exists('mainmenu')) {
	class mainmenu {
		
		function load($did) {
			$this->did = $did;
		}

		function printEditArea () {
			global $_GET, $_POST;

			//Save the form is it was submitted:
			if (isset($_POST['main_menu_submit'])) {
				$this->saveForm($this->did, $_POST);
			}

			//is this document already set to be on the main menu:
			$result = mysql_query("SELECT * FROM hierarchy WHERE did = '$this->did' AND parent IS NULL");
			$row = mysql_num_rows($result);
			//if already on menu set $checked = "checked" else set to ""
			$checked = (mysql_num_rows($result) > 0) ? "checked" : "";
			$this->printForm($this->did, $checked);
		}

		function printForm($did, $checked) {
			?>
	        <form name="main_menu_form" target="_self" method="post">
		    	<fieldset><legend><b>Display on menu</b></legend>
		    		<input type='hidden' name="did" value="<?php echo $did; ?>" />
		        	<b>Display this document be displayed on the main menu?:
		        	<input type='checkbox' name='show_on_main' value='yes' <?php echo $checked ?> /> yes</b>
		        	<input type='submit' value='save the decision' name='main_menu_submit' />
		        </fieldset>
	    	</form>
	    	<?php
		}

		function saveForm($did, $post) {
			//if the checkbox is marked
			if (isset($post['show_on_main']) && $post['show_on_main'] == "yes") {
				mysql_query("INSERT INTO hierarchy (did) VALUES ('$did')");
			} else {
				$this->delete();			
			}
		}

		function delete() {
			mysql_query("DELETE FROM hierarchy WHERE did = '$this->did' and parent IS NULL");			
		}
	}
}
?>