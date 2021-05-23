<?php

function navbar_init($app, $user = null, $is_auth = null) {
    if (empty($app)) {
        throw new Exception("Invalid application variable on 'navbar_init' function call.");
    }
    if ($user == null) { $user = new user(); }
    
    if ($is_auth == null) { $is_auth = $user->is_authenticated(); }

    if ($is_auth === true) { $app->set_message("nav_perm", $app->get_session_message("perm")); }

    $app->set_message("is_auth", $is_auth);
    $app->set_message("nav_name", $app->get_session_message("name"));
}