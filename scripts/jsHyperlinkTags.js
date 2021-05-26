
// To replace using a tags
jQuery(document).ready(function($) {
    $('*[data-href]').on('click', function() {
        window.location = $(this).data("href");
    });

    $('*[data-delete-id]').on('click', function() {
        $('<form action="/delarticle" method="post">' +
        '<input type="hidden" name="_method" value="DELETE"/>' +
        '<input type="hidden" name="id" value="' + $(this).data("delete-id") + '"/>' +
        '</form>')
        .appendTo($(document.body))
        .submit();
    });
});
