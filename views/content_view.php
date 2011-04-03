<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
require_once 'sidebar.php';
?>
        <script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
        <script src="http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.1.min.js"></script>
        <?php
        if (isset($include_scripts) && is_array($include_scripts))
            foreach ($include_scripts as $script)
                echo '<script src="'.$script.'"></script>'."\n";
        ?>
        <script>
            $(document).ready(function() {
                <?php if (!empty($document_ready))
                    echo $document_ready;?>
                $("a.modalconfirm").click(function(e) {
                    e.preventDefault();
                    $("#modalconfirm").attr('data-href', $(this).attr('href'));
                    $("#modaltext").text($(this).attr('data-modaltext'));
                    $("#modalprompt").modal({
                        overlayClose: true
                    });
                });
                $("#modalconfirm").click(function() {
                    $.modal.close();
                    window.location.href = $(this).attr('data-href');
                })
                $("img.zooming").click(function() {
                    $.modal("<img src='" + $(this).src + "'>", {overlayClose : true, overlayCss: {backgroundColor:"black"}});
                })
            });
        </script>
        
        <div id="modalprompt">
            <h2>Wait!</h2>
            <p id="modaltext"></p>
            <span id="modalconfirm" class="simplemodal-close">Continue</span> <span id="modalcancel" class="simplemodal-close">Cancel</span>
        </div>
        
        <h1><?php echo $title;?></h1>
        
        <?php if (isset($back)) echo anchor($back, '< Back').br(); echo validation_errors();?>
        
        <?php echo $content;?>
</body>

</html>
