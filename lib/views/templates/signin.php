<?php if (!empty($flash)) { echo "<div class='flash'>{$flash}</div>"; } ?>

<h1>Signin</h1>
<form action="/signin" method="post">
    <?php require PARTIALS."signinForm.php"; ?>
    <input type="submit" value="submit">
</form>