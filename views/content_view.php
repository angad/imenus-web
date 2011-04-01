<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');?>
<html>
<head>
        <title><?php echo $title;?></title>
        <style>
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
        </style>
</head>

<body>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
        <script src="http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.1.min.js"></script>
        <?php if (!empty($editable_uri))
            echo '<script src="http://www.appelsiini.net/download/jquery.jeditable.mini.js"></script>';?>
        <script>
            $(document).ready(function() {
                <?php if (!empty($editable_uri))
                    echo '$(".edit").editable("'.$editable_uri.'");'?>
                $("img.zooming").click(function() {
                    $.modal("<img src='" + $(this).src + "'>", {overlayClose : true, overlayCss: {backgroundColor:"black"}});
                })
            });
        </script>
        
        <h1><?php echo $title;?></h1>
        
        <?php echo $content;?>
</body>

</html>
