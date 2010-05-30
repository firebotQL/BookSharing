<?php


class Login extends Controller {

    function Login()
    {
            parent::Controller();
    }

    function index() {
        $data = array( 'header_content' => 'login_view/login_header',
                       'site_content' => 'login_view/login_form',
                       'footer_content' => 'login_view/login_footer'
                        );
        $this->load->view('template', $data);
    }

    function validate_credentials() {
        $this->load->model('user_model');
        $this->load->model('forum_model');
        $query = $this->user_model->validate();
        if ($query->num_rows() > 0 ) {// if the user's credentials validated...
            $user_data = $this->user_model->get_user_data($query->row()->id);
            $admin = $user_data->user_type;
            $data = array(
                'user_id' => $query->row()->id,
                'username' => $this->input->post('username'),
                'is_logged_in' => true,
                'admin' => $admin
            );
            print_r($data);
            $this->session->set_userdata($data);
          //  redirect('site/site_area');
        } else {
            print_r($data);
            $this->index();
        }
    }

    function signup() {
        $data = array( 'header_content' => 'login_view/login_header',
                       'site_content' => 'login_view/signup_form',
                       'footer_content' => 'login_view/login_footer'
                        );
        $this->load->view('template', $data);
    }

    function create_user() {
        $this->load->library('form_validation');
        // field name, error message, validation rules

        $this->form_validation->set_rules('first_name', 'Name', 'trim|required|alpha');
        $this->form_validation->set_rules('second_name', 'Second Name', 'trim|required|alpha');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email|callback_unique_email');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[25]|alpha_numeric|callback_unique_user');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->signup();
        } else {
            $this->load->model('user_model');
            $this->load->model('forum_model');
            $query = $this->user_model->create_user();
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $email = $this->input->post('email_address');
            $query2 = $this->forum_model->register($username, $password, $email);
            if ($query && $query2) {
                $data = array( 'header_content' => 'login_view/login_header',
                               'site_content' => 'login_view/signup_successful',
                               'footer_content' => 'login_view/login_footer'
                                );
                $this->load->view('template', $data);
            } else {
                $this->load->view('login_view/signup_form');
            }
        }
    }
    

    function unique_user($str) {
        $this->load->model('user_model');
        if (!$this->user_model->user_exist_by_name($str))
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('unique_user', "%s {$str} already exist");
            return FALSE;
        }
    }

    function unique_email($str)
    {
        $this->load->model('user_model');
        if (!$this->user_model->exist_user_email($str))
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('unique_email', "%s {$str} already exist");
            return FALSE;
        }
    }
}