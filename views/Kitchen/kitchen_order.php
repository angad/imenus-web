<?php 
/**
 * @author angad
 */
?>

<div id = "order">
	<div class = "item_name">
		<?php echo $item_name ?>
	</div>
	<div class = "quantity">
		<?php echo $quantity ?>
	</div>
	<div class = "remarks">
		<?php echo $remarks ?>
	</div>
	<div class = "time">
		<?php echo $time ?>
	</div>
	<div class = "table_number">
		<?php echo $table_number ?>
	</div>
	<div class = "feature">
		<?php 
		if(isset($feature_names))
		{
			foreach($feature_names as $feature_name)
			{
				echo $feature_name;
			}

			foreach($feature_values as $feature_value)
			{
				echo $feature_value;
			}
		}
		else echo "&nbsp;";
		?>
	</div>
	<div class = "start">
		
	</div>
</div>
<br style = "clear:both"/>