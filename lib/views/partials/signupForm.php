
<table class="form_table">
    <tr>
        <td><label for="fname">First Name:</label></td>
        <td><input type="text" name="fname" id="fname" value="<?php if (!empty($fname)) { echo $fname; } ?>" autocomplete="given-name"/></td>
    </tr>
    <tr>
        <td><label for="lname">Last name</label></td>
        <td><input type="text" name="lname" id="lname" value="<?php if (!empty($lname)) { echo $lname; } ?>" autocomplete="family-name"/></td>
    </tr>
    <tr>
        <td><label for="email">Email:</label></td>
        <td><input type="email" name="email" id="email" value="<?php if (!empty($email)) {echo $email;} ?>" autocomplete="email"/></td>
    </tr>
    <tr>
        <td><label for="pwd">Password:</label></td>
        <td><input type="password" name="pwd" id="pwd" autocomplete="new-password"/></td>
    </tr>
    <tr>
        <td><label for="pwdConf">Confirm Password:</label></td>
        <td><input type="password" name="pwdConf" id="pwdConf" autocomplete="new-password"/></td>
    </tr>
    <tr>
        <td><label for="perm">Permission Level:</label></td>
        <td>
        <input type="text" name="perm" id="prem" <?php
                                                    if (!empty($perm_form)) {
                                                        // If the user has entered permission level before, enter it now / enter perset if DB empty.
                                                        echo "value=\"{$perm_form}\"";
                                                    } 
                                                    if (!empty($lockperm) && $lockperm == true) {
                                                        // If the DB is empty disable the user from changing this value.
                                                        echo " readonly";
                                                        }
                                                ?>/>
        </td>
    </tr>
</table>

