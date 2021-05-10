<?php
// Contains functions that handel user authentication and session management.

// parts adapted from https://www.php.net/manual/en/function.password-hash.php comment by "phpnetcomment201908 at lucb1e dot com"

/**
 * Communication class to send success/error information from modules to the controller.
 * $message = message to be displayed.
 * 
 * $resultCode = int code to depict successfulness (default: 0=error, 1=success).
 */
class result {
    public string $message = '';
    public int $resultCode = 0; // 0=error, 1=success
    
    /**
     * __construct
     *
     * @param  string $message
     * @param  int $resultCode
     * @return void
     */
    public function __construct($message = '', $resultCode = 0) {
        $this->$message = $message;
        $this->$resultCode = $resultCode;
    }
}

/**
 * Registers a new user with the database. Checks for email confliction.
 * Returns a instance of the 'result' class with either an error message to be displayed, or a success message
 * (See the result class for more information).
 *
 * @param  string $email
 * @param  string $fname
 * @param  string $lname
 * @param  string $pwd
 * @param  int $perm
 * @return result class
 * 
 * result->resultCode = 0: error
 * 
 * result->resultCode = 1: Success
 */
function registerUser($email, $fname, $lname, $pwd, $perm): result {
    
    if (empty($email) || empty($fname) || empty($lname) || empty($pwd) || empty($perm)) {
        return new result('Empty field');
    } else if (strlen($pwd) <= 8) {
        return new result('Password should be larger than 8 characters.');
    }

    // Get a valid db connection, else return error.
    require './db.php';
    $dbConn = db_connect();
    if ($dbConn == false) {
        // Database error. Exit function with error.
        return new result("Internal error when connecting to DB. Please try again later.");
    }

    // Set-up and execute a prepared sql statement to get all users with matching emails.
    $sql = "SELECT uid FROM Users WHERE email=?";
    $stmt = $dbConn->prepare($sql);
    $stmt->execute($email);

    // If the count is 1 or more, the email is already in use.
    $count = $stmt->rowCount();
    if ($count > 0) {
        return new result('Username currently in use.');
    } else {
        // Hash password with pepper and salt.
        $pepper = "HufNkGrBiLfVeCfSyu";
        $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
        $pwd_hashed = password_hash($pwd_peppered, PASSWORD_DEFAULT);

        // Set-up and execute a prepared sql statement to insert the new user into the database.
        $sql = "INSERT INTO Users (email, fname, lname, pwd, perm) VALUES (?, ?, ?, ?, ?)";
        $stmt = $dbConn->prepare($sql);
        if ($stmt->execute($email, $fname, $lname, $pwd_hashed, $perm))
            return new result('successfully added user.', 1);
        else
            return new result('Internal error when adding user. Please try again later.');
    }
}

/**
 * Checks the validity of an entered raw username/email and password.
 * Returns a instance of the 'result' class with either an error message to be displayed, or a success message
 * (See the result class for more information).
 * 
 * result->resultCode = 0: error
 * 
 * result->resultCode = 1: Success
 *
 * @param  string $username
 * @param  string $pwd
 * @return result class
 */
function checkLogin($username, $pwd): result {
    // Get a valid db connection, else return error.
    require './db.php';
    $dbConn = db_connect();
    if ($dbConn == false) {
        // Database error. Exit function with error.
        return new result("Internal error when connecting to DB. Please try again later.");
    }

    // Ensure entries are valid.
    if (empty($username) || empty($pwd)) {
        return new result("empty username or password.", 0);
    } else {
        // Get user with entered email.
        $sql = "SELECT user_id, email, pwd, salt, perm FROM Users WHERE email=?";
        $stmt = $dbConn->prepare($sql);
        $stmt->execute($username);

        // only if there is one matching user, continue to check pwd.
        $count = $stmt->rowCount();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($count == 1 && !empty($row)) {
            // Username exists.
            // Check if paswords match.
            $pwd_hashed = $row['pwd'];
            $pepper = "HufNkGrBiLfVeCfSyu";
            $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
            if (password_verify($pwd_peppered, $pwd_hashed)) {
                // Success
                // Assuming session has already started in controller.
                session_start();
                $_SESSION['uid'] = $row['uid'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['perm'] = $row['perm'];
                $_SESSION['LAST_ACTIVITY'] = time();
                session_write_close();
                return new result('', 1);
            } else {
                return new result('Invalid Username or Password');
            }
        } else {
            return new result('Invalid Username or Password');
        }
    }
}


/**
 * Checks if the current session is valid and is not older than the given value.
 * Stops code execution if check fails and redirects to $failRedirect.
 *
 * @param  int $ageSec oldest age of session accepted in seconds.
 * @param  string $failRedirect string URL/URI to redirect user too on session timeout.
 * @return void
 */
function checkSession($ageSec, $failRedirect) {
    if (!empty($SESSION['uid']) && !empty($SESSION['email']) && !empty($SESSION['perm']) && !empty($SESSION['LAST_ACTIVITY'])) {
        if ( (time() - $_SESSION['LAST_ACTIVITY']) > $ageSec) {
            destroyCurrentSession();
            header("Location: ".$failRedirect); // ======= REPLACE WITH FUNC CALL =========
            exit();
        } else {
    
        }
    }
}

/**
 * Unsets all session variables then attempts to destory the session.
 * Returns boolean depicting success of destory not unset.
 * @return bool
 */
function destroyCurrentSession(): bool {
    session_unset();
    return session_destroy();
}