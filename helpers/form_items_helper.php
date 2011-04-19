<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * iMenus Form Items Helper
 *
 * @package		iMenus
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Patrick
 */

define('LEVELINDENT', 4);

/**
 * iMenus Text Form Item Generator
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @param       string    Name of Text Field
 * @param       string    Label to display with Text Field
 * @param       string    Value of Text Field
 * @param       boolean   (optional) Whether input in Text Field is required
 * @param       boolean   (optional) Whether Text Field is readonly
 * @param       string    (optional) Text to show before Text Field
 * @param       string    (optional) Extra Properties for the Text Field
 * @return      string
 */
function text_item($name, $label, $value, $required = FALSE, $readonly = FALSE, $fieldprefix = '', $extra = '') {
return '<div class="form-item"><label for="edit-'.$name.'">'.htmlspecialchars($label).':'.($required ? ' <span class="form-required" title="This field is required">*</span>' : '').'</label>'.
    $fieldprefix.form_input($name, set_value($name, $value), 'id="edit-'.$name.'"'.($readonly ? ' readonly="readonly"' : '').$extra).'</div>';
}

/**
 * iMenus Textarea Form Item Generator
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @param       string    Name of Textarea Field
 * @param       string    Label to display with Textarea Field
 * @param       string    Value of Textarea Field
 * @param       boolean   (optional) Whether input in Textarea Field is required
 * @param       boolean   (optional) Whether Textarea Field is readonly
 * @param       string    (optional) Text to show before Textarea Field
 * @param       string    (optional) Extra Properties for the Textarea Field
 * @return      string
 */
function textarea_item($name, $label, $value, $required = FALSE, $readonly = FALSE, $fieldprefix = '', $extra = '') {
return '<div class="form-item"><label for="edit-'.$name.'">'.htmlspecialchars($label).':'.($required ? ' <span class="form-required" title="This field is required">*</span>' : '').'</label>'.
    $fieldprefix.form_textarea($name, set_value($name, $value), 'id="edit-'.$name.'"'.($readonly ? ' readonly="readonly"' : '').$extra).'</div>';
}

/**
 * iMenus Select Form Item Generator
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @param       string    Name of Select Field
 * @param       string    Label to display with Select Field
 * @param       array     Values in Select Field
 * @param       mixed     Item or Array of Selected Items. If selected is an array, Select Field will be Multi-Select
 * @param       boolean   (optional) Whether input in Textarea Field is required
 * @param       boolean   (optional) Whether Textarea Field is readonly
 * @param       string    (optional) Text to show before Text Field
 * @param       string    (optional) Extra Properties for the Textarea Field
 * @return      string
 */
function select_item($name, $label, $values, $selected, $required = FALSE, $readonly = FALSE, $fieldprefix = '', $extra = '') {
return '<div class="form-item"><label for="edit-'.$name.'">'.htmlspecialchars($label).':'.($required ? ' <span class="form-required" title="This field is required">*</span>' : '').'</label>'.
    $fieldprefix.form_dropdown($name, $values, $selected, 'id="edit-'.$name.'"'.($readonly ? ' readonly="readonly"' : '').$extra).'</div>';
}


/**
 * iMenus Tree Select Generator
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 * @param       string    Name of Select Field
 * @param       string    Label to display with Select Field
 * @param       array     Tree structure generated by getTreeFromCurrentMenu
 * @param       mixed     Item or Array of Selected Items. If selected is an array, Select Field will be Multi-Select
 * @param       boolean   (optional) Whether input in Select Field is required
 * @param       boolean   (optional) Whether Select Field is readonly
 * @param       boolean   (optional) True if All (instead of just Leaf) items are selectable
 * @param       int       (optional) Filter Leaves by their Type index
 * @param       string    (optional) Extra Properties for the Select Field
 */
function tree_select_item($name, $label, $tree, $selected, $required = FALSE, $readonly = FALSE, $allselectable = FALSE, $leaffilter = NULL, $extra = '') {
$output = '<div class="form-item"><label for="edit-'.$name.'">'.htmlspecialchars($label).':'.($required ? ' <span class="form-required" title="This field is required">*</span>' : '').'</label>
        <select id="edit-'.$name.'" name="'.$name.'"'.(is_array($selected) ? ' multiple="multiple"' : '').($readonly ? ' disabled="disabled"' : '').$extra.'>';

if ($allselectable)
    $output .= '<option value="0" '.set_select($name, 0).'>'.ROOT_CATEGORY.'</option>';

if (is_array($tree))
    foreach ($tree['Data'] as $ID => $subtree)
        $output .= optgrouptree($name, $ID, $subtree, $allselectable ? 1 : 0, $selected, isset($leaffilter) ? $leaffilter : NULL, $allselectable);
    
$output .= '</select></div>';
return $output;
}

/**
 * Generates the optgroup /option tree structure
 *
 * @access	public
 * @param   string  fieldName
 * @param	int     key
 * @param   array   tree
 * @param   int     level
 * @param   mixed   selected
 * @param   int     (optional) leaffilter
 * @param   boolean (optional) allselectable
 */
function optgrouptree($fieldName, $key, $tree, $level, $selected, $leaffilter = NULL, $allselectable = FALSE) {
    $output = '';
    if ($allselectable || !is_array($tree) || !isset($tree['Data'])) {
        if (!isset($leaffilter, $tree['Type']) || $leaffilter == $tree['Type']) {
            $output .= '<option value="'.$key.'"'.set_select($fieldName, $key, ($key == $selected || is_array($selected) && in_array($key, $selected))).'>'.str_repeat('&nbsp;', $level*LEVELINDENT).(is_array($tree) ? htmlspecialchars($tree['Name']) : $tree).'</option>'."\n";
        }
    } 
    if (is_array($tree) && isset($tree['Data'])) {
        if (!$allselectable)
            $output .= '<optgroup label="'.str_repeat('&nbsp;', $level*LEVELINDENT).$tree['Name'].'">'."\n";
        foreach ($tree['Data'] as $ID => $subtree)
            $output .= optgrouptree($fieldName, $ID, $subtree, $level + 1, $selected, $leaffilter);
    }
    return $output;
}