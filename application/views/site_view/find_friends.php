<div id="friend-search-wrapper">
    <h1>Search people & find friends</h1>
    <div id="friend-search">
         <?php
               $form_attributes = array('class' => 'search-form searchform', 'id' => 'friends_search', 'name' => 'search_terms');
               $keyword_data = array('name'=>'keywords',
                             'class'=>'keyword textbox',
                             'value' => 'Type in friends name or nickname',
                             'size' => "15");
               $submit_data = array ('name'  => 'submit','class' => 'button submit');
               echo form_open('friends/search', $form_attributes);
               echo "<p>";
               echo form_input($keyword_data);
               echo form_submit($submit_data, 'Search');
               echo "</p>";
               echo form_close();
        ?>
    </div>
    <div id="friend-search-result">
        
    </div>
</div>