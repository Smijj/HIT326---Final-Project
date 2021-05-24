<?php

function sanitise_str($str):string {
    $str = htmlspecialchars($str);  // Converts all tag-like elements into special chars ('<' => '&lt').
    $str = htmlentities($str, ENT_QUOTES, "UTF-8");
    $str = strip_tags($str);        // Removes all tags that are left behind (there should not be any).
    return $str;
}

function sanitise_int($str):int {
    if (preg_match("^[\d]+$", $str)) {
        return intval($str);
    } else {
        return 0;
    }
}