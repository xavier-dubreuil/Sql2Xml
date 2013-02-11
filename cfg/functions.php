<?php

function line($start, $field)
{
    $array = preg_split('/$\R?^/m', strip_tags($field));
    foreach ($array as $item) {
        if (!strncmp($start, $item, strlen($start))) {
            return trim(substr($item, strpos($item, ':')+1));
        }
    }
    return null;
}

function genre($field)
{
    $array = preg_split('/$\R?^/m', strip_tags($field));
    foreach ($array as $item) {
        if (!strncmp('Genre', $item, 5) || !strncmp('Type', $item, 4)) {
            $value = trim(substr($item, strpos($item, ':')+1));
            if (strpos($value, ',') === false) {
                return $value;
            }
        }
    }
    return null;
}

function sousgenre($field)
{
    $array = preg_split('/$\R?^/m', strip_tags($field));
    foreach ($array as $item) {
        if (!strncmp('Genre', $item, 5) || !strncmp('Sous-genre', $item, 10)) {
            $value = trim(substr($item, strpos($item, ':')+1));
            if (strpos($value, ',') !== false) {
                return $value;
            }
        }
    }
    return null;
}

?>
