<div class="messages_navigation">
    <ul class="messages_menu_list">
            <li><?php echo anchor('site/compose/', 'Compose'); ?></li>
            <?php echo '<br/>'; ?>
            <li><?php echo anchor('site/message_box/', 'Inbox'); ?></li>
            <li><?php echo anchor('site/message_box/2', 'Outbox'); ?></li>
            <li><?php echo anchor('site/message_box/3', 'Trashbox'); ?></li>
    </ul>
</div>

 <?php echo validation_errors('<p class="error">');?>
<?php if ($type == "inbox"):?>
<div class="messages_list">
    <div class="pagination_area">
        <?php echo $this->pagination->create_links(); ?>
    </div>
        <?php echo $this->table->generate(); ?>
</div>
<?php elseif($type == "compose"): ?>
<div class="compose_message">
    <div class="message_heading">
        <?php
            $textarea_init = array('name' => 'content',
                 'cols' => '60',
                 'value' => 'Here is your message');
            echo form_open('site/send_message');
            echo form_input('receipent', set_value('receipent', 'Receipent user'));
            echo form_input('subject', set_value('subject', 'Subject'));


            echo form_textarea($textarea_init);
            echo form_submit('submit', 'Send');
            echo form_close();
        ?>
    </div>
    <div class="message_content">
    </div>
</div>
<?php endif; ?>


</body>
</html>