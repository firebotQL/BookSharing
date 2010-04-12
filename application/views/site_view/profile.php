<div id="profile_menu">
    <h1>MY PROFILE</h1>
    <div class="profile_links">
        <ul class="profileNav">
            <li><?php echo anchor('site/profile', 'Profile'); ?></li>
            <li><?php echo anchor('site/message_box', 'Inbox'); ?></li>
            <li><?php echo anchor('site/mybooks', 'My Bookshelve'); ?></li>
            <li><?php echo anchor('site/myfriends', 'My Friendlist'); ?></li>
            <?php echo '<br/>'; ?>
            <li><?php echo anchor('site/singout', 'Log Out'); ?></li>
        </ul>
    </div>
</div>