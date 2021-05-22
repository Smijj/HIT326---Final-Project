<?php
    if (!empty($flash)) { echo "<div class='flash'>".$flash."</div>"; }
?>

<form action="/signup" method="post">
    <input type="hidden" name="_method" value="PUT"/>
    <input type="hidden" name="token" value="<?php if (!empty($csrf_token)) { echo $csrf_token; } ?>"/>
    <?php require PARTIALS."signupForm.php"; ?>
    <input type="submit" value="submit">
</form>