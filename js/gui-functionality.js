$(function() {
    $('.add_details_book').click(function() {
        var isbn = $("input[name='jid_hidden_isbn']").val();
        var type = $("input[name='jid_hidden_type']").val();
        var posting = "isbn=" + isbn + "&type=" + type;
        $.ajax({
            url: "/books/add_book_a",
            type: "POST",
            data: posting,
            success: function(data) {
                $(".add_details_book").html(data);
                $(".add_detauls_book").attr('class', 'added');
            }
        })

    });
});
