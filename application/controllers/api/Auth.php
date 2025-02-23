<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class Auth extends RestController
{
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->library(['form_validation']);
        $this->load->library('Authorization_Token');
        $this->load->helper(['url','common_helper','security']);
        $this->load->model(['Authentication_model']);
        $this->load->model(['Admin_model']);
        $this->load->model('UserModel');
    }
    public function token_get()
    {
        echo password_hash('password', PASSWORD_BCRYPT);
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
                $tokenData = get_token_data($res);
                $token = $this->authorization_token->generateToken($tokenData);

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
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'email' => $this->input->post('email'),
                'password' => $password,
                'type' => $this->input->post('type'),
            ];

            $res = $this->UserModel->register($user_data);

            if (!empty($res)) {
                $tokenData = get_token_data($res);
                $token = $this->authorization_token->generateToken($tokenData);
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


    public function admin_login_post()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean');

        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run()) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            // $user_type = $this->input->post('user_type');
            $res = $this->Admin_model->validate_login($email, $password);
            // print_r(true);
            // exit();
            if (!empty($res)) {
                $this->load->library('Authorization_Token');
                $token_data = [];
                $token_data['admin_id'] =  $res['admin_id'];
                $token_data['email'] = $res['email'];
                $token_data['role'] = "admin";

                if (!empty($res['image'])) {
                    $token_data['image'] =  safe_image("/uploads/".$res['image']);
                }
                $token = $this->authorization_token->generateToken($token_data);
                $result = ['status' => 200,'message' => 'Login Successfully!','user' => $token_data,'auth_token' => $token];
            } else {
                $result = ['status' => 403,'message' => 'Invalid Credentials'];
            }
        } else {
            $result = ['status' => 400,'message' => strip_tags(validation_errors())];
        }
        $this->response($result, RestController::HTTP_OK);
    }


    public function admin_register_post()
    {
        exit('blocked');
        $this->load->database();
        $this->load->library('upload');
        $this->load->library('form_validation');

        // $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim|required', array('is_unique' => 'This Mobile no is already registered.'));
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[admin.email]', array('is_unique' => 'This Email is already registered.'));
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
        if (empty($_FILES['image']['name'])) {
            $this->form_validation->set_rules('image', 'Image', 'require');
        }

        if ($this->form_validation->run()) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|pdf';
            $config['encrypt_name']			= true;
            $config['max_size']             = 10000;

            $this->upload->initialize($config);


            $uploaded_image = null;

            if ($this->upload->do_upload('image')) {
                $uploaded_image = $this->upload->data('file_name');
            } else {
                $result = ['status' => 400, 'message' => $this->upload->display_errors()];
                $this->response($result, RestController::HTTP_OK);
                return;
            }


            $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            $data = [
                'email' => $this->input->post('email'),
                'password' => $password,
                'image' => $uploaded_image,
                'role' => 'admin',
            ];

            $res = $this->Admin_model->register($data);
            // print_r($data);
            // exit;
            if (!empty($res)) {
                $this->load->library('Authorization_Token');
                $token_data = [];
                $token_data['admin_id'] = $res['admin_id'];
                $token_data['email'] = $this->input->post('email');
                $token_data['role'] = $res['role'];
                $token_data['image'] = base_url('uploads/' . $uploaded_image);
                $token_data['created_at'] = $res['created_at'];

                $result = ['status' => 200, 'message' => 'Register Successfully!', 'user' => $token_data];
            } else {
                $result = ['status' => false, 'message' => 'Something Went Wrong!'];
            }
        } else {
            $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
        }

        $this->response($result, RestController::HTTP_OK);
    }


}
