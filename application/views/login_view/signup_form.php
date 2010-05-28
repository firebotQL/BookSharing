<div id="singup_form">
    <h1>
        <span class="orange"> Create an Account </span>
    </h1>

    <?php

        echo form_open('login/create_user', array('class' => 'form-signup join-login'));
        echo "<div class='personal_info'>";
            echo "<h4>Personal Information</h4>";
            echo form_label('First name: ', 'l_first_name');
            echo form_input('first_name', set_value('first_name', 'First Name'));
            echo form_label('Second name: ', 'l_second_name');
            echo form_input('second_name', set_value('second_name', 'Second Name'));
            echo form_label('Email address: ', 'l_email_address');
            echo form_input('email_address', set_value('email_address', 'Email Address'));
        echo "</div>";
        echo "<div class='login_info'>";
            echo "<h4>Login Information</h4>";
            echo form_label('Username: ', 'l_username');
            echo form_input('username', set_value('username', 'Username'));
            echo form_label('Password: ', 'l_password');
            echo form_password('password', set_value('password', 'Password'));
            echo form_label('Password: ', 'l_password');
            echo form_password('password2', set_value('password2', 'Password'));
        echo "</div>";
        echo form_submit('submit', 'Create Account');
        echo form_close();
    ?>

    <?php echo validation_errors('<p class="error">');?>
</div>