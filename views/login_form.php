<html>
<head>
<title>Login</title>
</head>
<body>
<?php echo validation_errors(); ?>
<?php echo form_open('user\login'); ?>

<p>Username<input type = "text" name = "name" value = "<?php echo set_value('username'); ?>" size = "50" /></p>

<p>Password<input type = "text" name = "name" value = "<?php echo set_value('password'); ?>" size = "50" /></p>

</body>
</html>