<?php

/**** === Custom Exceptions === ****/
/**
 * Exception handeling Database related errors.
 */
class DBException extends Exception{
    public function __construct($message) {
	    $message = "Internal error 001: ".$message;
        parent::__construct($message, 0, null);
    }
}

/**** === Custom Classes === ****/
class userdata {
    public string $uid;
    public string $fname;
    public string $lname;
    public string $email;
    public string $perm;

    public function __construct($uid = "", $fname = "", $lname = "", $email = "", $perm = "") {
        $this->uid = $uid;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->email = $email;
        $this->perm = $perm;
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
               $query = "SELECT pwd, perm FROM users WHERE user_id=?";
               if($statement = $this->prepare($query)){
                    $binding = array($id);
                    if(!$statement -> execute($binding)){
                    return false;
                    } else {
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        if($result !== false && $result['pwd'] === $hash && $result['perm'] >= 1){
                            $_SESSION['perm'] = $result['perm'];
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
           $query = "SELECT user_id, name FROM users";
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

    public function get_user(): userdata {
        $sql = "SELECT fname, lname, email, perm FROM users WHERE user_id = ?";
        $user_id = (isset($_SESSION["uid"])) ? $_SESSION["uid"] : null;
        if ($user_id == null) {
            return new userdata();
        }
        try {
            if ($stmt = $this->prepare($sql)) {
                if(!$stmt -> execute(array($user_id))){
                    throw new Exception("Could not execute query.");
                } else {
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    return new userdata($user_id, $results['fname'], $results['lname'], $results['email'], $results['perm']);
                }
            } else {
                throw new DBException("Could not prepare statement.");
            }

        } catch (Exception $e) {
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
     * @param int|string $perm permission level
     * @return void returns **true** on success and throws an **Exception** on failure.
     */
    public function registerUser($email, $fname, $lname, $pwd, $perm) {
    
        if (empty($email) || empty($fname) || empty($lname) || empty($pwd) || (empty($perm) && $perm != 0)) {
            throw new Exception('Empty field');
        } else if (!$this->test_pwd($pwd)) {
            throw new Exception('Password should be a minimum of 8 characters with at least one lowercase, uppercase, and special character.');
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
     * Updates the current users information. If password is set to "" (blank), password is not changed.
     * 
     * Throws an exception on error.
     *
     * @param  string $email new email
     * @param  string $fname new first name
     * @param  string $lname new last name
     * @param  string $pwd (optional) new password
     * @return void
     */
    public function updateUser($email, $fname, $lname, $pwd = "") {
        if (empty($email) || empty($fname) || empty($lname)) {
            throw new Exception('Empty field');
        }
        $user_id = $this->get_user_id();
        // Set-up and execute a prepared sql statement to get all users with matching emails.
        $sql = "SELECT user_id FROM users WHERE email=? AND NOT user_id = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute(array($email, $user_id));
        
        // If the count is 1 or more, the email is already in use.
        $count = $stmt->rowCount();
        if ($count > 0) {
            throw new Exception('Username currently in use.');
        } else {
            if ($pwd != "") {
                if (!$this->test_pwd($pwd)) {
                    throw new Exception('Password should be a minimum of 8 characters with at least one lowercase, uppercase, and special character.');
                }
                // Hash password.
                $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET fname = ?, lname = ?, email = ?, pwd = ? WHERE user_id = ?";
                $variables = array($fname, $lname, $email, $pwd_hashed, $user_id);
            } else {
                $sql = "UPDATE users SET fname = ?, lname = ?, email = ? WHERE user_id = ?";
                $variables = array($fname, $lname, $email, $user_id);
            }
            
            // Set-up and execute a prepared sql statement to insert the new user into the database.
            if ($stmt = $this->prepare($sql)) {
                if ($stmt->execute($variables)) {
                    $this->set_authenticated_session($user_id, $fname." ".$lname, $_SESSION["perm"], ($pwd != "") ? $pwd_hashed : $_SESSION["hash"]);
                    return true;
                } else {
                    throw new Exception('Internal error when adding user. Please try again later.');
                }
            } else {
                throw new DBException("Could not perpare sql statement.");
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
        if(!empty($_SESSION["uid"])){
            $id = $_SESSION["uid"];
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
                        $name = $row['fname']." ";
                        if ($row['lname'] != '.') { $name .= $row["lname"]; }           // Add last name if not '.' (meaning the user has no last name).
                        $this->set_authenticated_session($row['user_id'], $name, $row['perm'], $row['pwd']);
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
     * Tests password, returning true if the inputted string constains a minimum of 8 characters with at least one lowercase, uppercase, and special character.
     *
     * @param  string $pwd
     * @return bool
     */
    private function test_pwd($pwd) {
        if (preg_match('/^(?=(?:.*[A-Z]){1,})(?=(?:.*[a-z]){1,})(?=(?:.*[0-9]{1,}))(?=(?:.*[!@#$%^&*()\-__+.]{1,})).{8,}$/', $pwd)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Set an authenticated session and session information.
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
     * Un-sets all session variables then attempts to destory the session.
     * Returns boolean depicting success of destory, and will always unset.
     * @return bool
     */
    function signout(): bool {
        session_start();
        session_unset();
        return session_destroy();
    }

    public function is_db_empty() {
        try {
            $sql = "SELECT user_id FROM users";
            if ($stmt = $this->query($sql)) {
                    // fetchColumn() returns false if the first row returned is nothing, hence empty database.
                    $result = $stmt->fetchColumn(0);
                    if ($result == false) {
                        return true;
                    } else {
                        return false;
                    }

            }
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        }
    }
}