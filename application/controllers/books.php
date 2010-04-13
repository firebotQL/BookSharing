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
        $item_page = $this->uri->segment(5);

        if (empty($keywords))
            $keywords = $this->input->post('keywords');
        if (empty($item_page))
            $item_page = $this->input->post('item_page');
        if (empty($action))
            $action = $this->input->post('action');
      
        $data = array('header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/find_books',
                       'profile_content' => 'site_view/profile'
                      );
        //Checks if search form has been submitted.

        if ($action == 'search')
        {
                //Calls search_amazon method
                $temporary_data = $this->book_model->search($keywords, $item_page);
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



         $this->load->view('template', $data); 
    }
        
}
