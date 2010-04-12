<div class="logo">
    <img class="logo_img" src="/images/layout/booksharing_logo.gif">
</div>
<div class="navigation">
    <ul class="siteNav">
        <li>
            <a href ="<?php echo base_url(); ?>site/site_area"> HOME </a>
        </li>
        <li>
            <a href ="<?php echo base_url(); ?>books/show">BOOKS</a>
        </li>
        <li>
            <a href="<?php echo base_url(); ?>friends/show">FRIENDS</a>
        </li>
        <li>
            <a href="<?php echo base_url(); ?>community/show">COMMUNITY</a>
        </li>
    </ul>
</div>
<div class="book_search">
        <?php
           $form_attributes = array('class' => 'search-form', 'id' => 'search-form', 'name' => 'search_terms');
           $hidden_fields = array('item_page' => '1', 'action' => 'search');
           echo form_open('books/search', $form_attributes, $hidden_fields);
           echo "<div>";
           $keyword_data = array('name'=>'keywords',
                                 'class'=>'keyword',
                                 'value' => 'find books by title or author or isbn',
                                 'size' => "15");
           echo form_input($keyword_data);

           $submit_data = array ('name'  => 'submit','class' => 'button submit');
           echo form_submit($submit_data, 'Search books');
           echo "</div>";
           echo form_close();
        ?>
</div>