<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if (!empty($title)) { echo $title; } ?></title>
</head>
<body>
    <header>
    <?php
        if (!empty($is_auth) && $is_auth === true) {
            $btLocation = "/signout";
            $btName = "sign out";
        } else {
            $btLocation = "/signin";
            $btName = "sign in";
        }
        echo "<div class='login'><a href='{$btLocation}'><input type='button' value='{$btName}'/></a></div>";
    ?>
    </header>
    <?php if (!empty($content)) { require $content; } ?>
</body>
</html>