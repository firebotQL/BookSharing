<?php

class Friends extends Controller
{
    function __construct() {
       parent::Controller();
       // $this->is_logged_in();    // TODO: FIX SESSIONS
    }

    function show() {
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/dummy',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }

    function add_friend()
    {
        $friend_id = $this->input->post('friend_id');
        $user_id = $this->session->userdata('user_id');
        $this->model->load('friend_model');
        $this->friend_model->add_friend($friend_id, $user_id);
    }
}