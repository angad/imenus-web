<?php 
/**
 * @author angad
 */
?>


<div id = "waiter">
	<div class = "time">
		<?php echo $time ?>
	</div>
	<div class = "table_number">
		<?php echo $table_number ?>
	</div>
	<div class = "status">
		<?php echo $status ?>
	</div>
	<div id = "button">
		<button type = "button" onclick="removeCall(<?php echo $id ?>)">Clear</button>
	</div>
</div>
<br style = "clear:both"/>