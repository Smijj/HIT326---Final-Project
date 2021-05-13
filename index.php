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
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
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