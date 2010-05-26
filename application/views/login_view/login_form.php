<div id="login_form">
    <h1><span class="orange">Login</span></h1>
    <?php
        $form_attributes = array('id' => 'jid_f_login',
                                'class' => 'form-login join-login');
        echo form_open('login/validate_credentials',  $form_attributes);
        echo form_label('Username: ', 'l_username');
        echo form_input('username', 'Username');
        echo form_label('Password: ', 'l_password');
        echo form_password('password', 'Password');
        echo form_hidden('login','login');
        echo form_submit('login', 'Login');
        echo anchor('login/signup', 'Sign Up');
        echo form_close();
    ?>
</div>