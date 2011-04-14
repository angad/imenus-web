<link rel='stylesheet' type='text/css' media='all' href='<?php echo site_url('../POS.css');?>' />

<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
<script src="http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.1.min.js"></script>
<script src="http://sprintf.googlecode.com/files/sprintf-0.7-beta1.js"></script>
<script src="<?php echo site_url('../scripts/POS.js');?>"></script>
<script src="<?php echo site_url('../scripts/modals.js');?>"></script>

<script>
    handlePOS([5, 10, 25], [5, 10, 25, 50], ['headQty', 'headItem', 'headPrice', 'headAmt', 'headDisc'],
                <?php echo $GSTrate;?>, <?php echo $ServiceCharge;?>, '<?php echo addslashes(htmlspecialchars($Time));?>', '<?php echo addslashes(htmlspecialchars($Name));?>',
                '<?php echo addslashes(htmlspecialchars($Address));?>', <?php echo $Table;?>, '<?php echo addslashes(htmlspecialchars($Remarks));?>');
    $(document).ready(function () {
        $('#print').click(function () {
            window.print();
        }).css('cursor', 'pointer');
    });
</script>

<div id="modalprompt">
    <h2>Wait!</h2>
    <p id="modaltext"></p>
    <span id="modalconfirm" class="simplemodal-close">Continue</span> <span id="modalcancel" class="simplemodal-close">Cancel</span>
</div>

<div id = "contentarea">
    <span id="controls"><h2 class = "title">Bill</h2>
    <a id="print">Print Receipt</a> / <a href="<?php echo site_url('POS/clear/'.$OrderID);?>" class="modalconfirm" data-modaltext="Are you sure you want to Clear this Bill? If you have not printed it, please do so first.">Clear Bill</a></span>

    <?php echo $table;?>

</div>