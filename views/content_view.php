<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');?>
<html>
<head>
        <title><?php echo $title;?></title>
        <style>
            tr:nth-child(odd){
                background-color:#EEE;
            }
            img.zooming {
                max-height: 50px;
                max-width: 50px;
            }
            div.form-item {
                display:block;
            }
            div.form-item label {
                display:block;
                font-weight:bold;
            }
            span.form-required {
                color: #FFAE00;
            }
            #simplemodal-overlay {
                background-color:#000;
            }
            #simplemodal-container {
                background-color:#333;
                border:8px solid #444;
                padding:12px;
            }
            #modalprompt {
                display:none;
                overflow:visible;
                color:#BBB;
            }
            #modalprompt h2 {
                color:#5F87AE;
            }
            .simplemodal-close {
                background-color:#5F87AE;
                color:white;
                cursor:pointer;
                font-variant:small-caps;
                line-height: 150%;
                font-size:16px;
                font-weight:bold;
            }
            #modalcancel.simplemodal-close {
                padding:2px 4px;
            }
            #modalconfirm.simplemodal-close {
                text-decoration:underline;
                padding:2px 4px;
            }
        </style>
        <?php if (isset($include_css) && is_array($include_css))
            foreach ($include_css as $css)
                echo link_tag($css)."\n";
        ?>
</head>

<body>
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
        
        <?php if (isset($back)) echo anchor($back, '< Back').br(2); echo validation_errors();?>
        
        <?php echo $content;?>
</body>

</html>
