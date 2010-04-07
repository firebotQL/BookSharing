<?php

class Books extends Controller {

    function __construct() {
        parent::Controller();
        $this->is_logged_in();
    }
}
