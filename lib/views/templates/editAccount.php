<?php
    if (!empty($flash)) { echo "<div class='flash'>".$flash."</div>"; }
?>

<form action="/editAccount" method="post">
    <input type="hidden" name="token" value="<?php if (!empty($csrf_token)) { echo $csrf_token; } ?>"/>
    <?php require PARTIALS."signupForm.php"; ?>
    <input type="submit" value="submit">
</form>