<?php

/**
 * Sanitises string variables. Returns string clean of html tags and quotes.
 *
 * @param  mixed $str
 * @return string
 */
function sanitise_str($str):string {
    $str = htmlspecialchars($str);  // Converts all tag-like elements into special chars ('<' => '&lt').
    $str = htmlentities($str, ENT_QUOTES, "UTF-8");
    $str = strip_tags($str);        // Removes all tags that are left behind (there should not be any).
    return $str;
}

/**
 * Sanitises int variables. Returns **int 0** if string passed is not an int.
 *
 * @param  string $str 
 * @return int
 */
function sanitise_int($str):int {
    if (preg_match("^[\d]+$", $str)) {
        return intval($str);
    } else {
        return 0;
    }
}