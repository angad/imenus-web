<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>

<script> 
 $(document).ready(function() {
 	var refreshId = setInterval(function() {
      $("#contentarea").load('<?php echo site_url($AJAXUpdate);?>?randval='+ Math.random());
   }, 9000);
   $.ajaxSetup({ cache: false });
});
</script>

<div id = "contentarea">

    <?php echo $table;?>

</div>