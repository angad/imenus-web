<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */
?>

<div id = "contentarea">
	<?php
	
	for($i=1; $i<3; $i++)
	{
		if($current == $i)
		{
			echo '<div id = "selected">';
			
		}
		else 
		{
			echo '<div id = "notselected">';
		}
		echo '<center><a href = "theme/' . $i .  '">';
		echo '<img id = "image" width = "400px" height = "300px" src = "/themes/' . $i . '.jpg"/>';
		echo '</a></center>';
		echo '</div><br/>';
	}
?>
</div>