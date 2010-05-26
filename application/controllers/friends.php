<?php

class Friends extends Controller
{
    function __construct() {
       parent::Controller();
       $this->is_logged_in();
    }

    function show() {
        $data = array( 'header_content' => 'site_view/site_header',
                       'site_content' => 'site_view/site_area',
                       'footer_content' => 'site_view/site_footer',
                       'main_content' => 'site_view/find_friends',
                       'profile_content' => 'site_view/profile'
                        );
        $data['profile_id'] = $this->session->userdata('user_id');
        $this->load->view('template', $data);
    }

    function search()
    {
        $keyword = $this->input->post('keywords');
        if (!empty($keyword))
        {
            $this->session->set_userdata(array('search-friend' => $keyword));
        }
        $keyword = $this->session->userdata('search-friend');
        $total_search = 0;
        $from = 1;

        if ($this->uri->segment(3) != "")
        {
            $from = (int)$this->uri->segment(3);
        }
        $this->load->model('friend_model');
        $search_result = $this->friend_model->search_friends($keyword, $from - 1, 10);
        $total_search = $this->friend_model->get_search_friend_count($keyword);
        $friend_exist = "";
        if ($search_result->num_rows() > 0)
        {
            foreach($search_result->result() as $row)
            {
                $friend_exist[$row->friend_id] = $this->friend_model->friend_exist(
                    $this->session->userdata('user_id'),
                    $row->friend_id);
            }
        }

        $data['friend_exist'] = $friend_exist;

        $data['search_result'] = $search_result;
        $config['base_url'] = "/friends/search/";
        $config['total_rows'] = $total_search;
        $config['per_page'] = '10';
        $config['num_links'] = '10';
        $config['uri_segment'] = 3;

        $this->pagination->initialize($config);
        $this->load->view('site_view/friend_results', $data);
    }

    function add_friend()
    {
        $friend_id = $this->input->post('friend_id');
        $user_id = $this->session->userdata('user_id');
        $this->model->load('friend_model');
        $this->friend_model->add_friend($friend_id, $user_id);
    }

    function is_logged_in()
    {
        $is_logged_in = $this->session->userdata('is_logged_in');

        if(!isset($is_logged_in) || $is_logged_in != true) {
            echo 'You don\'t have have persmission to access sthis page. <a href="../login">Login</a>';
            die();
        }
    }
}