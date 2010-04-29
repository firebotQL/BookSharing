<?php

class Comments extends Controller
{
    function __construct()
    {
        parent::Controller();
       // $this->is_logged_in();    // TODO: FIX SESSIONS
       $this->load->model('comment_model');
    }

    function send()
    {
        $user_id = $this->uri->segment(3);
        $comment = $this->input->post('comment_content');

        if (empty($user_id) || empty($comment))
        {
            return;                 // TODO: MAKE SOME ERROR MESSAGE TO HAPPEN
        }
        $sender_id =  $this->session->userdata('user_id');
        $is_sent = $this->comment_model->send_comment($user_id, $sender_id, $comment);
        if ($is_sent)
        {
            redirect("site/profile/" . $user_id);
        }
        else
        {
            print_r("Debug: Error");
        }
    }

}