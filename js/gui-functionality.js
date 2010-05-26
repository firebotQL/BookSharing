$(function() {
    $('.jid_hidden_data').submit(function() {
        var serialized = $(this).serialize();
        var sUrl = "http://88.222.153.97/books/add_book_a";
        //var sUrl = "http://localhost/books/add_book_a";
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

    $('#jid_f_book_upload').live('submit',function() {
       //var sUrl = "http://localhost/books/upload";
       var sUrl = "http://88.222.153.97/books/upload";

       $(this).ajaxSubmit({
           url: sUrl,
           type: "POST",
           success: function(data) {
               $("#main").html(data);
               $('#jid_f_book_upload').ajaxForm();
           },
           error: function (XMLHttpRequest) {
               alert(XMLHttpRequest);
           }
       });
       return false;
    });

    $('#friends_search').submit(function() {
       //var sUrl = "http://localhost/friends/search";
       var sUrl = "http://88.222.153.97/books/upload";

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

    $("#jid_f_login").submit(function() {
        //var sUrl = "http://localhost/forum/NinkoBB//register.php";
        var sUrl = "http://88.222.153.97/forum/NinkoBB//register.php";
        var form = $(this);
       form.ajaxSubmit({
           url: sUrl,
           type: "POST",
           success: function(data) {
               //var sUrl = "http://localhost/login/validate_credentials";
               var sUrl = "http://88.222.153.97/login/validate_credentials";
               form.ajaxSubmit({
                   url: sUrl,
                   type: "POST",
                   success: function(data2) {
                       //document.location = "http://localhost/site/site_area";
                       document.location = "http://88.222.153.97/site/site_area";
                   },
                   error: function (XMLHttpRequest) {
                       alert(XMLHttpRequest);
                   }
               });
           },
           error: function (XMLHttpRequest) {
               alert(XMLHttpRequest);
           }
       });
       return false;   
       
    });
    $(".jid_s_and_d").live('click', function() {
        var link = $(this).attr("href");
        $("#main").load(link);
        return false;
    });

});
