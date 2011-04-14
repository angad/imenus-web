<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */
?>


<html>
<head>
        <title>New Organization Registration</title>
		<link rel='stylesheet' type='text/css' media='all' href='http://imenus.tk/stylesheet.css' /> 
		<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
		<style>
		#registration input {
			width:300px;
		}
		
		</style>
</head>

<body>
<div id = "outer-wrapper">

<div id = "header">
	<h2><a href = "http://imenus.tk/">iMenus</a></h2><br/>
	<h3>Restaurant Menu Designer</h3>
</div>

	<div id = "registration">
        <?php echo validation_errors(); ?>
		<?php echo $error;?>		
        <?php echo form_open_multipart('register/newOrganization'); ?>		

        <p><h4>Restaurant Name</h4> <input type = "text" name = "name" value = "<?php echo set_value('name'); ?>" size = "50" /></p>
		
        <p><h4>Username</h4><input type = "text" name = "username" value = "<?php echo set_value('username'); ?>" size = "50"/></p>

        <p><h4>Owner Name</h4><input type = "text" name = "owner_name" value = "<?php echo set_value('owner_name'); ?>" size = "50"/></p>

        <p><h4>Contact Number</h4><input type = "text" name = "contact_number" value = "<?php echo set_value('contact_number'); ?>" size = "50"/> </p>

        <p><h4>Address</h4><input type = "text" name = "address" value = "<?php echo set_value('address'); ?>" size = "50"/></p>

        <p><h4>Email id</h4><input type = "text" name = "email" value = "<?php echo set_value('email'); ?>" size = "50"/></p>

		<p><h4>Password</h4><input type = "password" name = "password" value = "" size = "50"/></p>
		<p><h4>Repeat Password</h4><input type = "password" name = "repeat" value = "" size = "50"/></p>
		<p><h4>Invite Key (Optional)</h4><input type = "text" name = "invite_key" value = "" size = "50"/> </p>
		
		<h4>Upload Logo</h4> <br/>
		GIF, PNG or JPEG only <br />
        Maximum Size: 300 x 300 <br />
		<input type="file" style = "-moz-box-shadow: 0px 0px 0px #888888 inset; 
		-webkit-box-shadow: 0px 0px 0px #888888 inset;
		box-shadow: 0px 0px 0px #888888 inset; font-size:12px" name="logo" size="20" />
		
		<p><input type = "submit" style ="width:200px;" value = "Submit" /></p>
		</form>
	</div>
</div>

<div id = "footer">
The iMenus Team, CS3217
</div>

</body>

</html>
