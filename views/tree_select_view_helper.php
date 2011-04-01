<?php

define('LEVELINDENT', 2);

function optgrouptree($key, $tree, $level, $selected) {
    if (!is_array($tree) || !isset($tree['Data'])) {
        echo '<option value="'.$key.'"';
        if ($key == $selected || is_array($selected) && in_array($key, $selected))
            echo ' selected="selected"';
        echo '>'.str_repeat('&nbsp;', $level*LEVELINDENT).(is_array($tree) ? $tree['Name'] : $tree).'</option>';
    } else {
        echo '<optgroup label="'.str_repeat('&nbsp;', $level*LEVELINDENT).$tree['Name'].'">';
        foreach ($tree['Data'] as $ID => $subtree)
            optgrouptree($ID, $subtree, $level + 1, $selected);
    }
}