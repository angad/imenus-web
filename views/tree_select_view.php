<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Tree Select View
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @var         name            Name of Select 
 * @var         readonly        Whether Select is read
 * @var         selected        Item or Array of Selected Items. If selected is an array, Select Field will be Multi-Select 
 * @var         alleslectable   True if All (instead of just Leaf) items are selectable
 * @var         leaffilter      Filter Leaves by their Type index
 */
?>
<select id="edit-<?php echo $name;?>" name="<?php echo $name;?>"<?php if (is_array($selected)) echo ' multiple="multiple"';?><?php if ($readonly) echo ' disabled="disabled"';?>>
<?php
require_once 'tree_select_view_helper.php';

if (!isset($allselectable))
    $allselectable = FALSE;

if ($allselectable)
    echo '<option value="0">'.ROOT_CATEGORY.'</option>';

foreach ($tree['Data'] as $ID => $subtree)
    optgrouptree($ID, $subtree, $allselectable ? 1 : 0, $selected, isset($leaffilter) ? $leaffilter : NULL, $allselectable);
?>
</select>