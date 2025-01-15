<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class Web extends RestController {

	function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}
		$this->load->library(['form_validation']);
		$this->load->helper(['url','common_helper','security']);
		$this->load->model(['Authentication_model']);
		$this->load->model('UserModel');
	}
	public function token_get(){
		echo password_hash('password', PASSWORD_BCRYPT);
	}
	public function print_get(){
		$this->load->view('print_records');
	}


public function reviewer_search_post()
{
    $this->load->database();
    $this->load->library('form_validation');

    $this->form_validation->set_rules('name', 'Name', 'trim|required');

    if (!$this->form_validation->run()) {
        $this->response([
            'status' => 400,
            'message' => strip_tags(validation_errors()),
        ], RestController::HTTP_BAD_REQUEST);
        return;
    }

    $name = $this->input->post('name', true);

    // Filter reviewers by name
    $this->db->like('name', $name);
    $query = $this->db->get('reviewers');
    $reviewers = $query->result_array();

    // Prepare the response data
    $data = [];
    if (!empty($reviewers)) {
        foreach ($reviewers as $reviewer) {
            $data[] = [
                'id' => $reviewer['id'],
                'name' => $reviewer['name'],
                'email' => $reviewer['email'],
                'contact' => $reviewer['contact'],
            ];
        }
        $this->response([
            'status' => 200,
            'message' => 'Reviewers found.',
            'data' => $data,
        ], RestController::HTTP_OK);
    } else {
        $this->response([
            'status' => 404,
            'message' => 'No reviewers found matching the given name.',
        ], RestController::HTTP_NOT_FOUND);
    }
}


	




}
