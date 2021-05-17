<?php

/**** Custom Exceptions ****/
/**
 * Exception handeling Database related errors.
 */
class DBException extends Exception{
    public function __construct($message) {
	    $message = "Internal error 001: ".$message;
        parent::__construct($message, 0, null);
    }
}
/**
 * Exception handeling authentication errors.
 */
class AuthException extends Exception {
    public function __construct($message) {
        $message = "Internal Authentication error: ".$message;
        parent::__construct($message, 0, null);
    }
}

class User extends Database {
    
    /**
     * Checks whether a valid user session is active.
     * May throw an DBException.
     *
     * @return bool returns **true** on success and **false** on failure.
     */
    public function is_authenticated() {
        $id = "";
        $hash="";

        session_start();
        if(!empty($_SESSION["uid"]) && !empty($_SESSION["hash"]) && !empty($_SESSION['perm'])) {
           $id = $_SESSION["uid"];
           $hash = $_SESSION["hash"];
        }
        session_write_close();

        if(!empty($id) && !empty($hash)) {

            try{
               $query = "SELECT pwd FROM users WHERE user_id=?";
               if($statement = $this->prepare($query)){
                    $binding = array($id);
                    if(!$statement -> execute($binding)){
                    return false;
                    } else {
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        if($result !== false && $result['pwd'] === $hash){
                            return true;
                        }
                    }
               }

            }
            catch(Exception $e){
                throw new DBException("Please try again later. {$e->getMessage()}");
            }

        }
        return false;
    }
    
    /**
     * Returns an array of all users.
     *
     * @return mixed returns an **array** if successful and throws an **Exception** on DB failure.
     */
    public function get_users() {
        try {
           $query = "SELECT id, name FROM users";
           if ($statement = $this->prepare($query)) {
                if(!$statement -> execute()){
                    throw new Exception("Could not execute query.");
                } else {
                    $results = $statement->fetchall(PDO::FETCH_ASSOC);
                    return $results;
                }
           } else {
                throw new DBException("Could not prepare statement.");
            }
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Adds a new user to the database.
     *
     * @param string $email email
     * @param string $fname first name
     * @param string $lname last name
     * @param string $pwd password
     * @param int $perm permission level
     * @return void returns **true** on success and throws an **Exception** on failure.
     */
    public function registerUser($email, $fname, $lname, $pwd, $perm) {
    
        if (empty($email) || empty($fname) || empty($lname) || empty($pwd) || empty($perm)) {
            throw new Exception('Empty field');
        } else if (strlen($pwd) <= 8) {
            throw new Exception('Password should be larger than 8 characters.');
        }
    
        // Set-up and execute a prepared sql statement to get all users with matching emails.
        $sql = "SELECT user_id FROM users WHERE email=?";
        $stmt = $this->prepare($sql);
        $stmt->execute(array($email));
    
        // If the count is 1 or more, the email is already in use.
        $count = $stmt->rowCount();
        if ($count > 0) {
            throw new Exception('Username currently in use.');
        } else {
            // Hash password.
            // $pwd_peppered = $this->generate_pepper_hash($pwd);
            $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
    
            // Set-up and execute a prepared sql statement to insert the new user into the database.
            $sql = "INSERT INTO Users (email, fname, lname, pwd, perm) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            if ($stmt->execute(array($email, $fname, $lname, $pwd_hashed, $perm))) {
                return true;
            } else {
                throw new Exception('Internal error when adding user. Please try again later.');
            }
        }
    }
    
    /**
     * Returns the id stored in the $_SESSION array for the current user.
     *
     * @return string empty if none found.
     */
    public function get_user_id(){
        $id="";
        session_start();
        if(!empty($_SESSION["id"])){
            $id = $_SESSION["id"];
        }
        session_write_close();
        return $id;
    }
    
    /**
     * Delete a user with the uid given.
     *
     * @param  string $uid
     * @return void Returns **true** on success and throws and **Exception** on failure.
     */
    public function delete_user($uid) {
        if(empty($uid)){
          throw new Exception("User has no valid id");
        }
        if(!empty($uid) && $uid=="1"){
          throw new Exception("Cannot delete super user!");
        }
    
        try{
           $query = "DELETE FROM users WHERE user_id=?";
           if($statement = $this->prepare($query)) {
              if(!$statement -> execute(array($uid))) {
                    throw new Exception("Could not execute query.");
              } else {
                  return true;
              }
           }
           else {
                throw new Exception("Could not prepare statement.");
           }
        }
        catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Generates hash with sha256 encoding, adding a pepper.
     *
     * @param  string $pwd password
     * @return string Hashed password with pepper added.
     */
    // private function generate_pepper_hash($pwd):string {
    //     // return $pwd;
    //     $pepper = "HufNkGrBiLfVeCfSyu";
    //     return hash_hmac("sha256", $pwd, $pepper);
    // }
    
    /**
     * Validates sign in credentials from user.
     * 
     * @param  string $email
     * @param  string $pwd
     * @return void Returns **true** on success and throws an **exception** on failure.
     */
    public function sign_in($email, $pwd) {
        try{ 
            // Ensure entries are valid.
            if (empty($email) || empty($pwd)) {
                throw new Exception("empty username or password.");
            } else {
                // Get user with entered email.
                $sql = "SELECT user_id, email, fname, lname, pwd, perm FROM users WHERE email=?";
                $stmt = $this->prepare($sql);
                $stmt->execute(array($email));

                // only if there is one matching user, continue to check pwd.
                $count = $stmt->rowCount();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($count == 1 && !empty($row)) {
                    // Username exists.
                    // Check if paswords match.
                    $pwd_hashed = $row['pwd'];
                    // $pwd_peppered = $this->generate_pepper_hash($pwd_hashed);
                    if (password_verify($pwd, $pwd_hashed)) {
                        // Success
                        $this->set_authenticated_session($row['user_id'], $row['fname']." ".$row['lname'], $row['perm'], $row['pwd']);
                        return true;
                    } else {
                        throw new Exception('Invalid Username or Password');
                    }
                } else {
                    throw new Exception('Invalid Username or Password');
                }
            }
        } catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * set an authenticated session and session information.
     *
     * @param  string $uid Unique user identifier
     * @param  string $name Full name
     * @param  string $perm Permission level
     * @param  string $hash Password hash
     * @return void
     */
    private function set_authenticated_session($uid, $name, $perm, $hash) {
        session_start();
        $_SESSION['uid'] = $uid;
        $_SESSION['name'] = $name;
        $_SESSION['hash'] = $hash;
        $_SESSION['perm'] = $perm;
        $_SESSION['LAST_ACTIVITY'] = time();
        session_write_close();
    }

    /**
     * Unsets all session variables then attempts to destory the session.
     * Returns boolean depicting success of destory, and will always unset.
     * @return bool
     */
    function signout(): bool {
        session_start();
        session_unset();
        return session_destroy();
    }

    public function is_db_empty) {
        try {
            $sql = "SELECT user_id FROM users";
            if ($stmt = $this->prepare($sql)) {
                if ($statement->execute()) {

                } else {
                    throw new 
                }
            }
        }
    }
}