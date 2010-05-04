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
        $user_id_or_isbn = $this->uri->segment(3);
        $comment = $this->input->post('comment_content');
        $type_id = $this->input->post('type_id') ? $this->input->post('type_id') : 0 ;
        if (empty($user_id_or_isbn) || empty($comment))
        {
            return;                 // TODO: MAKE SOME ERROR MESSAGE TO HAPPEN
        }
        $sender_id =  $this->session->userdata('user_id');
        $is_sent = $this->comment_model->send_comment($user_id_or_isbn, $sender_id, $comment, $type_id);
        if ($is_sent)
        {
            switch($type_id)
            {
                case 0:
                    redirect("site/profile/" . $user_id_or_isbn);
                    break;
                case 1:
                    redirect("books/details/" . $user_id_or_isbn);
                    break;
            }
        }
        else
        {
            print_r("Debug: Error");
        }
    }

}