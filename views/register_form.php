<html>
<head>
        <title>New Organization Registration</title>
</head>

<body>
        <?php echo validation_errors(); ?>
        <?php echo form_open('register\newOrganization'); ?>

        <p>Restaurant Name <input type = "text" name = "name" value = "<?php echo set_value('name'); ?>" size = "50" /></p>

        <p>Username <input type = "text" name = "name" value = "<?php echo set_value('username'); ?>" size = "50"/></p>

        <p>Owner Name <input type = "text" name = "name" value = "<?php echo set_value('owner_name'); ?>" size = "50"/></p>

        <p>Contact Number <input type = "text" name = "contact_number" value = "<?php echo set_value('contact_number'); ?>" size = "50"/> </p>

        <p>Address <input type = "text" name = "address" value = "<?php echo set_value('address'); ?>" size = "50"/></p>

        <p>Email id <input type = "text" name = "email" value = "<?php echo set_value('email'); ?>" size = "50"/></p>

		<p>Password <input type = "text" name = "password" value = "" size = "50"/></p>
		<p>Repeat Password <input type = "text" name = "repeat" value = "" size = "50"/></p>
		
		<p><input type = "submit" value = "Submit" /></p>
		</form>
</body>

</html>
