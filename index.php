<?php
/* SET to display all warnings in development. Comment next two lines out for production mode*/
ini_set('display_errors','On');
error_reporting(E_ERROR | E_PARSE);

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

get("/", function($app) {
    $app->render(LAYOUT, "mainpage");
});

get("/login", function($app){
    $email = $app->form('email');
    $pwd = $app->form('pwd');
    if ($email && $pwd) {
        require MODELS."users.php";
        $user = new Users;
        try {
            if ($user->sign_in($email, $pwd)) {
                $app->render(LAYOUT, "main");
            }
        } catch (Exception $e) {
            $app->set_flash("ERROR: ", $e->getMessage());
            $app->set_message("email", $email);
            $app->render("login", null);
        }
    } else {
        $app->render("blank", "login");
    }
});

get("/signup", function($app) {
    $email = $app->form('email');
    $pwd = $app->form('pwd');
    $pwd_conf = $app->form('pwdConf');
    $fname = $app->form('fname');
    $lname = $app->form('lname');
    $perm = $app->form('perm');
    // ===== Need to add some kind of proper filter maybe: filter_input()?
    if ($app->get_method("get")) {
        $app->render("blank", "signup");
        exit();
    }
    if ( $_SERVER["REQUEST_METHOD"] == "POST"/* && !($email && $pwd && $pwd_conf && $fname && $lname && $perm)*/) {
        $app->set_flash("Please fill all fields.");
        $app->render("blank", "signup");
        exit();
    } else {
        if ($pwd === $pwd_conf){
            $user = new Users;
            try {
                $user->registerUser($email, $fname, $lname, $pwd, $perm);
                $app->set_flash("Success");
                $app->render(LAYOUT, "mainpage");
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
    $app->render("blank", "signup");
});