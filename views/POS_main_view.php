<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>

<script> 
 $(document).ready(function() {
 	var refreshId = setInterval(function() {
      $("#AJAXcontent").load('<?php echo site_url($AJAXUpdate);?>?randval='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});
</script>

<div id = "contentarea">
    <?php if (isset($back)) echo anchor($back, '< Back').'<br />';?>

    <div id="AJAXcontent">
    <?php echo $table;?>
    </div>
</div>