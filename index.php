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
DEFINE("MODELS",LIB."models/");

/* Path to the Mouse application i.e. the Mouse Framework */
DEFINE("MOUSE",LIB."mouse.php");

/* Define a default layout */
DEFINE("LAYOUT","standard");

/************************ END CONFIGURE PATHS *************************************/

/* Start the Mouse application */
require MOUSE;
require MODELS."sanitise.php";
require MODELS."user.php";
require MODELS."navbar.php";
require MODELS."article.php";

/********************** Controller logic below here ********************************/

get("/", function($app) {
    $user = new user();                                     // Create new user class.
    $is_auth = false;
    try {
        $is_auth = $user->is_authenticated();               // Check if current user is authenticated.
        // $app->set_message("is_auth", $is_auth);             // Give this variable to the mainpage.
        if ($is_auth) {                                     // Check if the user is authenticated,
            navbar_init($app, $user, $is_auth);             // if so, give the mainpage the user's name.
        }
    } catch (DBException $e) {
        $app->set_flash($e->getMessage());                  // Catch any error and display to user as flash.
        exit();                                             // Ensure all code execution stops here.
    } catch (Exception $e) {
        $app->set_flash("Internal Error. Please try again later.");
    } finally {
        $app->render(LAYOUT, "mainpage");                   // Render the mainpage.
    }

});

get("/signin", function($app) {
    $app->render("blank", "signin");                        // Always render the sign-in page in a black HTML document.
});

post("/signin", function($app) {
    // $app->force_to_https("/signin");
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

    try {
        $user = new User();                                 // Create new user class.
        $is_auth = $user->is_authenticated();               // get authentication status.

        if ($is_auth === true) {                            // Check if the user is authenticated.
            if ($app->get_session_message("perm") === 3) {  // Check if the user has the correct permission level to access the page.
                // User is authenticated to level 3 (admin/superuser/boss)
                $app->render(LAYOUT, "signup");
                exit();
            } else {
                $app->render(LAYOUT, "403");                                // Render the 403: access denied error message.
                exit();                                                     // Ensure that code execution stops here.
            }
        } elseif ($user->is_db_empty() === true) {                          // Check if DB is empty.
            $app->set_message("db_empty", true);
            $app->set_flash("No users in DB please create an admin now.");  // Display page anyway if there's no users to signin.
            $app->render(LAYOUT, "signup");
        } else {
            $app->set_flash("Please log in to access this feature.");       // If user is not logged in redirect to 403 error.
            navbar_init($app, $user, $is_auth);
            $app->render(LAYOUT, "404");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash("An internal error has occurred, please contact the system administrators is error posits.");
        $app->render(LAYOUT, "mainpage");
        exit();
    }    
});

post("/signup", function($app) {
    $email = $app->form("email", "email");
    $pwd = $app->form("pwd");
    $pwd_conf = $app->form("pwdConf");
    $fname = $app->form("fname");
    $lname = $app->form("lname");
    $perm = $app->form("perm");

    $app->set_message("email", ($email != false) ? $email : "");
    $app->set_message("fname", ($fname != false) ? $fname : "");
    $app->set_message("lname", ($lname != false) ? $lname : "");
    try {

        $user = new User();
        if ($is_auth = $user->is_authenticated() === true || $db_empty = $user->is_db_empty() === true) {
            navbar_init($app, $user, $is_auth);

            if ($email === false || $pwd === false || $pwd_conf === false || $fname === false || $lname === false || $perm === false) {
                $app->set_flash("Please fill all fields.");
                $app->render(LAYOUT, "signup");
                exit();
            } else {
                if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                    $email = "";
                    $app->set_flash("Invalid email. Please supply a valid email address.");
                    $app->render(LAYOUT, "signup");
                } elseif ($pwd === $pwd_conf){
                    $user = new User();
                    try {
                        if ($user->is_db_empty()) { $perm = 3; }                        // Set permission level to 3 regardless of what user submitted (only for first user).
                        $user->registerUser($email, $fname, $lname, $pwd, $perm);
                        $app->set_flash("Success");
                        $app->redirect_to("/");
                    } catch (Exception $e) {
                        $app->set_flash("Error: ".$e->getMessage());
                        $app->render(LAYOUT, "signup");
                    }
                    exit();
                } else {
                    $app->set_flash("Passwords do not match");
                    $app->render(LAYOUT, "signup");
                    exit();
                }
            }
        } else {
            $app->redirect_to("/");
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


//Article creation get request
get("/addarticle", function($app) {
    $user = new user();
    $is_auth = false;
    $author_id = $user->get_user_id();
    $app->set_message("author_id", $author_id);

    try {
        $is_auth = $user->is_authenticated();
        $app->set_message("is_auth", $is_auth);
        if ($is_auth) {
            // $username = $app->get_session_message("name");
            // $app->set_message("username", $username);
            navbar_init($app, $user, $is_auth);
            $app->render(LAYOUT, "addarticle");
        } else {
            $app->set_flash("You are not authorised");
            $app->redirect_to("/");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash("Database error");
        $app->redirect_to("/");
        exit();
    }
});


//Article creation post function
post("/addarticle", function($app) {
    
    $user = new User();
    $author_id = $user->get_user_id();
    $app->set_message("author_id", $author_id);

    try {
         $is_auth = $user->is_authenticated();

        if ($is_auth == true) {
            // $app->set_message("is_auth", $is_auth);
            // $username = $app->get_session_message("name");
            // $app->set_message("username", $username);

            /* Commented out for AJAX testing */
            // navbar_init($app, $user, $is_auth);
            $title = $app->form("title");
            $keywords = $app->form("keywords");
            $article_content = $app->form("article_content");

            /* Commented out for AJAX testing */
            // $app->set_message("title", ($title != false) ? $title : "");
            // $app->set_message("keywords", ($keywords != false) ? $keywords : "");
            // $app->set_message("article_content", ($article_content != false) ? $article_content : "");


            if ($title === false || $keywords === false || $article_content === false) {
                // $app->set_flash("Please fill all fields.");
                // $app->render(LAYOUT, "addarticle");
                $app->set_message("result", 0);
                $app->set_message("html", "Please fill all fields.");
                $app->render(NULL, "addarticle.json");
                exit();
            } else {
                $article = new Article();
                try {
                    $article->registerArticle($author_id, $title, $keywords, $article_content);
                    // $app->set_flash("Success");
                    // $app->redirect_to("/");
                    $app->set_message("result", 1);
                    $app->render(NULL, "addarticle.json");
                } catch (Exception $e) {
                    // $app->set_flash("Error: ".$e->getMessage());
                    // $app->render(LAYOUT, "addarticle");
                    $app->set_message("result", 0);
                    $app->set_message("html", "Internal Error: ".$e->getMessage());
                    $app->render(NULL, "addarticle.json");
                }
                exit();
            } 
        } else {
            $app->set_flash("You are not authorised");
            $app->redirect_to("/");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash($e->getMessage());
        $app->redirect_to("/");
    }
});

//Display Edit Articles List
get("/editarticleslist", function($app) {
    $user = new user();
    $is_auth = false;

    try {
        $is_auth = $user->is_authenticated();
        $app->set_message("is_auth", $is_auth);
        if ($is_auth) {
            $username = $app->get_session_message("name");
            $app->set_message("username", $username);
            $app->render(LAYOUT, "editarticleslist");
        } else {
            $app->set_flash("You are not authorised");
            $app->redirect_to("/");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash("Database error");
        $app->redirect_to("/");
        exit();
    }
});

get("/article/:id[/d+]", function($app) {

});

// Resolve all other URL cases. (will most likely show 404).
resolve();