<?php
/* SET to display all warnings in development. Comment next two lines out for production mode*/
ini_set('display_errors','On');
// error_reporting(E_ERROR | E_PARSE);


/************************** CONFIGURE PATHS *************************************/
/* Set the path to the framework folder */
DEFINE("LIB",$_SERVER['DOCUMENT_ROOT']."/lib/");

/* SET VIEW paths */
DEFINE("VIEWS",LIB."views/");
DEFINE("PARTIALS",VIEWS."partials/");

/* set the path to the Model classes folder */
DEFINE("MODELS",LIB."models");

/* Set path to static pages */
DEFINE("STATICPAGES", LIB."static");

/* Path to the Mouse application i.e. the Mouse Framework */
DEFINE("MOUSE",LIB."mouse.php");

/* Define a default layout */
DEFINE("LAYOUT","standard");

/************************ END CONFIGURE PATHS *************************************/

/* Start the Mouse application */
require MOUSE;
require MODELS."/sanitise.php";
require MODELS."/user.php";

/********************** Controller logic below here ********************************/

get("/", function($app) {
    $user = new user();                                     // Create new user class.
    $is_auth = false;
    try {
        $is_auth = $user->is_authenticated();               // Check if current user is authenticated.
        $app->set_message("is_auth", $is_auth);             // Give this variable to the mainpage.
        if ($is_auth) {                                     // Check if the user is authenticated,
            $username = $app->get_session_message("name");  // if so, give the magepage their name.
            $app->set_message("username", $username);
        }
    } catch (Exception $e) {
        $app->set_flash($e->getMessage());                  // Catch any error and display to user as flash.
        $app->render(LAYOUT, "mainpage");                   // Render the magepage.
        exit();                                             // Ensure all code execution stops here.
    }

    $app->render(LAYOUT, "mainpage");                       // Render the mainpage.
});

get("/signin", function($app) {
    $app->render("blank", "signin");                        // Always render the sign-in page in a black HTML document.
});

post("/signin", function($app) {
    $app->force_to_https("/signin");
    $email = $app->form("email", "email");                  // Get clean email and password from user.
    $pwd = $app->form("pwd");
    $app->set_message("email", ($email) ? $email : "");     // Pass email (if entered) back to sign-in page to display on error.
    if ($email && $pwd) {                                   // Check if both variables were given.
        $user = new User();                                 // Create new User class.
        try {
            if ($user->sign_in($email, $pwd)) {             // Try to sign in using provided credentials.
                $app->redirect_to("/");                     // If successful redirect to the mainpage.
            }
        } catch (Exception $e) {
            $app->set_flash("ERROR: {$e->getMessage()}");   // Catch any error and display to user (should be custom error).
            $app->render("blank", "signin");                // Redirect back to sign-in page to display error.
        }
    } else {
        $app->set_flash("Please enter an email and password.");
        $app->render("blank", "signin");                    // Render the sign-in page with empty error displayed.
    }
});

get("/signup", function($app) {
    // $app->force_to_https("/signup");                     // Force user to use https for sensitive messages.

    // try {
    //     $user = new User();                              // Create new user class.
    //     $is_auth = $user->is_authenticated();            // get authentication status.

    //     if ($is_auth === true) {                                             // Check if the user is authenticated.
    //         if ($app->get_session_message("perm") === 3) {                   // Check if the user has the correct permission level to access the page.
    //             // !==== User is authenticated to level two (admin/superuser/boss) ====!
    //             // !==== Place render here once an account has been made. ====!
    //         } else {
    //             $app->set_flash("You do not have access to this feature.");  // Set flash to access denied message.
    //             $app->redirect_to("/");                                      // Redirect to the mainpage.
    //             exit();                                                      // Ensure that code execution stops here.
    //         }
    //     } else {
    //         $app->set_flash("Please log in to access this feature.");        // If user is not logged in redirect to 403 error.
    //         $app->redirect_to(LAYOUT, "404");
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

get("/article/:id", function($app) {

});



// If no valid URL matches are found, let teh application resolve the issue.
resolve();