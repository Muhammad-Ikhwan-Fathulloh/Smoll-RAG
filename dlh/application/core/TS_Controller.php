<?php

class TS_Security extends CI_Security {

    public function __construct() {
        parent::__construct();
    }

    /* override */
    public function csrf_show_error() {
		show_error('The actison you have requested is not allowed!!');
	}

}

class TS_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

    }
}