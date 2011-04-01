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
                $("img.zooming").click(function() {
                    $.modal("<img src='" + $(this).src + "'>", {overlayClose : true, overlayCss: {backgroundColor:"black"}});
                })
            });
        </script>
        
        <h1><?php echo $title;?></h1>
        
        <?php echo $content;?>
</body>

</html>
