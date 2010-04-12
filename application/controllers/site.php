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
        $this->load->view('template', $data);
    }
    
    function message_box() {

        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/message_area',
                       'profile_content' => 'site_view/profile'
                        );

        $this->load->model('message_model');
        $id = $this->session->userdata('user_id');
        $per_page = 10;
        $type_id = "1";
        $nr = "0";
        if ($this->uri->segment(3) != "")
        {
            $type_id = $this->uri->segment(3);
        }

        if ($this->uri->segment(4) != "")
        {
            $nr = $this->uri->segment(4);
        }
        
        $data['message'] = $this->message_model->get_messages($id, $type_id, $per_page, $nr);
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
        }

        $this->table->set_heading(array($user_heading,'Subject','Date'));
        
        if ($data['message']->num_rows() > 0)
        {
            foreach($data['message']->result() as $row)
            {
                $this->table->add_row(array($row->username,
                                         $row->subject,
                                         $row->date_sent));
            }
        }
        
        $config['base_url'] = "/site/message_box/" . $type_id . "/";
        $config['total_rows'] = $data['message']->num_rows();
        $config['per_page'] = $per_page;
        $config['num_links'] = '10';
        $config['uri_segment'] = 5;



        $this->pagination->initialize($config);
        $this->load->view('template', $data); 
    }

    function books() {
         $data = array('header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/inbox_area',
                       'profile_content' => 'site_view/profile'
                        );
        $this->load->view('template', $data);
    } 

    function is_logged_in() {
        $is_logged_in = $this->session->userdata('is_logged_in');

        if(!isset($is_logged_in) || $is_logged_in != true) {
            echo 'You don\'t have have persmission to access sthis page. <a href="../login">Login</a>';
            die();
        } 
    } 
}