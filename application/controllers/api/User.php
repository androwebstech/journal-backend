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
		$this->load->model(['Authentication_model','UserModel','Admin_model']);
        
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

    // ---------------Add Journal Api-------------------/

    public function add_journal_post()
    {
        $this->load->model('UserModel');
        $this->load->helper('url');

        // Validation rules
        $this->form_validation->set_rules('journal_name', 'Journal Name', 'trim|required');
        $this->form_validation->set_rules('eissn_no', 'E-ISSN', 'trim');
        $this->form_validation->set_rules('pissn_no', 'P-ISSN', 'trim');
        $this->form_validation->set_rules('first_volume', 'First Volume', 'trim|integer|required');
        $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|required|in_list[Monthly,Bimonthly,Yearly,Halfyearly,Quaterly]');
        $this->form_validation->set_rules('publisher_name', 'Publisher Name', 'trim|required');
        $this->form_validation->set_rules('broad_research_area', 'Broad Research Area', 'trim|required');
        $this->form_validation->set_rules('website_link', 'Website Link', 'trim|valid_url');
        $this->form_validation->set_rules('journal_submission_link', 'Submission Link', 'trim|valid_url');
        $this->form_validation->set_rules('indexing', 'Indexing', 'trim');
        $this->form_validation->set_rules('country', 'Country', 'trim|required|in_list[USA,India,UK,Canada,Australia]');
        $this->form_validation->set_rules('state', 'State', 'trim|required');
        $this->form_validation->set_rules('publication_type', 'Publication', 'trim|required|in_list[Free,Paid]');
        $this->form_validation->set_rules('usd_publication_charge', 'Publication Charge', 'trim|integer');
        $this->form_validation->set_rules('review_type', 'Review Type', 'trim|required|in_list[Single-Blind,Double-Blind,Open Peer Review,Collaborative]');
        $this->form_validation->set_rules('review_time', 'Review Time', 'trim');

        if ($this->form_validation->run()) {
            $data = [
                'journal_name' => $this->input->post('journal_name'),
                'eissn_no' => $this->input->post('eissn_no'),
                'pissn_no' => $this->input->post('pissn_no'),
                'first_volume' => $this->input->post('first_volume'),
                'number_of_issue_per_year' => $this->input->post('number_of_issue_per_year'),
                'publisher_name' => $this->input->post('publisher_name'),
                'broad_research_area' => $this->input->post('broad_research_area'),
                'website_link' => $this->input->post('website_link'),
                'journal_submission_link' => $this->input->post('journal_submission_link'),
                'indexing' => $this->input->post('indexing'),
                'country' => $this->input->post('country'),
                'state' => $this->input->post('state'),
                'publication_type' => $this->input->post('publication_type'),
                'usd_publication_charge' => $this->input->post('usd_publication_charge'),
                'review_type' => $this->input->post('review_type'),
                'review_time' => $this->input->post('review_time'),
                'user_id' => $this->user['id'], // Ensure $this->user is properly set
                'approval_status' => 0, // Default pending status
            ];

            $res = $this->UserModel->insert_journal($data);

            if ($res) {
                $result = [
                    'status' => 200,
                    'message' => 'Journal submitted successfully!',
                    'data' => array_merge(['id' => $res], $data),
                ];
            } else {
                $result = ['status' => 500, 'message' => 'Failed to submit the journal!'];
            }
        } else {
            $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
        }

        // Return the response
        $this->response($result, RestController::HTTP_OK);
    }
    
    
//-----------Update Jounal API------------------------------
    
    
public function update_journal_post($journal_id = null)

