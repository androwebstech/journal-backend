<?php
defined('BASEPATH') or exit('No Access');
class Site extends CI_Controller
{   
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
       echo 'Server is up and running';
    }
}