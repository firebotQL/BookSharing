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

    function search_amazon()
    {
            $keywords = $this->input->post('keywords');
            $item_page = $this->input->post('item_page');
            $action = $this->input->post('action');

            //Checks if search form has been submitted.
            if ($action == 'search')
            {

                    //Calls search_amazon method
                    $data['book_info'] = $this->book_model->search_amazon($keywords, $item_page);
                    $data['page_info'] = array('title' => 'Results From Amazon Search', 'search-term' => $keywords, 'item-page' => $item_page);
            }
            else
            {
                    //This error message and checking could be built up a bit
                    $data['page_info'] = array('title' => 'Results From Amazon Search', 'error-message' => 'Please enter in a search term.');
            }

            //Load the views and pass the data
            //$this->load->view('header', $data);
            $this->load->view('find-books', $data);
    }
        
}
