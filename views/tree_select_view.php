<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');?>
<select id="edit-<?php echo $name;?>" name="<?php echo $name;?>"<?php if (is_array($selected)) echo ' multiple="multiple"';?><?php if ($readonly) echo ' readonly="readonly';?>>
<?php
print_r($selected);
require_once 'tree_select_view_helper.php';

foreach ($tree['Data'] as $ID => $subtree)
    optgrouptree($ID, $subtree, 0, $selected, isset($leaffilter) ? $leaffilter : NULL);
?>
</select>