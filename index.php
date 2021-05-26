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
    if ($user->is_db_empty()) {
        $app->redirect_to("/signup");
    }
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
    }
    $article = new article();
    $app->set_message("top5articles", $article->article_list(5), true); // Get the top 5 articles.
    $app->render(LAYOUT, "mainpage");                   // Render the mainpage.

});

get("/signin", function($app) {
    $app->force_to_https("/signin");
    $app->set_csrftoken();
    $app->render("blank", "signin");                        // Always render the sign-in page in a black HTML document.
});

post("/signin", function($app) {
    $email = $app->form("email", "email");                  // Get clean email and password from user.
    $pwd = $app->form("pwd");
    $app->set_message("email", ($email) ? $email : "");     // Pass email (if entered) back to sign-in page to display on error.

    if ($email != "" && $pwd != "") {                                   // Check if both variables were given.
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
    $app->force_to_https("/signup");                     // Force user to use https for sensitive messages.

    try {
        $user = new User();                                 // Create new user class.
        $is_auth = $user->is_authenticated();               // get authentication status.

        if ($is_auth === true) {                            // Check if the user is authenticated.
            navbar_init($app, $user, $is_auth);
            if ($app->get_session_message("perm") == 3) {  // Check if the user has the correct permission level to access the page.
                // User is authenticated to level 3 (admin/superuser/boss)
                $app->set_csrftoken();
                $app->render(LAYOUT, "signup");
                exit();
            } else {
                $app->render(LAYOUT, "403");                                // Render the 403: access denied error message.
                exit();                                                     // Ensure that code execution stops here.
            }
        } elseif ($user->is_db_empty() === true) {                          // Check if DB is empty.
            $user->signout();                                               // Ensure all session variables are clear (catches case where DB is emptied while user is logged in).
            $app->set_message("lockperm", true);
            $app->set_message("perm_form", 3);
            $app->set_flash("No users in DB please create an admin now.");  // Display page anyway if there's no users to signin.
            $app->set_csrftoken();
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

put("/signup", function($app) {
    $email = $app->form("email", "email");
    $pwd = $app->form("pwd");
    $pwd_conf = $app->form("pwdConf");
    $fname = $app->form("fname");
    $lname = $app->form("lname");
    $perm = $app->form("perm");

    $app->set_message("email", $email);
    $app->set_message("fname", $fname);
    $app->set_message("lname", $lname);
    $app->set_message("perm_form", $perm);

    
    // Check if Csrf token is valid.
    if (!$app->check_csrftoken($app->form("token"))) {
        $app->set_flash("Invalid Authentication token. Please try again.");
        $app->render(LAYOUT, "signup");
        exit();
    }

    try {

        $user = new User();
        $is_auth = $user->is_authenticated();
        $db_empty = $user->is_db_empty();

        if ($is_auth == true || $db_empty == true) {
            if($db_empty) {
                $is_auth = false;
                $app->set_message("lockperm", true);
                $app->set_message("perm", 3);
            }
            $app->set_csrftoken();
            navbar_init($app, $user, $is_auth);

            if ($email == "" || $pwd == ""  || $pwd_conf == ""  || $fname == ""  || $lname == "" || $perm == "" ) {
                $app->set_flash("Please fill all fields.");
                $app->render(LAYOUT, "signup");
                exit();
            } else {
                if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                    $app->set_flash("Invalid email. Please supply a valid email address.");
                    $app->render(LAYOUT, "signup");
                } elseif (!preg_match("/^[0-3]$/", $perm)) {
                    $app->set_message("perm", "");
                    $app->set_flash("Invalid permission. Please supply a number between 0 and 3.");
                    $app->render(LAYOUT, "signup");
                } elseif ($pwd === $pwd_conf){
                    $user = new User();
                    try {
                        if ($db_empty) { $perm = 3; }                        // Set permission level to 3 regardless of what user submitted (only for first user).
                        $user->registerUser($email, $fname, $lname, $pwd, $perm);
                        $user->signout();
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
    $app->force_to_https("/signin");
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
    $app->force_to_https("/signin");
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
            $app->set_csrftoken();
            $app->render(LAYOUT, "addarticle");
        } else {
            header("HTTP/1.0 403 Access Denied.");
            $app->render(LAYOUT, "403");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash("Database error");
        $app->redirect_to("/");
        exit();
    }
});


//Article creation post/put function: AJAX
put("/addarticle", function($app) {
    // Get User class to access user info.
    $user = new User();
    
    try {
        // Check authorisation.
        $is_auth = $user->is_authenticated();
        // Access to this feature requires permissions.
        if ($is_auth == true) {
            // Get variables from input form and database/session.
            $author_id = $user->get_user_id();
            $app->set_message("author_id", $author_id);
            $title = $app->form("title");
            $keywords = $app->form("keywords");
            $article_content = $app->form("article_content");
            $csrf_token = $app->form("token");

            // Check CSRF tocken.
            if (!$app->check_csrftoken($csrf_token)) {
                // CSRF token failure.
                $app->set_message("result", 0);
                $app->set_message("html", "Invalid authentication token. Please refresh the page.");
                $app->render(NULL, "addarticle.json");
                exit();
            }

            // If the user is not privileged to set the public bool, set to 0 to default to hidden.
            if ($app->get_session_message("perm") >= 2) {
                $public = ($app->form("public") != "") ? 1 : 0; // 'public' is a checkbox and does not return a value if unticked.
            } else {
                $public = 0;
            }

            if ($title == "" || $article_content == "") {
                // An input was left blank.
                $app->set_message("result", 0);
                $app->set_message("html", "Please fill all fields.");
                $app->render(NULL, "addarticle.json");
                exit();
            } else {
                $article = new Article();
                try {
                    // Attempt to register the new article, then send success to user.
                    $article->registerArticle($author_id, $title, $keywords, $article_content, $public);

                    $app->set_message("result", 1);
                    $app->render(NULL, "addarticle.json");
                } catch (Exception $e) {
                    // Something happend, send error to user.
                    $app->set_message("result", 0);
                    $app->set_message("html", "Internal Error: ".$e->getMessage());
                    $app->render(NULL, "addarticle.json");
                }
                exit();
            } 
        } else {
            // Render 403 if user does not have access,
            // this would be consistant with how the server should react for any type of request.
            header("HTTP/1.0 403 Access Denied.");
            $app->render(LAYOUT, "403");
            exit();
        }
    } catch (Exception $e) {
        $app->set_flash($e->getMessage());
        $app->redirect_to("/");
    }
});

//Display Edit Articles List
// get("/editarticleslist", function($app) {
//     $app->force_to_https("/signin");
//     $user = new user();
//     $is_auth = false;

//     try {
//         $is_auth = $user->is_authenticated();
//         $app->set_message("is_auth", $is_auth);
//         if ($is_auth) {
//             //$username = $app->get_session_message("name");
//             //$app->set_message("username", $username);
// 			navbar_init($app, $user, $is_auth);
//             $app->render(LAYOUT, "editarticleslist");
//         } else {
//             $app->set_flash("You are not authorised");
//             $app->redirect_to("/");
//             exit();
//         }
//     } catch (Exception $e) {
//         $app->set_flash("Database error");
//         $app->redirect_to("/");
//         exit();
//     }
// });


get("/articlelist", function($app) {
    $app->force_to_https("/signin");
    
    $article = new Article();
    $user = new User();
    $is_auth = $user->is_authenticated();
    
    navbar_init($app);
    try {
        $article_data = $article->article_list();
        $user_data = $user->get_user();
    } catch (DBException $e) {
        $app->set_flash("Internal DB error: ".$e->getMessage());
        $app->redirect_to("/");
    }

    if ($is_auth == true && $user_data->perm >= 2) {
        $high_level_user = true;
    } else {
        $high_level_user = false;
    }

    if ($article_data !== false) {
        $app->set_message("user_id", $user->get_user_id());
        $app->set_message("article_list", $article_data);
        $app->set_message("high_level_user", $high_level_user);
        $app->render(LAYOUT, "articlelist");
    } else {
        $app->set_flash("Database error");
        $app->redirect_to("/");
        exit();
    }
});
// Deletes an article from the database if the user is permitted to do so.
delete("/delarticle", function($app) {
    // Check user authentication.
    $user = new user();
    $is_auth = $user->is_authenticated();
    navbar_init($app, $user, $is_auth);

    if ($is_auth === true) {
        $id = $app->form("id");

        // Get article data.
        $article = new article();
        $article_data = $article->get_article($id);
        // Check if user attempting to delete the article is privileged to level 2 (editor) or above.
        if ($app->get_session_message("perm") >= 2 || $app->get_session_message("uid") == $article_data->author_id) {
            // Then check if the article id is valid.
            if ($article_data != false) {
                // Attempt to delete the article.
                if ($article->delete_article($id)) {
                    // Successful deleteion of article.
                    $app->set_flash("Successfully removed article: '".$article_data->title."'");
                    $app->redirect_to("/articlelist");
                } else {
                    $app->set_flash("An error occurred while attempting to remove article: '".$article_data->title."'");
                    $app->redirect_to("/articlelist");
                }
            } else {
                $app->set_flash("An error occurred while attempting to remove article: '".$article_data->title."'. The article specified was not found.");
                $app->redirect_to("/articlelist");
            }
        } elseif ($app->get_session_message("perm") < 2) {
            $app->set_flash("Insufficient permissions to remove article: '".$article_data->title."'<br>You need to be above level 2 or the author to delete this article.");
            $app->redirect_to("/articlelist");
        } else {

        }
    } else {
        header("HTTP/1.0 403 Access Denied.");
        $app->render(LAYOUT, "403");
    }
});
// Displays Article data in form for editing.
get("/editarticle/:id;[\d]+", function($app) {
    $app->force_to_https("/editarticle/".$app->route_var("id"));
    // Check user authentication.
    $user = new User();
    try {
        $is_auth = $user->is_authenticated();
        navbar_init($app, $user, $is_auth);
        if ($is_auth == true) {
            // Try to get article data.
            $article = new Article();
            $article_data = $article->get_article($app->route_var("id"));
            // Check if article is valid.
            if ($article_data != false && !empty($article_data)) {
                // Pass every variable separately as utilising the articleForm.php partial.
                foreach ($article_data as $key => $value) {
                    $app->set_message($key, $value);
                }
                $app->set_csrftoken();
                $app->render(LAYOUT, "editarticle");
            } else {
                header("HTTP/1.0 404 Not Found");
                $app->render(LAYOUT, "404");
            }
        } else {
            header("HTTP/1.0 403 Access Denied.");
            $app->render(LAYOUT, "403");
        }
    } catch (Exception $e) {
        $app->set_flash("ERROR: {$e->getMessage()}");
        $app->redirect_to("/");
    }
});

// EditArticle post function: AJAX
post("/editarticle/:id;[\d]+", function($app) {
    // Check user authentication.
    $user = new User();
    $id = $app->route_var("id");
    try {
        $is_auth = $user->is_authenticated();
        navbar_init($app, $user, $is_auth);
        if ($is_auth == true) {
            $title = $app->form("title");
            $keywords = $app->form("keywords");
            $article_content = $app->form("article_content");
            $csrf_token = $app->form("token");

            // If the user is not privileged to set the public bool, set to -1 to keep the same.
            if ($app->get_session_message("perm") >= 2) {
                $public = ($app->form("public") != "") ? 1 : 0; // 'public' is a checkbox and does not return a value if unticked.
            } else {
                $public = -1;
            }

            if (!$app->check_csrftoken($csrf_token)) {
                // CSRF token failure.
                $app->set_message("result", 0);
                $app->set_message("html", "Invalid authentication token. Please refresh the page.");
                $app->render(NULL, "editarticle.json");
                exit();
            }
            if ($title == "" || $article_content == "") {
                $app->set_message("result", 0);
                $app->set_message("html", "Please fill all fields.");
                $app->render(NULL, "editarticle.json");
                exit();
            } else {
                // Attempt to update the article.
                $article = new Article();
                try {
                    $article->update_article($id, $title, $keywords, $article_content, $public);
                    $app->set_message("result", 1);
                    $app->render(NULL, "editarticle.json");
                } catch (Exception $e) {
                    $app->set_message("result", 0);
                    $app->set_message("html", "Internal Error: ".$e->getMessage());
                    $app->render(NULL, "editarticle.json");
                }
                exit();
            } 

        } else {
            $app->render(LAYOUT, "403");
        }
    } catch (Exception $e) {
        $app->set_flash("An internal error has occurred: ".$e->getMessage());
        $app->redirect_to("/");
    }
});
// Display article contense.
get("/article/:id;[\d]+", function($app) {
    $id = $app->route_var('id');
    $app->force_to_https("/article/".$id);
    
    // Get required data.
    $article = new Article();
    $user = new User();
    try {
        $is_auth = $user->is_authenticated();
        
        navbar_init($app);
        $article_data = $article->get_article($id, true);
        $user_data = $user->get_user();
        $user_id = $user->get_user_id();
    } catch (DBException $e) {
        $app->set_flash("Internal DB error: ".$e->getMessage());
        $app->redirect_to("/");
    }
    // check if requested article is valid.
    if ($article_data !== false) {
        // If the article is public then show it
        if ($article_data->public === true) {
            $app->set_message("articles_data", $article_data);
            $app->render(LAYOUT, "article");
        } else {
            // Show the article even if it isn't public IF:
            // The user is authenticated & their permissions are >= 2
            // OR:
            // The user is authenticated & their permissions == 1 & their user_id matches the author_id of the article (meaning they were the author)
            if ($is_auth == true && $user_data->perm >= 2 || $is_auth == true && $user_data->perm == 1 && $user_id == $article_data->author_id) {
                $app->set_message("articles_data", $article_data);
                $app->render(LAYOUT, "article");
            } else {
                $app->render(LAYOUT, "404");
                exit();
            }
        }
    } else {
        $app->render(LAYOUT, "404");
        exit();
    }
});


get("/editAccount", function($app) {
    $user = new User();
    $is_auth = $user->is_authenticated();
    navbar_init($app, $user, $is_auth);
    if ($is_auth == true) {
        $data = $user->get_user();
        foreach ($data as $key => $value) {
            if ($key == "perm") {
                $key = "perm_form";
            }
            $app->set_message($key, $value);
        }
        $app->set_message("lockperm", true);
        $app->set_csrftoken();
        $app->render(LAYOUT, "editAccount");
    }
});

post("/editAccount", function($app) {
    $user = new User();
    $is_auth = $user->is_authenticated();
    navbar_init($app, $user, $is_auth);

    if ($is_auth == true) {
        $email = $app->form("email", "email");
        $pwd = $app->form("pwd");
        $pwd_conf = $app->form("pwdConf");
        $fname = $app->form("fname");
        $lname = $app->form("lname");
        $perm = $app->form("perm");

        $app->set_message("email", $email);
        $app->set_message("fname", $fname);
        $app->set_message("lname", $lname);

        $app->set_message("lockperm", true);
        
        // Check if Csrf token is valid.
        if (!$app->check_csrftoken($app->form("token"))) {
            $app->set_flash("Invalid Authentication token. Please try again.");
            $app->redirect_to("editAccount");
            exit();
        }

        if ($email == "" || $fname == ""  || $lname == "" || $perm == "" ) {
            $app->set_flash("Please fill all fields.");
            $app->redirect_to("editAccount");
            exit();
        } else {
            if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
                $email = "";
                $app->set_flash("Invalid email. Please supply a valid email address.");
                $app->redirect_to("editAccount");
            } elseif ($pwd === $pwd_conf){
                $user = new User();
                try {
                    if ($user->is_db_empty()) { $perm = 3; }                        // Set permission level to 3 regardless of what user submitted (only for first user).
                     if ($user->updateUser($email, $fname, $lname, $pwd, $perm)) {
                        $app->set_flash("Successfully updated account.");
                        $app->redirect_to("editAccount");
                     } else {
                        $app->set_flash("Internal Error. Please try again later.");
                        $app->redirect_to("editAccount");
                     }
                } catch (Exception $e) {
                    $app->set_flash("Error: ".$e->getMessage());
                    $app->redirect_to("editAccount");
                }
                exit();
            } else {
                $app->set_flash("Passwords do not match");
                $app->redirect_to("editAccount");
                exit();
            }
        }

    } else {
        $app->render(LAYOUT, "403");
        exit();
    }
});

// Resolve all other URL cases. (will most likely show 404).
resolve();