{
    $this->load->model('UserModel');
    $this->load->helper('url');

       if (empty($journal_id)) {
        $result = ['status' => 400, 'message' => 'Invalid Journal ID.'];
        $this->response($result, RestController::HTTP_BAD_REQUEST);
        return;
    }

    // Validation rules
    
    $this->form_validation->set_rules('journal_name', 'Journal Name', 'trim');
    $this->form_validation->set_rules('eissn_no', 'E-ISSN', 'trim');
    $this->form_validation->set_rules('pissn_no', 'P-ISSN', 'trim');
    $this->form_validation->set_rules('first_volume', 'First Volume', 'trim|integer');
    $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|in_list[Monthly,Bimonthly,Yearly,Halfyearly,Quaterly]');
    $this->form_validation->set_rules('publisher_name', 'Publisher Name', 'trim');
    $this->form_validation->set_rules('broad_research_area', 'Broad Research Area', 'trim');
    $this->form_validation->set_rules('website_link', 'Website Link', 'trim|valid_url');
    $this->form_validation->set_rules('journal_submission_link', 'Submission Link', 'trim|valid_url');
    $this->form_validation->set_rules('indexing', 'Indexing', 'trim');
    $this->form_validation->set_rules('country', 'Country', 'trim');
    $this->form_validation->set_rules('state', 'State', 'trim');
    $this->form_validation->set_rules('publication_type', 'Publication Frequency', 'trim|in_list[Free,Paid]');
    $this->form_validation->set_rules('usd_publication_charge', 'Publication Charge', 'trim|integer');
    $this->form_validation->set_rules('review_type', 'Review Type', 'trim|in_list[Single-Blind,Double-Blind,Open Peer Review,Collaborative]');
    $this->form_validation->set_rules('review_time', 'Review Time', 'trim');

    if ($this->form_validation->run()) {
       
        $update_data = array_filter([
            'journal_name' => $this->input->post('journal_name'),
            'eissn_no' => $this->input->post('eissn_no'),
            'pissn_no' => $this->input->post('pissn_no'),
            'first_volume' => $this->input->post('first_volume'),
            'number_of_issue_per_year' => $this->input->post('number_of_issue_per_year'),
            'publisher_name' => $this->input->post('publisher_name'),
            'broad_research_area' => $this->input->post('broad_research_area'),
            'website_link' => $this->input->post('website_link'),
            'journal_submission_link' => $this->input->post('journal_submission_link'),
            'indexing' => $this->input->post('indexing'),
            'country' => $this->input->post('country'),
            'state' => $this->input->post('state'),
            'publication_type' => $this->input->post('publication_type'),
            'usd_publication_charge' => $this->input->post('usd_publication_charge'),
            'review_type' => $this->input->post('review_type'),
            'review_time' => $this->input->post('review_time'),
        ]);

        if (!empty($update_data)) {
            $updated = $this->UserModel->update_journal($journal_id, $update_data);

            if ($updated) {
                $result = [
                    'status' => 200,
                    'message' => 'Journal updated successfully!',
                    'data' => array_merge(['journal_id' => $journal_id], $update_data),
                ];
            } else {
                $result = ['status' => 500, 'message' => 'Failed to update the journal.'];
            }
        } else {
            $result = ['status' => 400, 'message' => 'No valid data to update.'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    // Return the response
    $this->response($result, RestController::HTTP_OK);
}






//-----------Get Jounal API------------------------------

public function get_journals_get()
{
    $this->load->model('UserModel');
    $journals = $this->UserModel->getJournalsByUserId($this->user['id']);
    if ($journals) {
        $result = [
            'status' => 200,
            'message' => 'Journals fetched successfully',
            'data' => $journals
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No journals found',
            'data' => []
        ];
    }
    $this->response($result, RestController::HTTP_OK);
}


public function get_journal_by_id_get($id = null)
{
   
    $this->load->model('UserModel');

   
    if (!$id) {
        $result = [
            'status' => 400,
            'message' => 'Journal ID is required',
            'data' => null
        ];
        return $this->response($result, RestController::HTTP_BAD_REQUEST);
    }

    
    $journal = $this->UserModel->get_journal_by_id($id);

  
    if ($journal) {
        $result = [
            'status' => 200,
            'message' => 'Journal fetched successfully',
            'data' => $journal
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No journal found with the given ID',
            'data' => null
        ];
    }

   
    return $this->response($result, RestController::HTTP_OK);
}


//-----------Delete Jounal API------------------------------
public function delete_journal_get($id = 0)
{
    // $this->load->database();
    $this->load->model('UserModel');

    $id = intval($id);
    if ($id > 0) {
        $result = $this->UserModel->deleteJournalById($id, $this->user['id']);
    } else {
        $result = ['status' => 400, 'message' => 'Valid ID is required.'];
    }

   
    $this->response($result, RestController::HTTP_OK);
}

public function get_personal_details_get()
{
    $user = $this->UserModel->getUserById($this->user['id']);
    
    if (!empty($user)) {
        $result = [
            'status' => 200,
            'message' => 'Success',
            'data' => $user
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No personal details found',
            'data' => []
        ];
    }

    $this->response($result, RestController::HTTP_OK);
}



public function update_personal_details_post()
{
    $id = $this->user['id']; // Assuming the logged-in user's ID is available here
    $data = $this->input->post(); // Retrieve input data

    // Handle profile image upload if provided
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Define upload configuration
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB limit
        $config['file_name'] = 'profile_' . $id . '_' . time();

        // Load upload library and initialize the config
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('profile_image')) {
            // Get uploaded file data
            $uploadData = $this->upload->data();
            $data['profile_image'] = 'uploads/' . $uploadData['file_name'];
        } else {
            $result = [
                'status' => 400,
                'message' => 'Profile image upload failed: ' . $this->upload->display_errors('', '')
            ];
            return $this->response($result, RestController::HTTP_OK);
        }
    }

    if (!empty($data)) {
        // Update user details
        $update = $this->UserModel->updateUserById($id, $data);

        if ($update) {
            $result = [
                'status' => 200,
                'message' => 'User details updated successfully',
                'data'=> $update,
            ];
        } else {
            $result = [
                'status' => 500,
                'message' => 'Failed to update user details'
            ];
        }
    } else {
        $result = [
            'status' => 400,
            'message' => 'Invalid data provided'
        ];
    }

    $this->response($result, RestController::HTTP_OK);
}


}