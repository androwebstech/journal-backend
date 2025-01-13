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
        $this->load->library('Authorization_Token');
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


	public function login_post()
{	
    $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
    $this->form_validation->set_rules('password', 'Password', 'trim|required');
    $this->form_validation->set_rules('type', 'Type', 'trim|required|in_list['. implode(',', USER_TYPE::ALL).']');


    if ($this->form_validation->run()) {	
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $type = $this->input->post('type');
        
        $res = $this->Authentication_model->validate_login($email, $password, $type);
        
        if (!empty($res)) {
            $token = $this->authorization_token->generateToken($res);

            $profile = $this->UserModel->getProfileByType($res['id'], $type); 
           if(!empty($profile))
            $res['profile'] = $profile;


            // Add user image if available
            // if (!empty($res['image'])) {
            //     $token_data['image'] = base_url("/uploads/" . $res['image']);
            // }
            
            // Generate and return the token
            
            $result = [
                'status' => 200,
                'message' => 'Login Successful!',
                'user' =>  $res,
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

    // Validation rules
    $this->form_validation->set_rules('name', 'Name', 'trim|required');
    $this->form_validation->set_rules('contact', 'Mobile No', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]', array('is_unique' => 'This Email is already registered.'));
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
    $this->form_validation->set_rules('type', 'Type', 'trim|required|in_list[' . implode(',', USER_TYPE::ALL) . ']');

    if ($this->form_validation->run()) {
        $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);

        $user_data = [
            'name'=>$this->input->post('name'),
            'contact'=>$this->input->post('contact'),
            'email' => $this->input->post('email'),
            'password' => $password,
            'type' => $this->input->post('type'),
        ];

        $res = $this->UserModel->register($user_data);

        if (!empty($res)) {
            $token = $this->authorization_token->generateToken($res);
            $profile = $this->UserModel->getProfileByType($res['id'], $res['type']);
            if(!empty($profile))
                $res['profile'] =  $profile;

            $result = [
                'status' => 200,
                'message' => 'Register Successful!',
                'user' => $res,
                'auth_token' => $token,
            ];
        } else {
            $result = ['status' => 500, 'message' => 'Something Went Wrong!'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    $this->response($result, RestController::HTTP_OK);
}


//contact api

public function contact_post()
    {

         $this->load->model('Admin_model');
        $this->load->library('form_validation');
        $this->load->helper('url');
        // Validation rules
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');

        if ($this->form_validation->run()) {
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'message' => $this->input->post('message'),
            ];

            $res = $this->Admin_model->insert_contact($data);

            if ($res) {
                $result = [
                    'status' => 200,
                    'message' => 'Message sent successfully!',
                    'data' => [
                        'id' => $res,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'message' => $data['message'],
                    ]
                ];
            } else {
                $result = ['status' => 500, 'message' => 'Something went wrong!'];
            }
        } else {
            $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
        }

        // Return the response
        $this->response($result, RestController::HTTP_OK);
    }


       

}
