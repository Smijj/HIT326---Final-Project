
<style>
    html {height:100%;width:100%;} main{align-items:center;justify-content:center;display:flex;height:100%;width:100%;background-color:rgb(230, 230, 230);} span#errorTitle{font-size:2em;padding-bottom:20px} #error-div{align-items:center;justify-content:center;display:flex;} #error-img{float:left;max-width:90%;} #error-div p{color:black;padding-left:40px;font-size:1em;} inc{color:gray;}
</style>
<main>
    <div id="error-div">
        <a href="index"><img id="error-img" src="/img/logo1.png"></a><br>
        <p>
            <span style="font-size:20px;padding-bottom:20px">Error 403: Access Denied</span><br>
            <?php
                $errormsg = array("Where do you think you're going?", "Halt!", "You shall not pass!", "Back up! Where are you of to now?", "What were you thinking?", "No, sorry can't let you pass.");
                echo "<b>".$errormsg[rand(0, sizeof($errormsg)-1)]."</b>";
            ?>
            <br>This section of the site is either restricted or does not exist. Please leave the abyss and go back to <a href="/">safety</a>.<br>
            <inc>No really you are not welcome here, move on.</inc>
        </p>
    </div>
</main>