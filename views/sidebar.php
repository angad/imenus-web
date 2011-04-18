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
    <script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
    <script>
         $(document).ready(function() {
            var wcallText = $('#waitercall').text();
            function wcallUpdate() {
              $.get('<?php echo site_url('Kitchen/waiter/getRequestCount');?>?randval='+ Math.random(), function (data) {
                    var numCalls = parseInt(data, 10);
                    if (isNaN(numCalls) || numCalls == 0)
                        $('#waitercall').text(wcallText).removeClass('waiterCalled');
                    else
                        $('#waitercall').text(wcallText + ' (' + numCalls + ')').addClass('waiterCalled');
              });
            };
            wcallUpdate();
         	var waiterRefreshId = setInterval(wcallUpdate, 5000);
           $.ajaxSetup({ cache: false });
        });
    </script> 

    <?php if (isset($include_css) && is_array($include_css))
            foreach ($include_css as $css)
                echo "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
        ?>
</head>

<body onload = "load()">

<div id = "outer-wrapper">
<div id = "header">
	<h2><a href = "http://imenus.tk/"><img src = "http://imenus.tk/images/logo.png"/></a></h2><br/>
	<h3>Restaurant Menu Designer</h3>
</div>


<div id = "sidebar">
	<div class="sidebaritem">
		<a href = "<?php echo site_url('designer/selectTheme');?>">Themes</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('categories');?>">Categories</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('features');?>">Item Features</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('user/invitekey');?>">User Account</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('Kitchen/orders');?>">Orders</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('POS');?>">POS</a><br/>
	</div>
	<div class="sidebaritem">
		<a id="waitercall" href = "<?php echo site_url('Kitchen/waiter');?>">Waiter Call</a><br/>
	</div>
	<div class="sidebaritem">
		<a href = "<?php echo site_url('user/logout');?>">Logout</a><br/>
	</div>
</div>