<?php
    if (!empty($flash)) { echo "<div class='flash'>".$flash."</div>"; }
?>

<form action="/signup" method="post">
    <?php require PARTIALS."signupForm.php"; ?>
    <input type="submit" value="submit">
</form>