<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');
/**
 * iMenus Tree Select View Helper
 *
 * @package		iMenus
 * @category	View
 * @author		Patrick
 */

define('LEVELINDENT', 4);

/**
 * Generates the optgroup /option tree structure
 *
 * @access	public
 * @param	int     key
 * @param   array   tree
 * @param   int     level
 * @param   mixed   selected
 * @param   int     leaffilter
 * @param   boolean allselectable
 */
function optgrouptree($key, $tree, $level, $selected, $leaffilter = NULL, $allselectable = FALSE) {
    if ($allselectable || !is_array($tree) || !isset($tree['Data'])) {
        if (!isset($leaffilter, $tree['Type']) || $leaffilter == $tree['Type']) {
            echo '<option value="'.$key.'"';
            if ($key == $selected || is_array($selected) && in_array($key, $selected))
                echo ' selected="selected"';
            echo '>'.str_repeat('&nbsp;', $level*LEVELINDENT).(is_array($tree) ? $tree['Name'] : $tree).'</option>';
        }
    } 
    if (is_array($tree) && isset($tree['Data'])) {
        if (!$allselectable)
            echo '<optgroup label="'.str_repeat('&nbsp;', $level*LEVELINDENT).$tree['Name'].'">';
        foreach ($tree['Data'] as $ID => $subtree)
            optgrouptree($ID, $subtree, $level + 1, $selected, $leaffilter);
    }
}