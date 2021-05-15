<?php if (!empty($flash)) { echo "<div class='flash'>{$flash}</div>"; } ?>

<form action="/signin" method="post">
    <?php require PARTIALS."signinForm.php"; ?>
    <input type="submit" value="submit">
</form>