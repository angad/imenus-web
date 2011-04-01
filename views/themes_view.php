<html>
<head>
	<title>Select a theme</title>
	<style>
	
	#wrapper{
		width: 80%;
		margin-right: auto;
		margin-left: auto;
	}
	
	#selected{
		width: 80%;
		margin-right: auto;
		margin-left: auto;
	}

	#notselected{
		width: 80%;
		margin-right: auto;
		margin-left: auto;
	}
	
	#selected {
		border-style:solid;
		border-width:5px;
	}
	
	#image{
	}
	
	</style>
</head>

<body>
	<div id = "wrapper">
	<?php
	
	for($i=1; $i<4; $i++)
	{
		if($current == $i)
		{
			echo '<div id = "selected">';
			
		}
		else 
		{
			echo '<div id = "notselected">';
		}
		echo '<a href = "designer/theme/' . $i .  '">';
		echo '<center><img id = "image" src = "/themes/' . $i . '.png"/></center';
		echo '</a>';
		echo '</div>';
	}
	?>
	</div>
</body>

</html>