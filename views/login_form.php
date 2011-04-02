<html>
<head>
	<title>Login</title>
</head>
<body>
	<?php echo form_open('user/login'); ?>
	<p>Username<input type = "text" name = "username" value = "" size = "50" /></p>
	<p>Password<input type = "password" name = "password" value = "" size = "50" /></p>
	<p><input type = "submit" value = "Login" /></p>
	</form>
</body>
</html>