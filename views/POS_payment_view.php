<link rel='stylesheet' type='text/css' media='all' href='<?php echo site_url('../POS.css');?>' />

<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
<script src="http://sprintf.googlecode.com/files/sprintf-0.7-beta1.js"></script>
<script src="<?php echo site_url('../scripts/POS.js');?>"></script>

<script>
    handlePOS([5, 10, 25], [5, 10, 25, 50], ['headQty', 'headItem', 'headPrice', 'headAmt', 'headDisc'],
                <?php echo $GSTrate;?>, <?php echo $ServiceCharge;?>, '<?php echo addslashes(htmlspecialchars($Time));?>', '<?php echo addslashes(htmlspecialchars($Name));?>',
                '<?php echo addslashes(htmlspecialchars($Address));?>', <?php echo $Table;?>, '<?php echo addslashes(htmlspecialchars($Remarks));?>');
</script>

<div id = "contentarea">

    <?php echo $table;?>

</div>