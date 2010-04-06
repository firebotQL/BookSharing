<?php


class Login extends Controller {

    function Login()
    {
            parent::Controller();
    }

    function index() {
        $data['credentials_content'] = 'login_form';
        $this->load->view('includes/template', $data);
    }

    function validate_credentials() {
        $this->load->model('user_model');
        $query = $this->user_model->validate();

        if ($query ) {// if the user's credentials validated...
            $data = array(
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
        $data['credentials_content'] = 'signup_form';
        $this->load->view('includes/template', $data);
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
                $data['credentials_content'] = 'signup_successful';
                $this->load->view('includes/template', $data);
            } else {
                $this->load->view('signup_form');
            }
        }
    }
}