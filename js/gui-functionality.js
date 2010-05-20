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
       var sUrl = "http://localhost/books/upload";

       $(this).ajaxSubmit({
           url: sUrl,
           type: "POST",
           success: function(data) {
               $("#main").html(data);
           },
           error: function (XMLHttpRequest) {
               alert(XMLHttpRequest);
           }
       });
       return false;
    });

    $('#friends_search').submit(function() {
       var sUrl = "http://localhost/friends/search";

       $(this).ajaxSubmit({
           url: sUrl,
           type: "POST",
           success: function(data) {
               $("#friend-search-result").html(data);
           },
           error: function (XMLHttpRequest) {
               alert(XMLHttpRequest);
           }
       });
       return false;
    });

    $("#friend-search-result a").live('click',function() {
        if (!$(this).hasClass("jid_unique"))
        {
            var link = $(this).attr("href");
            $("#friend-search-result").load(link);
            return false;
        }
        return true;
    });
});
