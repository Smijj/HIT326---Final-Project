<?php
/* SET to display all warnings in development. Comment next two lines out for production mode*/
ini_set('display_errors','On');
// error_reporting(E_ERROR | E_PARSE);

/* Set the path to the framework folder */
DEFINE("LIB",$_SERVER['DOCUMENT_ROOT']."/lib/");

/* SET VIEW paths */
DEFINE("VIEWS",LIB."views/");
DEFINE("PARTIALS",VIEWS."partials/");

/* set the path to the Model classes folder */
DEFINE("MODELS",LIB."models");

/* Path to the Mouse application i.e. the Mouse Framework */
DEFINE("MOUSE",LIB."mouse.php");

/* Define a default layout */
DEFINE("LAYOUT","standard");

/* Start the Mouse application */
require MOUSE;
require MODELS."/sanitise.php";
require MODELS."/user.php";

get("/", function($app) {
    $user = new user();
    $is_auth = false;
    try {
        $is_auth = $user->is_authenticated();
        $app->set_message("is_auth", $is_auth);
    } catch (Exception $e) {
        $app->set_flash($e->getMessage());
        $app->render(LAYOUT, "mainpage");
        exit();
    }

    $app->render(LAYOUT, "mainpage");
});

get("/signin", function($app){
    $app->render("blank", "signin");
});

post("/signin", function($app) {
    $email = $app->form("email", "email");
    $pwd = $app->form("pwd");
    $app->set_message("email", ($email) ? $email : "");
    if ($email && $pwd) {
        $user = new User();
        try {
            if ($user->sign_in($email, $pwd)) {
                $app->redirect_to("/");
            }
        } catch (Exception $e) {
            $app->set_flash("ERROR: {$e->getMessage()}");
            $app->render("blank", "signin");
        }
    } else {
        $app->render("blank", "signin");
    }
});

get("/signup", function($app) {
    // $app->force_to_https("/signup");

    // try {
    //     $user = new User();
    //     $is_auth = $user->is_authenticated();

    //     if ($is_auth) {
    //         if (!empty($_SESSION["perm"]) && $_SESSION["perm"] == 2) {
    //             // User is authenticated to level two (admin/superuser/boss)
    //             // Place render here once an account has been made.
    //         } else {
    //             $app->set_flash("You do not have access to this feature.");
    //             $app->render(LAYOUT, "mainpage");
    //             exit();
    //         }
    //     } else {
    //         $app->set_flash("Please log in to access this feature.");
    //         $app->render(LAYOUT, "mainpage");
    //         exit();
    //     }
    // } catch (Exception $e) {
    //     $app->set_flash("An internal error has occurred, please contact the system administrators is error posits.");
    //     $app->render(LAYOUT, "mainpage");
    //     exit();
    // }

    $app->render("blank", "signup");
    exit();
});

post("/signup", function($app) {
    try {
        $email = $app->form("email", "email");
        $pwd = $app->form("pwd");
        $pwd_conf = $app->form("pwdConf");
        $fname = $app->form("fname");
        $lname = $app->form("lname");
        $perm = $app->form("perm");
        // ===== Need to add some kind of proper filter maybe: filter_input()?
        $app->set_message("email", ($email != false) ? $email : "");
        $app->set_message("fname", ($fname != false) ? $fname : "");
        $app->set_message("lname", ($lname != false) ? $lname : "");

        if ($email === false || $pwd === false || $pwd_conf === false || $fname === false || $lname === false || $perm === false) {
            $app->set_flash("Please fill all fields.");
            $app->render("blank", "signup");
            exit();
        } else {
            if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                $email = "";
                $app->set_flash("Invalid email. Please supply a valid email address.");
                $app->render("blank", "signup");
            } elseif ($pwd === $pwd_conf){
                $user = new User();
                try {
                    $user->registerUser($email, $fname, $lname, $pwd, $perm);
                    $app->set_flash("Success");
                    $app->redirect_to("/");
                } catch (Exception $e) {
                    $app->set_flash("Error: ".$e->getMessage());
                    $app->render("blank", "signup");
                }
                exit();
            } else {
                $app->set_flash("Passwords do not match");
                $app->render("blank", "signup");
                exit();
            }
        }
    } catch (Exception $e) {
        $app->set_flash($e->getMessage());
        $app->redirect_to("/");
    }
});

get("/signout", function($app) {
    $user = new user();
    if ($user->is_authenticated()) {
        if ($user->signout()) {
            $app->redirect_to("/");
        } else {
            $app->set_flash("An Internal Error has occurred during signout. Please try again later.");
        }
    } else {
        $app->redirect_to("/");
    }
});