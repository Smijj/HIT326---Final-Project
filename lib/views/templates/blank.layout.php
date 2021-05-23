<!DOCTYPE html>
<html lang="en" class="center-html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title></title>
</head>
<body class="center-body">
    <div class="center-parent">
        <div class="center-child">
            <?php if(isset($page_content)) { require $page_content; } ?>
        </div>
    </div>
</body>
</html>