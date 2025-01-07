<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class Auth extends RestController {

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
		echo password_hash('111111', PASSWORD_BCRYPT);
	}
	public function print_get(){
		$this->load->view('print_records');
	}


	public function login_post()
{	
    $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
    $this->form_validation->set_rules('password', 'Password', 'trim|required');
    $this->form_validation->set_rules('type', 'Type', 'trim|required|in_list['. implode(', ', USER_TYPE::ALL).']');


    if ($this->form_validation->run()) {	
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $type = $this->input->post('type');
        
 

        // Call the validate_login function to check credentials
        $res = $this->Authentication_model->validate_login($email, $password,$type);
        
        if (!empty($res)) {
            $this->load->library('Authorization_Token');
            $token_data = [];
            $token_data['id'] = $res['id'];
            $token_data['email'] = $res['email'];
            $token_data['type'] = $res['type']; 
            
            // Add user image if available
            if (!empty($res['image'])) {
                $token_data['image'] = base_url("/uploads/" . $res['image']);
            }
            
            // Generate and return the token
            $token = $this->authorization_token->generateToken($token_data);
            $result = [
                'status' => 200,
                'message' => 'Login Successful!',
                'user' => $token_data,
                'auth_token' => $token
            ];
        } else {
            $result = ['status' => 403, 'message' => 'Invalid Credentials'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }
    
    $this->response($result, RestController::HTTP_OK);
}


public function register_post()
{   		
    $this->load->database();
    $this->load->library('Authorization_Token');
    

    // Validation rules
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('contact', 'Mobile No', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]', array('is_unique' => 'This Email is already registered.'));
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
    $this->form_validation->set_rules('type', 'Type', 'trim|required|in_list['. implode(', ', USER_TYPE::ALL).']');


    if ($this->form_validation->run()) {
        
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

        
      
        $data = [
            'name' => $this->input->post('name'),
            'contact' => $this->input->post('contact'),
            'email' => $this->input->post('email'),
            'password' => $password, 
            'type' => $this->input->post('type'),
        ];

        $res = $this->UserModel->register($data);

        
        if (!empty($res)) {
            $token_data = [
                'id' => $res['id'],
                'email' => $res['email'],              
                'type' => $res['type'],
            ];

// Generate and return the token
$token = $this->authorization_token->generateToken($token_data);
$result = [
    'status' => 200,
    'message' => 'Register Successful!',
    'user' => $token_data,
    'auth_token' => $token
];

            
            $result = ['status' => 200, 'message' => 'Register Successfully!', 'user' => $token_data];
        } else {
            
            $result = ['status' => 500, 'message' => 'Something Went Wrong!'];
        }
    } else {
        
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    $this->response($result, RestController::HTTP_OK);
}


	

}
