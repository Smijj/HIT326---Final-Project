<form action="/login" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" vaule="<?php if (!isset($email)) {echo $email;} ?>">
        <label for="pwd">Password:</label>
        <input type="password" name="pwd" id="pwd">
    </form>