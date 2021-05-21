$( function() {
    $("#addArticleForm").submit( function(event) {
        event.preventDefault();
        $("#article_content").val($("#article_content").val().replace("\r\n", "<br/>\r\n"));
        var data = $(this).serialize();
        $.post("/addarticle", data,
            function (data, textStatus, jqXHR) {
                if (data != undefined && data.result != undefined && data.html != undefined) {
                    if (data.result == 1) {
                        $(".ArticleForm").css({"background-color":"#429C3E"});
                        $(".ArticleForm").html(data.html);
                        $(".flash").html("");
                    } else {
                        $(".flash").css({"background-color":"#AF3641"});
                        $(".flash").html(data.html);
                    }
                } else {
                    $(".flash").css({"background-color":"#AF3641"});
                    $(".flash").html("An error has occurred. Please try again.");
                }
            },
            "json"
        ).fail( function() {
            $(".flash").css({"background-color":"#AF3641"});
            $(".flash").text("An error has occurred attempting to contact the server. Please try again later.");
        });
    });
});