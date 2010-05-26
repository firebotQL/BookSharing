<?php

class Site extends Controller {

    function __construct() {
        parent::Controller();
        $this->is_logged_in();
    }
    
    function site_area() {
        $this->load->model('news_model');
        $this->load->model('comment_model');
        $from = 0;
        $quantity = 3;

        if ($this->uri->segment(3) != "")
        {
            $from = (int)$this->uri->segment(3);
        }

        
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/news_area',
                       'profile_content' => 'site_view/profile'
                        );
        $news_list= $this->news_model->get_news($from, $quantity);
        $comments = array();
        if($news_list->num_rows() > 0)
        {
            foreach($news_list->result() as $row)
            {
                $comments[$row->id] = $comments_count = $this->comment_model->get_comments_count($row->id, "2");
            }
        }
        $data['comments'] = $comments;
        $data['news_list'] = $news_list;
       //  Pagination for news
        $config['base_url'] = "/site/site_area/";
        $config['total_rows'] = $this->news_model->get_total_news();
        $config['per_page'] = '3';
        $config['num_links'] = '10';
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }

    function news_comments()
    {
        $news_id;
        $from = 0;
        $quantity = 5;
        $type_id = 2;
        if ($this->uri->segment(3) != "")
        {
            $news_id = $this->uri->segment(3);
        }
        else
        {
            echo "Please don't trick the system";
            return;
        }

        if ($this->uri->segment(4) != "")
        {
            $from = $this->uri->segment(4);
        }


        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/news_details',
                       'profile_content' => 'site_view/profile'
                        );
        $this->load->model('news_model');
        $this->load->model('comment_model');
        $data['news_id'] = $news_id;
        $data['news_list'] = $this->news_model->get_news_by_id($news_id);

        $data['comments'] = $this->comment_model->get_comments($news_id, $from, $quantity, $type_id);
        $config['base_url'] = "/site/news_comments/" . $news_id . "/";
        $config['total_rows'] = $this->comment_model->get_total($news_id, $type_id);
        $config['per_page'] = '5';
        $config['num_links'] = '10';
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);
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

        $this->table->set_heading(array($user_heading,'Subject','Time'));
        
        if ($data['message']->num_rows() > 0)
        {
            foreach($data['message']->result() as $row)
            {
                $username = ($type_id != "1") ? 
                            anchor(base_url() . "site/profile/" . $row->receiver_id, $row->receivername) :
                            anchor(base_url() . "site/profile/" . $row->sender_id, $row->sendername) ;
                $subject;
                switch ($type_id)
                {
                    case "1":
                        $subject = anchor(base_url() . "site/read_message/" . $row->m_id, $row->subject);
                        break;
                    case "2":
                        $subject = anchor(base_url() . "site/read_message/" . $row->m_id . "/i", $row->subject);
                        break;
                }
                $this->table->add_row(array($username,
                                         $subject,
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
         $username = $this->uri->segment(3);
         $reply_message_id = $this->uri->segment(4);
         $this->load->model('user_model');
         $exist = $this->user_model->user_exist_by_name($username);
         $data = array();
         $user_id = NULL;
         if ($username != "" && $exist)
         {
             $data['username'] = $username;
             $user_id = $this->user_model->get_user_id($username);
         }

         
         if ($reply_message_id != "" && $user_id)
         {
            $this->load->model('message_model');
            $message_result = $this->message_model->get_message($user_id, $reply_message_id, 1);
            if ($message_result->num_rows() > 0)
            {
                $data['reply_subject'] = "Re: " . $message_result->row()->subject;
                $data['reply_content'] = "\n" . $message_result->row()->content;
            }
         }
         
         $data += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/message_area',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $data['type'] = 'compose';
        $this->load->view('template', $data);
    }

    function read_message() {
        if ($this->uri->segment(3) == "")
        {
            return FALSE;
        }
        $type_id = 0;
        if ($this->uri->segment(4) == "i")
        {
            $type_id = 1;
        }

        $user_id = $this->session->userdata('user_id');
        $message_id = $this->uri->segment(3);
        $this->load->model("message_model");
        $data = array();
        $message_result = $this->message_model->get_message($user_id, $message_id, $type_id);
        $data['type_id'] = $type_id;
        if ($message_result->num_rows() > 0)
        {
            $message_data = $message_result->row();
            $data += array('message_text' => $message_data->content,
                           'username' => $message_data->username,
                           'subject' => $message_data->subject,
                           'message_id' => $message_id
            );
            $data += array( 'header_content' => 'site_view/site_header',
                           'site_content' => 'site_view/site_area',
                           'footer_content' => 'site_view/site_footer',
                           'main_content' => 'site_view/message_area',
                           'profile_content' => 'site_view/profile'
                            );
            $data['profile_id'] = $this->session->userdata('user_id');
            $data['type'] = 'read';
            $this->load->view('template', $data);
        }
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
        $amazon_result = NULL;
        if($result->num_rows() > 0)
        {
            foreach ($result->result() as $row)
            {
                $search_query[] = $row->isbn;
            }
            $separated = implode("|", $search_query);
            $amazon_result = $this->book_model->search($separated, $page_nr, "Medium");
        }        
        $data['book_list'] = $amazon_result;
        $data['profile_id'] = $this->session->userdata('user_id');

        $config['base_url'] = "/site/mybooks/";
        $config['total_rows'] = isset($amazon_result->Items->TotalResults) ? $amazon_result->Items->TotalResults : 0;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 3;



        $this->pagination->initialize($config);

        $this->load->view('template', $data);
    }

    function myfriends()
    {
        $total_friends = 0;
        $user_id = $this->session->userdata('user_id');
        $comment_from = 1;
        if ($this->uri->segment(3) != "")
        {
            $comment_from = (int)$this->uri->segment(3);
        }

        $this->load->model('friend_model');
        $data['friends'] = $this->friend_model->get_friend_list($user_id, $comment_from - 1, 10);
        $total_friends = $this->friend_model->get_total_friend_count($user_id);
        $config['base_url'] = "/site/myfriends/";
        $config['total_rows'] = $total_friends;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $data += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/friend_list',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');

        $this->load->view('template', $data);
    }

    function add_friend()
    {
         $friend_id = $this->uri->segment(3);
         $my_id = $this->session->userdata("user_id");
         if ($friend_id == "")
         {
            redirect("site/profile/" . $my_id);
         }

         $this->load->model('user_model');
         $user_exist = $this->user_model->user_exist_by_id($friend_id);

         if (!$user_exist)
         {
             redirect("site/site_area");
         }

         if ($user_exist && $friend_id == $my_id)
         {
            redirect("site/profile/" . $my_id);
         }

         $this->load->model('friend_model');
         $saved = $this->friend_model->add_friend($friend_id, $my_id);
         if($saved)
            redirect("site/profile/" . $friend_id);
         else
            redirect("site/profile/" . $my_id);
    }

    function profile() {
        $user_id;
        $main_content;
        $page_nr = 1;
        $data = array();
        $comment_from = 1;
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
            $page_nr = (int)$this->uri->segment(4) / 10 + 1;
        }

        if ($this->uri->segment(5) != "")
        {
            $comment_from = (int)$this->uri->segment(5);
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
            $amazon_result = $this->book_model->search($separated, $page_nr, "Medium");
        }
        
        $data['book_list'] = isset($amazon_result) ? $amazon_result : NULL;
        $data['profile_id'] = $user_id;

        // Pagination for bookshelve
        $config['base_url'] = "/site/profile/" . $user_id ;
        $config['total_rows'] = isset($amazon_result->Items->TotalResults) ? $amazon_result->Items->TotalResults : 0;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 4;



        $this->pagination->initialize($config);
        $data['set_1'] = $this->pagination->create_links();


        $this->load->model('comment_model');
        $total_comments = $this->comment_model->get_total($user_id, "0");

        $comments = $this->comment_model->get_comments($user_id, $comment_from-1, 3, "0");
        
        // Pagination for comments
        $config['base_url'] = "/site/profile/" . $user_id . "/" . ($page_nr-1)*10 ;
        $config['total_rows'] = $total_comments;
        $config['per_page'] = '3';
        $config['num_links'] = '10';
        $config['uri_segment'] = 5;

        $this->pagination->initialize($config);
        $data['set_2'] = $this->pagination->create_links();
        $data['comments'] = $comments;
        
        $data['user_name'] = $user_name;
        $data['profile_id'] = $this->session->userdata('user_id');
        $data['url_user_id'] = $user_id;
        if ($user_id == $data['profile_id'])
        {
            $data['my_profile'] = TRUE;
        }

        $this->load->model('friend_model');

        $friend_exist = $this->friend_model->friend_exist($data['profile_id'], $user_id);

        if ($friend_exist)
        {
            $data['friend_exist'] = TRUE;
        }
        else
        {
            $data['friend_exist'] = FALSE;
        }

            
        $this->load->view('template', $data);
    }

    function edit_profile() {
         $this->load->model('user_model');
         $user_id = $this->session->userdata('user_id');
         $data['user_data'] = $this->user_model->get_user_data($user_id);
         $data += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/edit_profile',
                       'profile_content' => 'site_view/profile'
                        );
         $data['profile_id'] = $this->session->userdata('user_id');

         $this->load->view('template', $data);
    }

    function save_profile() {
        $config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '158';
        $config['max_height'] = '158';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;

        $this->load->library('upload', $config);

        $user_data = array('first_name' => $this->input->post('first_name'),
                            'second_name' => $this->input->post('second_name'),
                            'email_address' => $this->input->post('email_address'),
                            'country' => $this->input->post('country'),
                            'city' => $this->input->post('city'),
                            'sex' => $this->input->post('sex'),
                            'age' => $this->input->post('age'),
                            'work' => $this->input->post('work'),
                            'education' => $this->input->post('education'),
                            'description' => $this->input->post('description'),
        );
        $bUploaded = $this->upload->do_upload('avatar');
        $error = array('error' => $this->upload->display_errors());

        $this->load->library('form_validation');
        // field name, error message, validation rules

        $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('second_name', 'Second Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');

        $this->form_validation->set_rules('country', 'Country', 'trim|max_length[50]');
        $this->form_validation->set_rules('city', 'City', 'trim|max_length[50]');
        $this->form_validation->set_rules('sex', 'Sex', 'trim|required');
        $this->form_validation->set_rules('age', 'Age', 'trim|required|is_natural_no_zero');
        $this->form_validation->set_rules('work', 'Work', 'trim|max_length[1024]');
        $this->form_validation->set_rules('education', 'Education', 'trim|max_length[1024]');
        $this->form_validation->set_rules('description', 'Description', 'trim|max_length[2056]');


        $file_data = $this->upload->data();
            if (!empty($file_data['file_name']))
                $user_data += array('avatar' => "/images/" . $file_data['file_name']);
        $password = $this->input->post('password');
        if (strlen($password) > 0)
        {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
            $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
        }

        if ((!$bUploaded &&
            strpos($error['error'],"You did not select a file to upload.") == FALSE) // TODO: Fix this hack :)
            || $this->form_validation->run() == FALSE)
        {
            $error['user_data'] = (object)$user_data;
            unlink('.' . $user_data['avatar']);
            $error += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/edit_profile',
                       'profile_content' => 'site_view/profile'
                        );

            $error['profile_id'] = $this->session->userdata('user_id');
            $this->load->view('template', $error); 
        }
        else
        {
            $data['user_id'] = $this->session->userdata('user_id');
            $this->load->model('user_model');
            $this->user_model->update_user_data($data['user_id'], $user_data);
            $this->user_model->update_password($data['user_id'], $password);
            
            $data += array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/profile_edit_successful',
                       'profile_content' => 'site_view/profile'
                        );
            $data['profile_id'] = $this->session->userdata('user_id');

            $this->load->view('template', $data);
        } 
    }

    function post_news()
    {
        $user_id = $this->session->userdata('user_id');
        if (!$this->session->userdata('admin'))
        {
            echo "You must have administrator rights to do this action";
            return;
        }


        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/news_post',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $user_id;

        $this->load->view('template', $data);
    }

    function send_news()
    {
        $user_id = $this->session->userdata('user_id');
        if (!$this->session->userdata('admin'))
        {
            echo "You must have administrator rights to do this action";
            return;
        }

        $header = $this->input->post('header');
        $content = $this->input->post('content');

        $this->load->model('news_model');
        $this->news_model->save_news($user_id, $header, $content);

        redirect('site/site_area');
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

    function is_logged_in()
    {
        $is_logged_in = $this->session->userdata('is_logged_in');

        if(!isset($is_logged_in) || $is_logged_in != true) {
            echo 'You don\'t have have persmission to access sthis page. <a href="../login">Login</a>';
            die();
        } 
    }

    function testing()
    {
        $this->load->model('forum_model');
        $this->forum_model->testing();
    }
}