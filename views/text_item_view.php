<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Text Form Item View
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @var         name      Name of Form Item 
 * @var         label     Label to display with Form Item
 * @var         required  Whether input in Form Item is required
 * @var         readonly  Whether Form Item is readonly
 */
?>
<div class="form-item"><label for="edit-<?php echo $name;?>"><?php echo $label;?>:<?php if ($required) echo ' <span class="form-required" title="This field is required">*</span>';?></label>
<?php echo form_input($name, $value, 'id="edit-'.$name.'"'.($readonly ? ' readonly="readonly"' : ''));?></div>