<?php 
/**
 * @author patrick
 */
?>

<script> 
 $(document).ready(function() {
 	var POSrefreshId = setInterval(function() {
      $("#AJAXcontent").load('<?php echo site_url($AJAXUpdate);?>?randval='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});
</script>

<div id = "contentarea">
	<h2 class = "title">Point-of-Sales</h2>

    <?php if (isset($back)) echo anchor($back, '< Back').'<br />';?>

    <div id="AJAXcontent">
    <?php echo $table;?>
    </div>
</div>