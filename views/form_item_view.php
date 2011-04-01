<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');?>
<span class="form-item"><label for="edit-<?php echo $name;?>"><?php echo $label;?>: <?php if (isset($required) && $required) echo '<span class="form-required" title="This field is required">*</span>';?></label>
<?php if (isset($html)) {
    echo $html;
    unset ($html);
    }else echo form_input($name, $value);
?></span>