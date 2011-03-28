<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');?>
<html>
<head>
        <title>Categories</title>
</head>

<body>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
        <script src="http://www.appelsiini.net/download/jquery.jeditable.mini.js"></script>
        
        <script>
            $(document).ready(function() {
                $('.edit').editable('<?php echo $uri;?>');
            });
        </script>
        
        <h1>Categories</h1>
        
        <?php echo $table;?>
</body>

</html>
