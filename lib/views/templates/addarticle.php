    <div class='flash'><?php if (!empty($flash)) { echo "{$flash}"; } ?></div>
    <div class="ArticleForm">
        <form action="/addarticle" method="post" id="addArticleForm">
            <?php require PARTIALS."articleForm.php"; ?>
            <input type="submit" value="submit">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/scripts/addArticle.js"></script>