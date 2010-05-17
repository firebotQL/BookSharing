$(function() {
    $('.jid_hidden_data').submit(function() {
        var serialized = $(this).serialize();
        var sUrl = "http://localhost/books/add_book_a";
        $.ajax({
            url: sUrl,
            type: "POST",
            data: serialized,
            success: function(data) {
                $(".add_details_book").html(data);
                $(".jid_hidden_data").remove();
            }
        });
        return false;
    });

    $('#jid_f_book_upload').submit(function() {
     //  var serialized = $(this).formSerialize();
       var sUrl = "http://localhost/books/upload";

       $(this).ajaxSubmit({
           url: sUrl,
           type: "POST",
          // data: serialized,
           success: function(data) {
               $(".main_container").html(data);
           },
           error: function (XMLHttpRequest) {
               alert(XMLHttpRequest);
           }
       });
       return false;
    });
});
