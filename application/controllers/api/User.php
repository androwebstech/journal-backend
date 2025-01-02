<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class User extends RestController {  
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
		$this->load->model('Authentication_model');
		$this->load->model('Admin_model');
        
		$headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
			if($decodedToken['status']){
				$this->user = (array)$decodedToken['data'];
			}else{
				$this->response(['status'=>401,'message'=>$decodedToken['message']], RestController::HTTP_UNAUTHORIZED);
			}
		}
		else {
			$this->response(['status'=>400,'message'=>'Token Header missing'], RestController::HTTP_BAD_REQUEST);
		}

	}

    function get_staff_list_get() {
        
        $staff_list = $this->Admin_model->get_staff();

        foreach($staff_list as $key=>$value) {
            if(!empty($value['image'])){
                $staff_list[$key]['image'] = base_url("/uploads/".$value['image']); 
            }
        }
    
        $this->response([
            'status'=>200,
            'data'=>$staff_list,
        ],RestController::HTTP_OK);
    
    }

    function get_daily_reports_get() {
        $startdate = $this->input->get('startdate');
        $enddate = $this->input->get('enddate');
        if($this->user['user_type'] == 'admin'){
            $user_id = $this->input->get('user_id');
        }else{
            $user_id = $this->user['user_id'];
        }
        $department_id = $this->input->get('department');
        $returnHTML = $this->input->get('htmlContent') == 'true';
        
        // Pass the dates to the model function
        $reports_list = $this->Admin_model->get_reports($startdate, $enddate , $user_id, $department_id);
        if($returnHTML){
            echo $this->load->view('print_records',['records'=>$reports_list],true);
            exit;
        }else{
            $this->response([
                'status' => 200,
                'data' => $reports_list,
            ], RestController::HTTP_OK);
        }
        
    }
    


//--------------------Register API---------------------------------------------





public function add_daily_report_post()
{   
    $this->load->database();
    // $allowed_department = ['IT', 'HR', 'Finance', 'Marketing'];
    
    $this->form_validation->set_rules('reports[]', 'Reports', 'required'); // Expect an array of reports

    if ($this->form_validation->run()) {
        $reports = $this->input->post('reports[]');
        $inserted_reports = [];
        $errors = [];
        $this->form_validation->reset_validation();

        foreach ($reports as $index => $report) {
            $this->form_validation->set_data($report);
            $this->form_validation->set_rules('head_name', 'Head Name', 'trim|required');
            $this->form_validation->set_rules('count', 'Count', 'trim|required|integer');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim');
            // $this->form_validation->set_rules(
            //     'department', 
            //     'Department', 
            //     'trim|required|in_list[' . implode(',', $allowed_department) . ']', 
            //     ['in_list' => 'Invalid department selected.']
            // );

            if ($this->form_validation->run()) {
                // Prepare data for insertion
                $data = [
                    'head_name' => $report['head_name'],
                    'count' => $report['count'],
                    'remarks' => $report['remarks'] ?? null, // Optional field
                    'user_id' => $this->user['user_id'],
                    'created_at' => get_datetime(),
                    'department' => $this->user['department_id'],
                ];

                // Insert data into the database
                if ($this->db->insert('daily_reports_table', $data)) {
                    $inserted_reports[] = $data;
                } else {
                    $errors[] = "Error inserting record at index $index";
                }
            } else {
                $errors[] = "Validation failed for record at index $index: " . strip_tags(validation_errors());
            }
        }

        if (!empty($inserted_reports)) {
            $result = [
                'status' => 200,
                'message' => 'Daily reports added successfully!',
                'inserted_reports' => $inserted_reports,
                'errors' => $errors
            ];
        } else {
            $result = [
                'status' => 400,
                'message' => 'No records inserted. Errors occurred.',
                'errors' => $errors
            ];
        }
    } else {
        $result = ['status' => 400, 'message' => 'Invalid input format. Expecting an array of reports.'];
    }

    $this->response($result, RestController::HTTP_OK);
}

//----------------Department Api-----------------------------------------


// ------------------ Add Department API ------------------------------------

