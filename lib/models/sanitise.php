<?php

function sanitise_str($str):string {
    $str = htmlspecialchars($str);  // Converts all tag-like elements into special chars ('<' => '&lt').
    $str = strip_tags($str);        // Removes all tags that are left behind (there should not be any).
    return $str;
}