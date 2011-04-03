<?php
if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * @author angad
 */
?>
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
		echo '<center><a href = "theme/' . $i .  '">';
		echo '<img id = "image" width = "300px" height = "200px" src = "/themes/' . $i . '.png"/>';
		echo '</a></center>';
		echo '</div><br/>';
	}
?>