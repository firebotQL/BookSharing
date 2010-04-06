<?php

class Site extends Controller {

    function __construct() {
        parent::Controller();
        $this->is_logged_in();
    }
    
    function site_area() {
        $this->load->view('site_area');
    }

    function is_logged_in() {
        $is_logged_in = $this->session->userdata('is_logged_in');

        if(!isset($is_logged_in) || $is_logged_in != true) {
            echo 'You don\'t have have persmission to access sthis page. <a href="../login">Login</a>';
            die();
        }
    }
}