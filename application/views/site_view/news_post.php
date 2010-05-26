<div class="compose_news">
    <div class="news_heading">
        <?php
            $textarea_init = array('name' => 'content',
                 'cols' => '60',
                 'value' => 'Type in your news content');
            echo form_open('site/send_news');
            echo form_label('News header: ', 'l_header');
            echo form_input('header', set_value('header', 'Type in header'));
            echo form_label('News content: ', 'l_content');
            echo form_textarea($textarea_init);
            echo form_submit('submit', 'Post news');
            echo form_close();
        ?>
    </div>
</div>