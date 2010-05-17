<?php

class Books extends Controller {

    function __construct() {
        parent::Controller();
       // $this->is_logged_in();    // TODO: FIX SESSIONS
        $this->load->model('book_model');
    }

    function index()
    {
		$data['page_info'] = array('title'=>'Find Books');
		$this->load->view('find-books', $data);
    }

    function search()
    {
        $keywords = "";
        $action = "";
        $item_page = "";
        
        $keywords = $this->uri->segment(3);
        $action = $this->uri->segment(4);
        $item_page = $this->uri->segment(5) + 1;

        if (empty($keywords))
            $keywords = $this->input->post('keywords');
        if (empty($item_page))
            $item_page = $this->input->post('item_page');
        if (empty($action))
            $action = $this->input->post('action');

        $data['keywords'] = $keywords;
        $data['action'] = $action;
        $data['item_page'] = $item_page;
        $data += array('header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/find_books',
                       'profile_content' => 'site_view/profile'
                      );

        //Checks if search form has been submitted.

        if ($action == 'search')
        {
                //Calls search_amazon method
                $temporary_data = $this->book_model->search($keywords, $item_page, "Medium");
                if ($temporary_data != "response fail" && $temporary_data != "parse fail")
                {
                    $data['book_info'] =  $temporary_data;
                }
                $data['page_info'] = array('title' => 'Results From Amazon Search', 'search-term' => $keywords, 'item-page' => $item_page);

        }
        else
        {
                //This error message and checking could be built up a bit
                $data['page_info'] = array('title' => 'Results From Amazon Search', 'error-message' => 'Please enter in a search term.');
        }

        
         $config['base_url'] = "/books/search/" . $keywords . "/" . $action . "/";
         $config['total_rows'] = isset($data['book_info']) ? $data['book_info']->Items->TotalPages : "";
         $config['per_page'] = '1';
         $config['num_links'] = '10';
         $config['uri_segment'] = '5';

         $exist = array();
         $profile_id = $this->session->userdata('user_id');
         foreach ($data['book_info']->Items->Item as $book)
         {
            $isbn = (string)$book->ItemAttributes->ISBN;
            $cnt_result = $this->book_model->check_book_exist($profile_id, $isbn);
            if ($cnt_result > 0)
            {
               
                $exist += array( $isbn => TRUE);
            }
            else
            {
                $exist += array( $isbn => FALSE);
            }
         }

         $data['exist'] = $exist;
         $this->pagination->initialize($config);


         $data['profile_id'] = $profile_id;
         $this->load->view('template', $data); 
    }

    function show() {
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/books_main',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }

    function add_book() {
         $isbn = $this->input->post('isbn');
         $book_type = $this->input->post('type');
         $keywords = $this->input->post('keywords');
         $actions = $this->input->post('action');
         $item_page = $this->input->post('item_page')-1;
         if (!isset($isbn) || 
             !isset($book_type) ||
             !isset($keywords) ||
             !isset($actions) ||
             !isset($item_page)
             )
            return;

         $user_id = $this->session->userdata('user_id');
         $this->load->model('book_model');
         $result = $this->book_model->add_to_bookshelve($isbn, $user_id, $book_type);
         //redirect('books/search/' . $keywords . '/' . $actions . '/' . $item_page );
    }

    function add_book_a()
    {
        $isbn = $this->input->post('isbn');
        $book_type = $this->input->post('type');

        if (!isset($isbn) ||
            !isset($book_type)
        )
        return;
        
        $user_id = $this->session->userdata('user_id');
        $this->load->model('book_model');
        $result = $this->book_model->add_to_bookshelve($isbn, $user_id, $book_type);

        echo "Already in bookshelve";
    }

    function details()
    {
        $isbn = $this->input->post('isbn');
        if (empty($isbn))
            $isbn = $this->uri->segment(3);

        $page = $this->uri->segment(4);
        $book_data = $this->book_model->search($isbn, "1", "Large");

        if (!empty($book_data))
        {
            //print_r($book_data);

            $data = array('book' => $book_data->Items->Item);

            $this->load->model('comment_model');
            $total_comments = $this->comment_model->get_total("", "1", $isbn);

            $comments = $this->comment_model->get_comments("", $page, 3, "1", $isbn);

            // Pagination for comments
            $config['base_url'] = "/books/details" . $isbn . "/" .$page;
            $config['total_rows'] = $total_comments;
            $config['per_page'] = '3';
            $config['num_links'] = '10';
            $config['uri_segment'] = 3;

            $this->pagination->initialize($config);
            $data['set_2'] = $this->pagination->create_links();
            $data['comments'] = $comments;
            $data['isbn'] = $isbn; 
            $data += array( 'header_content' => 'site_view/site_header',
                           'site_content' => 'site_view/site_area',
                           'footer_content' => 'site_view/site_footer',
                           'main_content' => 'site_view/book_details',
                           'profile_content' => 'site_view/profile'
                    ); 
            $data['profile_id'] = $this->session->userdata('user_id');
            $book_count = $this->book_model->check_book_exist($data['profile_id'], $isbn);
            if ($book_count > 0)
            {
                $data['exist'] = TRUE;
            }
            else
            {
                $data['exist'] = FALSE;
            }
            $this->load->view('template', $data);
        }
    }

    function remove()
    {
        $isbn = $this->input->post('isbn');
        $user_id = $this->session->userdata('user_id');
        $this->load->model('book_model');
        $this->book_model->remove_from_bookshelve($user_id, $isbn);
        redirect('site/mybooks');
    }

    function upload()
    {
        $config['upload_path'] = './images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '158';
        $config['max_height'] = '158';
        $config['encrypt_name'] = TRUE;
        $config['overwrite'] = FALSE;

        $this->load->library('upload', $config);
        
        $book_data = array('title' => $this->input->post('title'),
                            'author' => $this->input->post('author'),
                            'publisher' => $this->input->post('publisher'),
                            'isbn' => $this->input->post('isbn'),
                            'publ_date' => $this->input->post('publ_date'),
                            'pages' => $this->input->post('pages')
        );
        $bUploaded = $this->upload->do_upload('cover');
        $error = array('error' => $this->upload->display_errors());
        $user_data = $this->session->userdata('user_id');

        $this->load->library('form_validation');
        // field name, error message, validation rules

        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('author', 'Author', 'trim|max_length[255]');
        $this->form_validation->set_rules('publisher', 'Publisher', 'trim|max_length[255]');

        $this->form_validation->set_rules('isbn', 'ISBN', 'trim|exact_length[10]');
        $this->form_validation->set_rules('publ_date', 'Publication date', 'trim|max_length[4]|is_natural_no_zero');
        $this->form_validation->set_rules('pages', 'Pages', 'trim|is_natural_no_zero');

        $file_data = $this->upload->data();
           if (!empty($file_data['file_name']))
               $book_data += array('cover' => "/images/" . $file_data['file_name']);

        if ((!$bUploaded &&
            strpos($error['error'],"You did not select a file to upload.") == FALSE) // TODO: Fix this hack :)
            || $this->form_validation->run() == FALSE)
        {
            $error = array('error2' => validation_errors());
            echo $error['error2'];
        }
        else
        {
            $data['user_id'] = $this->session->userdata('user_id');

            $this->load->model('book_model');
            $result = $this->book_model->upload_book($data['user_id'], $book_data);
            if ($result)
            {
                echo "Book uploaded!";
            }
            else
            {
                unlink('.' . $book_data['cover']);
                echo "Book already exists in database.";

            }
        }
    }
        
}
