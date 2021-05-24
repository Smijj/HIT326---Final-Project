
// To replace using a tags
jQuery(document).ready(function($) {
    $('*[data-href]').on('click', function() {
        window.location = $(this).data("href");
    });

    $('*[data-delete-id]').on('click', function() {
        $('<form action="/delarticle/'+ $(this).data("delete-id") +'" method="post">' +
        '<input type="hidden" name="_method" value="DELETE"/>' +
        '</form>')
        .appendTo($(document.body))
        .submit();
    });
});
