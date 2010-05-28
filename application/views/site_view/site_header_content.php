<!-- header starts here -->
<div id="header-wrap">
    <div id="header-content">

        <h1 id="logo"><a href="<?php echo base_url(); ?>site/site_area" title="">Book<span class="orange">Sharing</span></a></h1>
        <h2 id="slogan">"I cannot live without books." - Thomas Jefferson  </h2>

        <div id="book-search">
             <?php
                   $form_attributes = array('class' => 'search-form searchform', 'id' => 'search-form', 'name' => 'search_terms');
                   $hidden_fields = array('item_page' => '1', 'action' => 'search');
                   $keyword_data = array('name'=>'keywords',
                                 'class'=>'keyword textbox',
                                 'value' => 'find books by title or author or isbn',
                                 'size' => "15");
                   $submit_data = array ('name'  => 'submit','class' => 'button submit');
                   echo form_open('books/search', $form_attributes, $hidden_fields);
                   echo "<p>";
                   echo form_input($keyword_data);
                   echo form_submit($submit_data, 'Search');
                   echo "</p>";
                   echo form_close();
            ?>
        </div>

        <!-- Menu Tabs -->
        <ul>
                <li>
                    <a href="<?php echo base_url(); ?>site/site_area" id="current">News</a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>books/show">Upload books</a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>friends/show">Find friends</a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>community/show" class="jid_s_and_d">Share & Discuss</a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?>about/show">About</a>
                </li>
        </ul>

    </div>
</div>