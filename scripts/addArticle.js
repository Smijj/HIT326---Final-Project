$( function() {
    $("#addArticleForm").submit( function(event) {
        event.preventDefault();
        $("#article_content").val($("#article_content").val().replace("\r\n", "<br/>\r\n"));
        var data = $(this).serialize();
        $.post("/addarticle", data,
            function (data, textStatus, jqXHR) {
                if (data != undefined && data.result != undefined && data.html != undefined) {
                    if (data.result == 1) {
                        $("div .ArticleForm").css({"background-color":"#429C3E"});
                        $("div .ArticleForm").text(data.html);
                    } else {
                        $("div .flash").css({"background-color":"#AF3641"});
                        $("div .flash").text(data.html);
                    }
                } else {
                    $("div .ArticleForm").css({"background-color":"#AF3641"});
                    $("div .flash").html("An error has occured. Please try again.");
                }
            },
            "json"
        ).fail( function() {
            $("div .flash").css({"background-color":"#AF3641"});
            $("div .flash").text("An error has occured attempting to contact the server. Please try again later.");
        });
    });
});