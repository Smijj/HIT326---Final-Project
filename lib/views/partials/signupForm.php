<label for="fname">First Name:</label>
<input type="text" name="fname" id="fname" value="<?php if (!empty($fname)) { echo $fname; } ?>"><br>
<label for="lname">Last name</label>
<input type="text" name="lname" id="lname" value="<?php if (!empty($lname)) { echo $lname; } ?>"><br>
<?php include PARTIALS."loginForm.php"; ?>
<label for="pwdConf">Confirm password</label>
<input type="password" name="pwdConf" id="pwdConf"/><br>
<label for="perm">Permission Level:</label>
<input type="text" name="perm" id="prem" value="<?php if (!empty($perm)) { echo $perm; } ?>"><br>