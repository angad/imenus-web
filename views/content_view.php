<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Content View
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @var         include_css      (optional) Array of CSS to be included 
 * @var         include_scripts  (optional) Array of scripts to be included
 * @var         document_ready   (optional) String to be included in jQuery's $(document).ready
 * @var         title            String for the page title
 * @var         back             (optional) Back-link URL
 * @var         content          HTML content
 * 
 */

require_once 'sidebar.php';
?>
        <script src="http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.1.min.js"></script>
        <script src="<?php echo site_url('../scripts/modals.js');?>"></script>
        <?php
        if (isset($include_scripts) && is_array($include_scripts))
            foreach ($include_scripts as $script)
                echo '<script src="'.$script.'"></script>'."\n";
        ?>
        <script>
            $(document).ready(function() {
                <?php if (!empty($document_ready))
                    echo $document_ready;?>
            });
        </script>
        
        <div id="modalprompt">
            <h2>Wait!</h2>
            <p id="modaltext"></p>
            <span id="modalconfirm" class="simplemodal-close">Continue</span> <span id="modalcancel" class="simplemodal-close">Cancel</span>
        </div>
        
        <div id="contentarea">
            <h2 class = "title"><?php echo $title;?></h2>
            
            <?php if (isset($back)) echo anchor($back, '< Back').br(); echo validation_errors();?>
            <?php echo $content;?>
        </div>
<?php require_once 'footer.php';