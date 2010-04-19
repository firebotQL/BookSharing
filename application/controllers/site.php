<?php

class Site extends Controller {

    function __construct() {
        parent::Controller();
        $this->is_logged_in();
    }
    
    function site_area() {
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/new_books',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }
    
    function message_box() {
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/message_area',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->model('message_model');
        $id = $this->session->userdata('user_id');
        $per_page;
        $type_id = "1";
        $nr = "0";
        $per_page = "10";
        
        $data['type'] = 'inbox';
        if ($this->uri->segment(3) != "")
        {
            $type_id = $this->uri->segment(3);
        }

        if ($this->uri->segment(4) != "")
        {
            $nr = $this->uri->segment(4);
        }
        
        
        $data['message'] = $this->message_model->get_messages($id, $type_id, $nr ,$per_page, FALSE);
        $temp = $this->message_model->get_messages($id, $type_id, $nr ,$per_page, TRUE);
        $totalMessageRows = $temp->num_rows();
        $data['type_id'] = $type_id;
       
        $user_heading = "";
        switch ($type_id)
        {
            case "1":
                $user_heading = 'From';
                break;
            case "2":
                $user_heading = "To";
                break;
            case "3":
                $user_heading = 'From';
                break;
        }

        $this->table->set_heading(array($user_heading,'Subject','Date'));
        
        if ($data['message']->num_rows() > 0)
        {
            foreach($data['message']->result() as $row)
            {
                $username = ($type_id != "1") ? $row->receivername  : $row->sendername ;
                $this->table->add_row(array($username,
                                         $row->subject,
                                         $row->date_sent));
            }
        }
        
        $config['base_url'] = "/site/message_box/" . $type_id . "/";
        $config['total_rows'] = $totalMessageRows;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 4;



        $this->pagination->initialize($config);
        $this->load->view('template', $data); 
    }

    function compose() {
         $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/message_area',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $data['type'] = 'compose';
        $this->load->view('template', $data);
    }

    function books() {
        $data = array('header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/inbox_area',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }
    function send_message() {
        $profile_content = "site_view/profile/" . $this->session->userdata('user_id');
        $this->load->model('message_model');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('receipent', 'Receipent','callback_username_message_check|trim|required|min_length[4]');
        $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/message_area',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        if ($this->form_validation->run() == FALSE)
        {
            $data['type'] = 'compose';
        }
        else
        {
            $sender_id = $this->session->userdata('user_id');
            $receiver_name = $this->input->post('receipent');
            $content = $this->input->post('content');
            $subject = $this->input->post('subject');
            $this->message_model->save_message($sender_id, $receiver_name, $content, $subject);
            redirect('site/message_box');
            return;
        }
        $this->load->view('template', $data);

    }

    function mybooks() {
        $nr = 0;
        $page_nr = 1;

        if ($this->uri->segment(3) != "")
        {
            $page_nr = (int)$this->uri->segment(3) / 10 + 1;
        }
        
        $user_id = $this->session->userdata('user_id');
        $this->load->model('book_model');
        $result = $this->book_model->get_shelve_books($user_id);
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/book_shelve',
                       'profile_content' => 'site_view/profile'
                        );

        $separated = "";
        if($result->num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $search_query[] = $row->isbn;
            }
            $separated = implode("|", $search_query);
        }
        $amazon_result = $this->book_model->search($separated, $page_nr);
        $data['book_list'] = $amazon_result;
        $data['profile_id'] = $this->session->userdata('user_id');

        $config['base_url'] = "/site/mybooks/";
        $config['total_rows'] = $amazon_result->Items->TotalResults;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 3;



        $this->pagination->initialize($config);

        $this->load->view('template', $data);
    }

    function myfriends() {
         $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/dummy',
                       'profile_content' => 'site_view/profile'
                        );
         $data['profile_id'] = $this->session->userdata('user_id');

         $this->load->view('template', $data);
    }

    function profile() {
        $user_id;
        $main_content;
        $page_nr = 1;
        $data = array();
        if ($this->uri->segment(3) != "")
        {
            $user_id = $this->uri->segment(3);
            $main_content = "site_view/profile_view";
        }
        else
        {
            $data['error_message'] = "0";
            $main_content = "site_view/error";
        }

        if ($this->uri->segment(4) != "")
        {
            $page_nr = (int)$this->uri->segment(3) / 10 + 1;
        }
        
        $this->load->model('user_model');
        $user = $this->user_model->user_exist_by_id($user_id);
        $user_name = "";
        if ($user == NULL)
        {
            $data['error_message'] = "1";
            $main_content = 'site_view/error';
        }
        else
        {
            $user_name = $user->row()->username;
            $data['user_data'] = $this->user_model->get_user_data($user_id);
        }

        $data += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => $main_content,
                       'profile_content' => 'site_view/profile'
                        );

        /* Book retrieval part */
        $this->load->model('book_model');
        $result = $this->book_model->get_shelve_books($user_id);
        $separated = "";
        if($result->num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $search_query[] = $row->isbn;
            }
            $separated = implode("|", $search_query);
        }
        $amazon_result = $this->book_model->search($separated, $page_nr);
        $data['book_list'] = $amazon_result;
        $data['profile_id'] = $this->session->userdata('user_id');

        $config['base_url'] = "/site/profile/" .$this->session->userdata('user_id') . "/" ;
        $config['total_rows'] = $amazon_result->Items->TotalResults;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 4;



        $this->pagination->initialize($config);
        $data['user_name'] = $user_name;
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect('login/index');
    }

    function username_message_check($username) {
        $this->load->model('user_model');
        return $this->user_model->user_exist_by_name($username);
    }

    function is_logged_in() {
        $is_logged_in = $this->session->userdata('is_logged_in');

        if(!isset($is_logged_in) || $is_logged_in != true) {
            echo 'You don\'t have have persmission to access sthis page. <a href="../login">Login</a>';
            die();
        } 
    }


}