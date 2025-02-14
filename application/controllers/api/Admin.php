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


public function approve_reject_reviewer_post(){
    $this->form_validation->set_rules('id', 'ID', 'required|numeric');
    $this->form_validation->set_rules('status', 'Status', 'required|in_list['.APPROVAL_STATUS::APPROVED.','.APPROVAL_STATUS::REJECTED.']');
    if (!$this->form_validation->run()) {
        $this->response(['status'=>400,'message'=>validation_errors()], RestController::HTTP_OK);
    } else {
        $user_id = $this->post('id');
        $status = $this->post('status');
        $this->load->model('Admin_model');
        $req = $this->Admin_model->getReviewerDetail($user_id);
        if (!$req) {
            $this->response(['status' => 404, 'message' => 'Reviewer not found'], RestController::HTTP_OK);
            return;
        }
        $current_status = $req['approval_status'];
        if ($current_status != APPROVAL_STATUS::PENDING) {
            $result = [
                'status' => 400,
                'message' => "Action already taken",
            ];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
        $requests = $this->Admin_model->approveRejectReviewer($user_id, $status);
        if ($requests) {
            $result = [
                'status' => 200,
                'message' => 'Reviewer status updated successfully',
                'data' => $requests];
        } else {
            $result = [
                'status' => 400, 'message' => 'Failed to update reviewer status',
                'data' => []
            ];
        }
        $this->response($result, RestController::HTTP_OK);
    }
}

public function approve_reject_journal_post(){
    $this->form_validation->set_rules('journal_id', 'ID', 'required|numeric');
    $this->form_validation->set_rules('status', 'Status', 'required|in_list['.APPROVAL_STATUS::APPROVED.','.APPROVAL_STATUS::REJECTED.']');
    if (!$this->form_validation->run()) {
        $this->response(['status'=>400,'message'=>validation_errors()], RestController::HTTP_OK);
    } else {
        $journal_id = $this->post('journal_id');
        $status = $this->post('status');
        $this->load->model('Admin_model');
        $req = $this->UserModel->getJournalById($journal_id);
        if (!$req) {
            $this->response(['status' => 404, 'message' => 'Journal not found'], RestController::HTTP_OK);
            return;
        }
        $current_status = $req['approval_status'];
        if ($current_status != APPROVAL_STATUS::PENDING) {
            $result = [
                'status' => 400,
                'message' => "Action already taken",
            ];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
        $requests = $this->Admin_model->approveRejectJournal($journal_id, $status);
        if ($requests) {
            $result = [
                'status' => 200,
                'message' => 'Journal status updated successfully',
                'data' => $requests];
        } else {
            $result = [
                'status' => 400, 'message' => 'Failed to update Journal status',
                'data' => []
            ];
        }
        $this->response($result, RestController::HTTP_OK);
    }
}




public function approve_reject_publication_post(){
    $this->form_validation->set_rules('ppuid', 'ID', 'required|numeric');
    $this->form_validation->set_rules('approval_status', 'Status', 'required|in_list['.APPROVAL_STATUS::APPROVED.','.APPROVAL_STATUS::REJECTED.']');
    if (!$this->form_validation->run()) {
        $this->response(['status'=>400,'message'=>validation_errors()], RestController::HTTP_OK);
    } else {
        $ppuid = $this->post('ppuid');
        $status = $this->post('approval_status');
        $this->load->model('Admin_model');
        $req = $this->UserModel->get_publication_by_id($ppuid);
        if (!$req) {
            $this->response(['status' => 404, 'message' => 'Publication not found'], RestController::HTTP_OK);
            return;
        }
        $current_status = $req['approval_status'];
        if ($current_status != APPROVAL_STATUS::PENDING) {
            $result = [
                'status' => 400,
                'message' => "Action already taken",
            ];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
        $requests = $this->Admin_model->approveRejectPublication($ppuid, $status);
        if ($requests) {
            $result = [
                'status' => 200,
                'message' => 'Publication status updated successfully',
                'data' => $requests];
        } else {
            $result = [
                'status' => 400, 'message' => 'Failed to update publication status',
                'data' => []
            ];
        }
        $this->response($result, RestController::HTTP_OK);
    }
}


    public function get_publications_get()
{
    $this->load->model('Admin_model');
    $requests = $this->Admin_model->getPublications();
    if ($requests) {
        $result = [
            'status' => 200,
            'message' => 'Publications fetched successfully',
            'data' => $requests];
    } else {
        $result = [
            'status' => 404, 'message' => 'No Publication found',
            'data' => []
        ];
    }

    $this->response($result, RestController::HTTP_OK);
}



    public function change_password_post()
    {
        // Input validation
        $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');

        if ($this->form_validation->run()) {
            $oldPassword = $this->input->post('old_password');
            $newPassword = $this->input->post('new_password');

            // Verify old password
            $user = $this->Admin_model->getAdminId($this->user['admin_id']);
            if (!$user || !password_verify($oldPassword, $user['password'])) {
                $result = [
                    'status' => 400,
                    'message' => 'Old password is incorrect'
                ];
                $this->response($result, RestController::HTTP_OK);
                exit;
            }

            // Update password
            $updateData = ['password' => password_hash($newPassword, PASSWORD_BCRYPT)];
            $updated = $this->Admin_model->updateAdmin($this->user['admin_id'], $updateData);

            if ($updated) {
                $result = [
                    'status' => 200,
                    'message' => 'Password changed successfully'
                ];
            } else {
                $result = [
                    'status' => 500,
                    'message' => 'Failed to change password'
                ];
            }
        } else {
            $result = [
                'status' => 400,
                'message' => strip_tags(validation_errors())
            ];
        }

        $this->response($result, RestController::HTTP_OK);
    }


}
