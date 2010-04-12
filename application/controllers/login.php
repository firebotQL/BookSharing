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
        $query = $this->user_model->validate();

        if ($query->num_rows() > 0 ) {// if the user's credentials validated...
           
            $data = array(
                'user_id' => $query->row()->id,
                'username' => $this->input->post('username'),
                'is_logged_in' => true
            );
            $this->session->set_userdata($data);
            redirect('site/site_area');
        } else {
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

        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('second_name', 'Second Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->signup();
        } else {
            $this->load->model('user_model');
            $query = $this->user_model->create_user();
            if ($query) {
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
}