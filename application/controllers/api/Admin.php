<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class Admin extends RestController {  
	private $user = [];
	function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}

		$this->load->library('Authorization_Token');
		$this->load->library(['form_validation']);
		$this->load->helper(['url','common']);
		$this->load->model(['Authentication_model','UserModel','Admin_model']);
        
		$headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
			if($decodedToken['status']){
				$this->user = (array)$decodedToken['data'];
                if($this->user['role'] != "admin"){
                    $this->response(['status'=>401,'message'=>'Unauthorized Access'], RestController::HTTP_UNAUTHORIZED);
                }
			}else{
				$this->response(['status'=>401,'message'=>$decodedToken['message']], RestController::HTTP_UNAUTHORIZED);
			}
		}
		else {
			$this->response(['status'=>400,'message'=>'Token Header missing'], RestController::HTTP_BAD_REQUEST);
		}

	}

    
    public function get_authors_get()
{
    $this->load->model('Admin_model');
    $requests = $this->Admin_model->getAuthors();
    if ($requests) {
        $result = [
            'status' => 200,
            'message' => 'Authors fetched successfully',
            'data' => $requests];
    } else {
        $result = [
            'status' => 404, 'message' => 'No Authors found',
            'data' => []
        ];
    }

    $this->response($result, RestController::HTTP_OK);
}

// public function get_publisher_get()
// {
//     $this->load->model('Admin_model');
//     $requests = $this->Admin_model->getPublishers();
//     if ($requests) {
//         $result = [
//             'status' => 200,
//             'message' => 'Publisher fetched successfully',
//             'data' => $requests];
//     } else {
//         $result = [
//             'status' => 404, 'message' => 'No Publisher found',
//             'data' => []
//         ];
//     }

//     $this->response($result, RestController::HTTP_OK);
// }

// public function get_reviewer_get()
// {
//     $this->load->model('Admin_model');
//     $requests = $this->Admin_model->getReviewers();
//     if ($requests) {
//         $result = [
//             'status' => 200,
//             'message' => 'Reviewers fetched successfully',
//             'data' => $requests];
//     } else {
//         $result = [
//             'status' => 404, 'message' => 'No Reviewers found',
//             'data' => []
//         ];
//     }

//     $this->response($result, RestController::HTTP_OK);
// }   

}
