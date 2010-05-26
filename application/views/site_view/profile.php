<div id="sidebar">

                <h1>Profile Menu</h1>
                <ul class="sidemenu">
                        <li><?php echo anchor('site/profile/' . $profile_id, 'Profile'); ?></li>
                        <li><?php echo anchor('site/message_box', 'Message box'); ?></li>
                        <li><?php echo anchor('site/mybooks', 'Bookshelve'); ?></li>
                        <li><?php echo anchor('site/myfriends', 'Friendlist'); ?></li>
                        <?php echo '<br/>'; ?>
                        <?php if ($this->session->userdata('admin')) : ?>
                        <li><?php echo anchor('site/post_news', 'Post news'); ?></li>
                        <?php echo '<br />'; ?>
                        <?php endif ?>
                        <li><?php echo anchor('site/logout', 'Log Out'); ?></li>
                </ul>

                <h1>Wise words</h1>

                <p>&quot;No man becomes rich unless he enriches others.&quot;</p>


</div>