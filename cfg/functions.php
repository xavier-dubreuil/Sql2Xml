<?php

function line($start, $field)
{
    $array = preg_split('/$\R?^/m', strip_tags($field));
    foreach ($array as $item) {
        if (!strncmp($start, $item, strlen($start))) {
            return trim(substr($item, strpos($item, ':')+1));
        }
    }
}

?>
