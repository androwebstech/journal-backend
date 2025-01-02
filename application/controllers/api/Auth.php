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
		$this->load->model('Admin_model');
	}
	public function token_get(){
		echo password_hash($this->input->get('password'), PASSWORD_BCRYPT);
	}
	public function print_get(){
		$this->load->view('print_records');
	}


	public function login_post()
{	
    $this->form_validation->set_rules('email','Email','trim|required|xss_clean');
    $this->form_validation->set_rules('password','Password','trim|required');

    if($this->form_validation->run())
    {	
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        
        // Call the validate_login function to check credentials
        $res = $this->Authentication_model->validate_login($email, $password);
        
        if(!empty($res))
        {
            $this->load->library('Authorization_Token');
            $token_data = [];
            $token_data['id'] =  $res['id'];
            $token_data['email'] = $res['email'];
            
            // Add user image if available
            if(!empty($res['image'])){
                $token_data['image'] = base_url("/uploads/".$res['image']);
            }

            $token_data['name'] = $res['name'];
            $token_data['email'] = $res['email'];
            
            // Generate and return the token
            $token = $this->authorization_token->generateToken($token_data);
            $result = ['status' => 200, 'message' => 'Login Successful!', 'user' => $token_data, 'auth_token' => $token];
        }
        else
        {
            $result = ['status' => 403, 'message' => 'Invalid Credentials'];
        }
    }
    else
    {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }
    $this->response($result, RestController::HTTP_OK);
}


public function register_post()
{   		
    $this->load->database();
    $this->load->library('form_validation');
    $type = $this->input->post('type');
    $valid_tables = ['users', 'journals', 'reviewers'];

    if (!in_array($type, $valid_tables)) {
        $result = ['status' => 400, 'message' => 'Invalid user type!'];
        $this->response($result, RestController::HTTP_OK);
        return;
    }

    // Validation rules
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('contact', 'Mobile No', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]', array('is_unique' => 'This Email is already registered.'));
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');

    if ($this->form_validation->run()) {
        
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

        
      
        $data = [
            'name' => $this->input->post('name'),
            'contact' => $this->input->post('contact'),
            'email' => $this->input->post('email'),
            'password' => $password, 
        ];

        $res = $this->Admin_model->register($data);

        
        if (!empty($res)) {
            $token_data = [
                'id' => $res['id'],
                'email' => $res['email'],
                'name' => $res['name'],
                'contact' => $res['contact'],
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
