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

         $this->pagination->initialize($config);


         $data['profile_id'] = $this->session->userdata('user_id');
         $this->load->view('template', $data); 
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
         redirect('books/search/' . $keywords . '/' . $actions . '/' . $item_page );
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
            $this->load->view('template', $data);
        }
    }
        
}
