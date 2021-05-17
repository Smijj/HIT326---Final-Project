<label for="fname">First Name:</label>
<input type="text" name="fname" id="fname" value="<?php if (!empty($fname)) { echo $fname; } ?>" autocomplete="given-name"><br>
<label for="lname">Last name</label>
<input type="text" name="lname" id="lname" value="<?php if (!empty($lname)) { echo $lname; } ?>" autocomplete="family-name"><br>
<label for="email">Email:</label>
<input type="email" name="email" id="email" value="<?php if (!empty($email)) {echo $email;} ?>" autocomplete="email"><br>
<label for="pwd">Password:</label>
<input type="password" name="pwd" id="pwd" autocomplete="new-password"><br>
<label for="pwdConf">Confirm password</label>
<input type="password" name="pwdConf" id="pwdConf" autocomplete="new-password"/><br>
<label for="perm">Permission Level:</label>
<input type="text" name="perm" id="prem" <?php if (isset($db_empty) && $db_empty === true) { echo "value=\"3\" readonly"; } ?>><br>