public function add_department_post()
{
    $this->load->database();

    // Validate input
    $this->form_validation->set_rules('name', 'Department Name', 'trim|required|is_unique[department.name]', ['is_unique' => 'This department already exists.']);
    $this->form_validation->set_rules('description', 'Description', 'trim');

    if ($this->form_validation->run()) {
        // Prepare data
        $data = [
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description') ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Insert into database
        if ($this->db->insert('department', $data)) {
            $result = [
                'status' => 200,
                'message' => 'Department added successfully!',
                'department' => $data,
            ];
        } else {
            $result = ['status' => false, 'message' => 'Failed to add department.'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    $this->response($result, RestController::HTTP_OK);
}

// ------------------ Get All Departments API -----------------------------

public function get_departments_get()
{
    $this->load->database();

    $departments = $this->db->get('department')->result_array();
        $result = [
            'status' => 200,
            'message' => 'Departments retrieved successfully!',
            'data' => $departments,
        ];

    $this->response($result, RestController::HTTP_OK);
}

// ------------------ Update Department API -------------------------------

public function update_department_post()
{
    $this->load->database();
    $this->load->library('form_validation');
    
    $this->form_validation->set_rules('id', 'Department ID', 'trim|required|integer');
    $this->form_validation->set_rules('name', 'Department Name', 'trim|required');
    $this->form_validation->set_rules('description', 'Description', 'trim');

    if ($this->form_validation->run()) {
        $id = $this->input->post('id', true);
        $name = $this->input->post('name', true);
        $description = $this->input->post('description', true);
        $department = $this->db->get_where('department', ['id' => $id])->row_array();
        if ($department) {
            $data = [
                'name' => $name,
                'description' => $description ?? $department['description'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->where('id', $id);
            if ($this->db->update('department', $data)) {
                $result = [
                    'status' => 200,
                    'message' => 'Department updated successfully!',
                    'department' => $data,
                ];
            } else {
                $result = [
                    'status' => false,
                    'message' => 'Failed to update the department due to a database error.',
                ];
            }
        } else {
            $result = [
                'status' => 404,
                'message' => 'Department not found.',
            ];
        }
    } else {
        $result = [
            'status' => 400,
            'message' => strip_tags(validation_errors()),
        ];
    }
    $this->response($result, $result['status'] ?? RestController::HTTP_OK);
}

// ------------------ Delete Department API -------------------------------

public function delete_department_delete()
{
    $this->load->database();
    $id = intval($this->input->get('id'));

    if ($id) {
        if($this->db->where('department', $id)->count_all_results('users') >0){
            $this->response(['status'=>400 , 'message'=>'Cannot delete department as some users are assigned to this department'], RestController::HTTP_OK);
            exit;
        }

        $this->db->where('id', $id)->delete('department');
        $this->db->where('department', $id)->delete('daily_reports_table');

        $result = ['status' => 200, 'message' => 'Department deleted successfully!'];
    } else {
        $result = ['status' => 400, 'message' => 'Department ID is required.'];
    }

    $this->response($result, RestController::HTTP_OK);
}





// ----------Profile Update API-------------------------------------

public function update_profile_post()
{
    $this->load->database();
    
    // Input validation
    $this->form_validation->set_rules('user_id', 'User ID', 'trim|required|integer');
    $this->form_validation->set_rules('fname', 'First Name', 'trim|required');
    $this->form_validation->set_rules('lname', 'Last Name', 'trim');
    $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

    if ($this->form_validation->run()) {
        // Fetch user ID
        $user_id = $this->input->post('user_id');

        // Prepare data for update
        $data = [
            'fname' => $this->input->post('fname'),
            'lname' => $this->input->post('lname'),
            'mobile_no' => $this->input->post('mobile_no'),
            'email' => $this->input->post('email'),
        ];
        if(!empty($this->input->post('password'))){
            $data['password'] = $this->input->post('password');
        }

        // Check if the user exists
        $this->db->where('user_id', $user_id);
        $user_exists = $this->db->get('users')->row_array();

        if ($user_exists) {
            // Update user details
            $this->db->where('user_id', $user_id);
            $updated = $this->db->update('users', $data);

            if ($updated) {
                // Generate response with updated data
                $updated_user = $this->db->get_where('users', ['user_id' => $user_id])->row_array();
                $result = [
                    'status' => 200,
                    'message' => 'Profile updated successfully!',
                    'user' => $updated_user
                ];
            } else {
                $result = ['status' => false, 'message' => 'Failed to update profile.'];
            }
        } else {
            $result = ['status' => 404, 'message' => 'User not found.'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    $this->response($result, RestController::HTTP_OK);
}











public function update_staff_post()
{   
    $this->load->database();
    $this->load->library('upload');
    $this->load->library('form_validation');

   
    $this->form_validation->set_rules('user_id', 'User ID', 'trim|required|numeric');
    $this->form_validation->set_rules('fname', 'First Name', 'trim');
    $this->form_validation->set_rules('lname', 'Last Name', 'trim');
    $this->form_validation->set_rules('mobile_no', 'Mobile No', 'trim', );
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email', );
    $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]');
    $this->form_validation->set_rules('department', 'Department', 'trim|required|greater_than[0]');

    if ($this->form_validation->run()) {
        
        $user_id = $this->input->post('user_id');

        
        $uploaded_image = null;

        if (!empty($_FILES['image']['name'])) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|pdf';
            $config['encrypt_name']         = true;
            $config['max_size']             = 10000;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('image')) {
                $uploaded_image = $this->upload->data('file_name');
            } else {
                $result = ['status' => 400, 'message' => $this->upload->display_errors()];
                $this->response($result, RestController::HTTP_OK);
                return;
            }
        }

        
       

        $data = [
            'department' =>$this->input->post('department'),
            'fname' => $this->input->post('fname'),
            'lname' => $this->input->post('lname'),
            'mobile_no' => $this->input->post('mobile_no'),
            'email' => $this->input->post('email'),
        ];
        if ($this->input->post('password')) $data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        
        if ($uploaded_image) $data['image'] = $uploaded_image;

       
        $this->db->where('user_id', $user_id);

        $update_status = $this->db->update('users', $data);

        if ($update_status) {
            $result = [
                'status' => 200,
                'message' => 'User updated successfully!',
                'updated_data' => $data,
            ];
        } else {
            $result = ['status' => false, 'message' => 'Failed to update user!'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    $this->response($result, RestController::HTTP_OK);
}














public function delete_staff_delete()
{
    $this->load->database();
    $id = $this->input->get('user_id');

    
        
        if ($id) {
            $this->db->where('user_id', $id)->delete('users');
            $result = ['status' => 200, 'message' => 'User deleted successfully!'];
            }

            else {
                $result = ['status' => 400, 'message' => 'Department ID is required.'];
            }
        
            $this->response($result, RestController::HTTP_OK);


}
}