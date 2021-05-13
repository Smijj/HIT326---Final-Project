<form action="/signup" method="post">
    <?php 
        if (!empty($flash)) {
            echo "<div class='flash'>".$flash."</div>";
        }
        require PARTIALS."signupForm.php";
    ?>

    <input type="submit" value="submit">
</form>