<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */
?>



<html>
<head>
	<title><?php echo $title ?></title>
	<link rel='stylesheet' type='text/css' media='all' href='<?php echo site_url('../stylesheet.css');?>' />
	<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'> 

    <?php if (isset($include_css) && is_array($include_css))
            foreach ($include_css as $css)
                echo "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
        ?>
</head>

<body onload = "load()">

<div id = "outer-wrapper">
<div id = "header">
	<h2><a href = "http://imenus.tk/">iMenus</a></h2><br/>
	<h3>Restaurant Menu Designer</h3>
</div>


<div id = "sidebar">
	<div id = "sidebaritem">
		<a href = "<?php echo site_url('designer/selectTheme');?>">Themes</a><br/>
	</div>
	<div id = "sidebaritem">
		<a href = "<?php echo site_url('categories');?>">Categories</a><br/>
	</div>
	<div id = "sidebaritem">
		<a href = "<?php echo site_url('user/invitekey');?>">Request InviteKey</a><br/>
	</div>
	<div id = "sidebaritem">
		<a href = "<?php echo site_url('user/logout');?>">Logout</a><br/>
	</div>
</div>