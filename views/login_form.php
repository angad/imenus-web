<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */
?>

<html>
<head>
	<title>Login</title>
	
	<link rel='stylesheet' type='text/css' media='all' href='<?php echo site_url('../stylesheet.css');?>' />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'> 
	
</head>
<body>

<div id = "outer-wrapper">
<div id = "header">
	<h2><a href = "http://imenus.tk/">iMenus</a></h2><br/>
	<h3>Restaurant Menu Designer</h3>
</div>

	<div id = "login">
		<?php echo form_open('user/login'); ?>
			<p><h4>Username</h4><input type = "text" id = "formelem" name = "username" value = "" size = "50" /></p>
			<p><h4>Password</h4><input type = "password" id = "formelem" name = "password" value = "" size = "50" /></p>
			<p><input type = "submit" value = "Login" /></p>
		</form>

	<br/>
	</div>

	<div id = "registerlink">
		<a href = "http://imenus.tk/index.php/register/">Register</a>
	</div>
	
</div>
</body>
</html>