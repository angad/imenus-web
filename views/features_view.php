<div id = "contentarea">
<?php 

foreach($features as $feature)
{
	echo $feature['Name'] . " " . $feature['Type'] . " " . $feature['MaxValue'] . " " . $feature['Icon'] . " " . $feature['Value'];
	echo "<br/>";
}

?>

</div>