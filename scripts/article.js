$( function() {

    $("#add_article_form").submit(function (event) {
        event.preventDefault();                                                 // Stop the automatic submission of thee form.
        var data = $(this).serialize();                                         // Get all of the form data serialised.
        // Post the data to the sever.
        $.post("/addarticle", data,
            function (data, textStatus, jqXHR) {
                if (data != undefined && data.result != undefined && data.html != undefined) {
                    if (data.result == 1) {
                        // Successfully added to the database.
                        $(".article_form").css({"background-color":"#429C3E"});  // Set background colour to green-ish.
                        $(".article_form").html(data.html);                      // Clear form and replace with html from server.
                        $(".flash").html("");                                   // Clear flash.
                    } else {
                        // Error, display message from server.
                        $(".flash").css({"background-color":"#AF3641"});        // Set the flash's background colour to red-ish
                        $(".flash").html(data.html);                            // Display error message from server.
                    }
                } else {
                    // Error, display generic message.
                    $(".flash").css({"background-color":"#AF3641"});            // Set the flash's background colour to red-ish
                    $(".flash").html("An error has occurred. Please try again."); // Display generic error message.
                }
            },
            "json"
        ).fail( function() {
            $(".flash").css({"background-color":"#AF3641"});                    // Set the flash's background colour to red-ish
            $(".flash").text("An error has occurred attempting to contact the server. Please try again later."); // Display generic error message.
        });
    });

    $("#edit_article_form").submit(function (event) {
        event.preventDefault();                                                 // Stop the automatic submission of thee form.
        var data = $(this).serialize();                                         // Get all of the form data serialised.
        // Post the data to the sever.
        $.post($(location).attr("pathname"), data,
            function (data, textStatus, jqXHR) {
                if (data != undefined && data.result != undefined && data.html != undefined) {
                    if (data.result == 1) {
                        // Successfully added to the database.
                        $(".article_form").css({"background-color":"#429C3E"});  // Set background colour to green-ish.
                        $(".article_form").html(data.html);                      // Clear form and replace with html from server.
                        $(".flash").html("");                                   // Clear flash.
                    } else {
                        // Error, display message from server.
                        $(".flash").css({"background-color":"#AF3641"});        // Set the flash's background colour to red-ish
                        $(".flash").html(data.html);                            // Display error message from server.
                    }
                } else {
                    // Error, display generic message.
                    $(".flash").css({"background-color":"#AF3641"});            // Set the flash's background colour to red-ish
                    $(".flash").html("An error has occurred. Please try again."); // Display generic error message.
                }
            },
            "json"
        ).fail( function() {
            $(".flash").css({"background-color":"#AF3641"});                    // Set the flash's background colour to red-ish
            $(".flash").text("An error has occurred attempting to contact the server. Please try again later."); // Display generic error message.
        });
    });
});