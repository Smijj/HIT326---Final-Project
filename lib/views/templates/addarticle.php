<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <?php if (!empty($flash)) { echo "<div class='flash'>{$flash}</div>"; } ?>

    <form action="/addarticle" method="post">
        <?php require PARTIALS."articleForm.php"; ?>
        <input type="submit" value="submit">
    </form>
 
</body>
</html>