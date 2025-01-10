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

    // ---------------Add Journal Api-------------------/

    public function add_journal_post()
    {
        $this->load->model('Admin_model');
        $this->load->library('form_validation');
        $this->load->helper('url');
    
        // Validation rules
        $this->form_validation->set_rules('journal_name', 'Journal Name', 'trim|required');
        $this->form_validation->set_rules('eissn_no', 'E-ISSN', 'trim');
        $this->form_validation->set_rules('pissn_no', 'P-ISSN', 'trim');
        $this->form_validation->set_rules('first_volume', 'First Volume', 'trim|integer|required');
        $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|integer|required');
        $this->form_validation->set_rules('publisher_name', 'Publisher Name', 'trim|required');
        $this->form_validation->set_rules('broad_research_area', 'Broad Research Area', 'trim|required');
        $this->form_validation->set_rules('website_link', 'Website Link', 'trim|valid_url');
        $this->form_validation->set_rules('journal_submission_link', 'Submission Link', 'trim|valid_url');
        $this->form_validation->set_rules('indexing', 'Indexing', 'trim');
        $this->form_validation->set_rules('country', 'Country', 'trim|required|in_list[USA,India,UK,Canada,Australia]');
        $this->form_validation->set_rules('state', 'State', 'trim|required|in_list[California,New York,Texas,Ontario,Queensland]');
        $this->form_validation->set_rules('publication', 'Publication Frequency', 'trim|required|in_list[Monthly,Quarterly,Yearly]');
        $this->form_validation->set_rules('usd_publication_charge', 'Publication Charge', 'trim|decimal');
        $this->form_validation->set_rules('review_type', 'Review Type', 'trim|required|in_list[Single-blind,Double-blind,Open Review,Editorial Review]');
        $this->form_validation->set_rules('publication_link', 'Publication Link', 'trim|valid_url');
    
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
                'publication' => $this->input->post('publication'),
                'usd_publication_charge' => $this->input->post('usd_publication_charge'),
                'review_type' => $this->input->post('review_type'),
                'publication_link' => $this->input->post('publication_link'),
                'jounal_status' => 0, // Default pending status
            ];
    
            $res = $this->Admin_model->insert_journal($data);
    
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
    
    
public function update_journal_post()
{
    $this->load->model('Admin_model');
    $this->load->library('form_validation');
    $this->load->helper('url');

    // Validation rules
    $this->form_validation->set_rules('id', 'Journal ID', 'trim|required|integer');
    $this->form_validation->set_rules('journal_name', 'Journal Name', 'trim');
    $this->form_validation->set_rules('eissn_no', 'E-ISSN', 'trim');
    $this->form_validation->set_rules('pissn_no', 'P-ISSN', 'trim');
    $this->form_validation->set_rules('first_volume', 'First Volume', 'trim|integer');
    $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|integer');
    $this->form_validation->set_rules('publisher_name', 'Publisher Name', 'trim');
    $this->form_validation->set_rules('broad_research_area', 'Broad Research Area', 'trim');
    $this->form_validation->set_rules('website_link', 'Website Link', 'trim|valid_url');
    $this->form_validation->set_rules('journal_submission_link', 'Submission Link', 'trim|valid_url');
    $this->form_validation->set_rules('indexing', 'Indexing', 'trim');
    $this->form_validation->set_rules('country', 'Country', 'trim|in_list[USA,India,UK,Canada,Australia]');
    $this->form_validation->set_rules('state', 'State', 'trim|in_list[California,New York,Texas,Ontario,Queensland]');
    $this->form_validation->set_rules('publication', 'Publication Frequency', 'trim|in_list[Monthly,Quarterly,Yearly]');
    $this->form_validation->set_rules('usd_publication_charge', 'Publication Charge', 'trim|decimal');
    $this->form_validation->set_rules('review_type', 'Review Type', 'trim|in_list[Single-blind,Double-blind,Open Review,Editorial Review]');
    $this->form_validation->set_rules('publication_link', 'Publication Link', 'trim|valid_url');

    if ($this->form_validation->run()) {
        $id = $this->input->post('id');
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
            'publication' => $this->input->post('publication'),
            'usd_publication_charge' => $this->input->post('usd_publication_charge'),
            'review_type' => $this->input->post('review_type'),
            'publication_link' => $this->input->post('publication_link'),
        ]);

        if (!empty($update_data)) {
            $updated = $this->Admin_model->update_journal($id, $update_data);

            if ($updated) {
                $result = [
                    'status' => 200,
                    'message' => 'Journal updated successfully!',
                    'data' => array_merge(['id' => $id], $update_data),
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

public function get_journal_get()
{
    $this->load->model('UserModel');

    $journals = $this->UserModel->get_all_journals();

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

//-----------Delete Jounal API------------------------------

public function delete_journal_delete()
{
    $this->load->database();
    $id = $this->input->get('id'); 

    if ($id) {
        $query = $this->db->get_where('journal_table', ['id' => $id]);
        
        if ($query->num_rows() > 0) {

            $this->db->where('id', $id)->delete('journal_table');
            
            if ($this->db->affected_rows() > 0) {
                $result = ['status' => 200, 'message' => 'Journal deleted successfully!'];
            } else {
                $result = ['status' => 500, 'message' => 'Failed to delete the journal.'];
            }
        } else {
            $result = ['status' => 404, 'message' => 'No Journal found with the provided ID.'];
        }
    } else {
        $result = ['status' => 400, 'message' => 'ID is required.'];
    }

    $this->response($result, RestController::HTTP_OK);
}


public function get_personal_details_get()
{
    $this->load->model('AuthorModel');
    $this->load->model('ReviewerModel');
    $this->load->library('Authorization_Token'); 

   
    $token = $this->input->get_request_header('Authorization');
    if (!$token) {
        $result = [
            'status' => 400,
            'message' => 'Token header missing',
            'data' => []
        ];
        $this->response($result, RestController::HTTP_BAD_REQUEST);
        return;
    }

    
    $decoded_token = $this->authorization_token->validateToken($token);
    if (!$decoded_token['status']) {
        $result = [
            'status' => 401,
            'message' => 'Invalid or expired token',
            'data' => []
        ];
        $this->response($result, RestController::HTTP_UNAUTHORIZED);
        return;
    }

    $entity_id = $decoded_token['data']->id; 
    $entity_type = $decoded_token['data']->type; 

    $personal_details = [];

    switch (strtolower($entity_type)) {
        case 'author':
            $personal_details = $this->AuthorModel->get_personal_details($entity_id);
            break;

        case 'reviewer':
            $personal_details = $this->ReviewerModel->get_personal_details($entity_id);
            break;

        case 'publisher':
            
            $personal_details = [];
            break;

        default:
            $result = [
                'status' => 400,
                'message' => 'Invalid entity type',
                'data' => []
            ];
            $this->response($result, RestController::HTTP_BAD_REQUEST);
            return;
    }

    if ($personal_details || $entity_type === 'publisher') {
        $result = [
            'status' => 200,
            'message' => 'Personal details fetched successfully',
            'data' => $personal_details
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




}