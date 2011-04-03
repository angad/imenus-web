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
		echo '<a href = "theme/' . $i .  '">';
		echo '<center><img id = "image" src = "/themes/' . $i . '.png"/></center';
		echo '</a>';
		echo '</div>';
	}
	?>
</body>
</html>