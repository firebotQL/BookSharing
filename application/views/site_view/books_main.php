<script type="text/javascript">
    $(document).ready(function() {
        $('#jid_f_book_upload').ajaxForm();
        add_dynamic_style();
    });
</script>
<div class="main_books">
    <div class="book_upload">
        <h1>Upload your book from here</h1>
        <?php echo isset($error) ? $error : "" ?>
        <?php echo validation_errors('<p class="error">');?>
        <?php
            $form_attributes = array('id' => 'jid_f_book_upload');
            echo form_open_multipart('books/upload', $form_attributes);
            echo form_label('Upload cover', 'l_cover'); echo "<br />";
            echo form_upload('cover', set_value('cover', 'Upload cover')); 
            echo "<p class='jid_required_field'> *  (maximum size 100x100)</p>";
            echo form_label('Title: ', 'l_title');
            echo form_input('title', set_value('title',"Enter title"));
            echo form_label('Author: ', 'l_author');
            echo form_input('author', set_value('author', "Enter author"));
            echo form_label('Publisher: ', 'l_publisher');
            echo form_input('publisher', set_value('publisher', "Enter publisher"));
            echo form_label('ISBN: ', 'l_isbn');
            echo form_input('isbn', set_value('isbn', "Enter ISBN"));
            echo "<p class='jid_required_field'> *  (10-alpha/digit)</p>";
            echo form_label('Publication date: ', 'l_pub_date');
            echo form_input('publ_date', set_value('publ_date', "Publication date"));
            echo "<p class='jid_field_notes'>(e.g. 2009)</p>";
            echo form_label('Pages: ', 'l_pages');
            echo form_input('pages', set_value('pages', "Enter total pages")); echo "<br />";echo "<br />";
            echo form_submit('send', 'Submit book');
            echo form_close();
            echo "<p class='jid_required_field'> * - required fields</p>";
        ?>
    </div>
    <div class="existing_books">

    </div>
</div>