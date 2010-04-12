<div class="messages_navigation">
    <ul class="messages_menu_list">
            <li><?php echo anchor('site/compose/', 'Compose'); ?></li>
            <?php echo '<br/>'; ?>
            <li><?php echo anchor('site/message_box/', 'Inbox'); ?></li>
            <li><?php echo anchor('site/message_box/2', 'Outbox'); ?></li>
            <li><?php echo anchor('site/message_box/3', 'Trashbox'); ?></li>
    </ul>
</div>

<div class="messages_list">
    <div class="pagination_area">
        <?php echo $this->pagination->create_links(); ?>
    </div>
        <?php echo $this->table->generate(); ?>
</div>



</body>
</html>