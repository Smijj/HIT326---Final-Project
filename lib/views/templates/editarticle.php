<div class='flash'><?php if (!empty($flash)) { echo "{$flash}"; } ?></div>
    <div class="article_form">
        <form action="#" method="post" id="edit_article_form">
            <input type="hidden" name="_method" value="PUT"/>
            <input type="hidden" name="token" value="<?php if (!empty($csrf_token)) { echo $csrf_token; } ?>"/>
            <?php require PARTIALS."articleForm.php"; ?>
            <input type="submit" value="submit"/>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/scripts/article.js"></script>