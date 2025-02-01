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

        if($this->user['type'] != USER_TYPE::PUBLISHER){
            $this->response(['status'=>401,'message'=>'Action not allowed'], RestController::HTTP_OK);
            exit;
        }

        // Validation rules
        $this->form_validation->set_rules('journal_name', 'Journal Name', 'trim|required');
        $this->form_validation->set_rules('eissn_no', 'E-ISSN', 'trim');
        $this->form_validation->set_rules('pissn_no', 'P-ISSN', 'trim');
        $this->form_validation->set_rules('first_volume', 'First Volume', 'trim|integer');
        $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|in_list[Monthly,Bimonthly,Yearly,Halfyearly,Quarterly]');
        $this->form_validation->set_rules('publisher_name', 'Publisher Name', 'trim|required');
        $this->form_validation->set_rules('broad_research_area', 'Broad Research Area', 'trim|required');
        $this->form_validation->set_rules('website_link', 'Website Link', 'trim|valid_url');
        $this->form_validation->set_rules('journal_submission_link', 'Submission Link', 'trim|valid_url');
        $this->form_validation->set_rules('indexing', 'Indexing', 'trim');
        $this->form_validation->set_rules('country', 'Country', 'trim|required|numeric');
        $this->form_validation->set_rules('state', 'State', 'trim|required|numeric');
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
                'approval_status' => APPROVAL_STATUS::PENDING, // Default pending status
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
    $this->form_validation->set_rules('number_of_issue_per_year', 'Number of Issues Per Year', 'trim|in_list[Monthly,Bimonthly,Yearly,Halfyearly,Quarterly]');
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
       
        $update_data = [
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
        ];

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

// ----------------Add Publication API----------------------
public function add_publication_post()
{
    $this->load->model('UserModel');
    $this->load->helper('url');

    $user_id = $this->user['id'];

    // Validation rules
    $this->form_validation->set_rules('paper_title', 'Title', 'trim|required');
    $this->form_validation->set_rules('publication_year', 'Publication Year', 'trim|integer|required');
    $this->form_validation->set_rules('paper_type', 'Paper Type', 'trim|required|in_list[Journal,Patent,Book]');
    $this->form_validation->set_rules('authors', 'Author Name', 'trim|required');
    $this->form_validation->set_rules('issn', 'Issn Number', 'trim|integer|required');
    $this->form_validation->set_rules('volume', 'Volume', 'trim|integer|required');
    $this->form_validation->set_rules('issue', 'Issue', 'trim|integer|required');
    $this->form_validation->set_rules('live_url', 'Live Url', 'trim|valid_url');
    $this->form_validation->set_rules('indexing_with[]', 'Indexing Partner', 'trim|required'); 
    $this->form_validation->set_rules('publication_date', 'Publication Date', 'trim|required');
    $this->form_validation->set_rules('description', 'Description', 'trim');

    if ($this->form_validation->run()) {
        $indexing_with = $this->input->post('indexing_with[]'); 
        $data = [
            'paper_title' => $this->input->post('paper_title'),
            'user_id' => $user_id,
            'publication_year' => $this->input->post('publication_year'),
            'paper_type' => $this->input->post('paper_type'),
            'authors' => $this->input->post('authors'),
            'issn' => $this->input->post('issn'),
            'volume' => $this->input->post('volume'),
            'issue' => $this->input->post('issue'),
            'live_url' => $this->input->post('live_url'),
            'indexing_with' => implode(',', $indexing_with), 
            'publication_date' => $this->input->post('publication_date'),
            'description' => $this->input->post('description'),
            'approval_status' => APPROVAL_STATUS::PENDING,
        ];

        $res = $this->UserModel->insert_publication($data);

        $id = $this->db->insert_id();
        if ($res) {
            $result = [
                'status' => 200,
                'message' => 'Publication submitted successfully!',
                'data' => array_merge($data, ['ppuid' => $id]),
            ];
        } else {
            $result = ['status' => 500, 'message' => 'Failed to submit the Publication!'];
        }
    } else {
        $result = ['status' => 400, 'message' => strip_tags(validation_errors())];
    }

    // Return the response
    $this->response($result, RestController::HTTP_OK);
}


public function get_publication_get()
{
    $this->load->model('UserModel');
    $journals = $this->UserModel->getPublicationByUserId($this->user['id']);
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

public function delete_publication_get($id = 0)
{
    $this->load->database();
    $this->load->model('UserModel');

    $id = intval($id);
    if ($id > 0) {
        $result = $this->UserModel->deletePublicationById($id, $this->user['id']);
    } else {
        $result = ['status' => 400, 'message' => 'Valid ID is required.'];
    }

   
    $this->response($result, RestController::HTTP_OK);
}


public function update_publication_post($id = null)
{
    $this->load->model('UserModel');
    $this->load->helper('url');

    if (empty($id)) {
        $this->response(['status' => 400, 'message' => 'Invalid Publication ID.'], RestController::HTTP_BAD_REQUEST);
        return;
    }

    // Validation rules
    $this->form_validation->set_rules('paper_title', 'Title', 'trim|required');
    $this->form_validation->set_rules('paper_type', 'Paper Type', 'trim|required|in_list[Journal,Patent,Book]');
    $this->form_validation->set_rules('publication_year', 'Publication Year', 'trim|integer|required');
    $this->form_validation->set_rules('authors', 'Author Name', 'trim|required');
    $this->form_validation->set_rules('issn', 'Issn Number', 'trim|integer|required');
    $this->form_validation->set_rules('volume', 'Volume', 'trim|integer|required');
    $this->form_validation->set_rules('issue', 'Issue', 'trim|integer|required');
    $this->form_validation->set_rules('live_url', 'Live Url', 'trim|valid_url');
    $this->form_validation->set_rules('indexing_with[]', 'Indexing Partner', 'trim|required'); 
    $this->form_validation->set_rules('publication_date', 'Publication Date', 'trim|required');
    $this->form_validation->set_rules('description', 'Description', 'trim');
    
    if ($this->form_validation->run()) {
        $fields = [
            'paper_title',
            'paper_type',
            'publication_year',
            'authors',
            'issn',
            'volume',
            'issue',
            'live_url',
            'indexing_with',
            'publication_date',
            'description',
        ];

        $update_data = [];

        foreach ($fields as $field) {
            $value = $this->input->post($field);
            if ($value !== null) { 
                if ($field === 'indexing_with') {
                 
                    $value = is_array($value) ? implode(',', $value) : $value;
                }
                $update_data[$field] = $value;
            }
        }

        if (!empty($update_data)) {
            $updated = $this->UserModel->update_publication($id, $update_data);

            if ($updated) {
                $this->response([
                    'status' => 200,
                    'message' => 'Publication updated successfully!',
                    'data' => array_merge(['ppuid' => $id], $update_data),
                ], RestController::HTTP_OK);
            } else {
                $this->response(['status' => 500, 'message' => 'Failed to update the Publication.'], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response(['status' => 400, 'message' => 'No valid data to update.'], RestController::HTTP_BAD_REQUEST);
        }
    } else {
        $this->response(['status' => 400, 'message' => strip_tags(validation_errors())], RestController::HTTP_BAD_REQUEST);
    }
}


public function get_publication_by_id_get($id = null)
{
   
    $this->load->model('UserModel');

   
    if (!$id) {
        $result = [
            'status' => 400,
            'message' => 'Publication ID is required',
            'data' => null
        ];
        return $this->response($result, RestController::HTTP_BAD_REQUEST);
    }

    
    $journal = $this->UserModel->get_publication_by_id($id);

  
    if ($journal) {
        $result = [
            'status' => 200,
            'message' => 'Publication fetched successfully',
            'data' => $journal
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No Publication found with the given ID',
            'data' => null
        ];
    }

   
    return $this->response($result, RestController::HTTP_OK);
}




//Research paper submit api

public function submit_research_post()
{
    $this->form_validation->set_rules('author_name', 'Author Name', 'trim|required');
    $this->form_validation->set_rules('author_contact', 'Mobile No', 'trim|required'); 
    $this->form_validation->set_rules('author_email', 'Email', 'trim|required|valid_email');
    $this->form_validation->set_rules('country', 'Country/Region', 'trim|required');
    $this->form_validation->set_rules('affiliation', 'Affiliation', 'trim');
    $this->form_validation->set_rules('department', 'Department', 'trim');
    $this->form_validation->set_rules('paper_title', 'Paper Title', 'trim|required');
    $this->form_validation->set_rules('abstract', 'Abstract', 'trim|required');
    $this->form_validation->set_rules('keywords', 'Keywords', 'trim|required');
    
    
    if ($this->form_validation->run()) {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'png|pdf|doc|docx';
        $config['max_size'] = 2048;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            $result = [
                'status' => 400,
                'message' => $this->upload->display_errors('', ''),
            ];
        } else {
            $file_data = $this->upload->data();

          // Fetch co-authors data from input
        //   $co_authors = $this->input->post('co_authors[]');
        //   $co_author_data = [];
        //   if (is_array($co_authors)) {
        //       foreach ($co_authors as $co_author) {
        //           $co_author_data[] = [
        //               'name' => $co_author['co_author_name'] ?? '',
        //               'contact' => $co_author['co_author_contact'] ?? '',
        //               'email' => $co_author['co_author_email'] ?? '',
        //               'country' => $co_author['co_author_country'] ?? '',
        //               'affiliation' => $co_author['co_author_affiliation'] ?? '',
        //               'department' => $co_author['co_author_department'] ?? '',
        //           ];
        //       }
        //   }

        $co_author_data = $this->input->post('co_authors') ?? '[]';

          $data = [
            'author_name' => $this->input->post('author_name'),
            'author_contact' => $this->input->post('author_contact'),
            'author_email' => $this->input->post('author_email'),
            'country' => $this->input->post('country'),
            'affiliation' => $this->input->post('affiliation'),
            'department' => $this->input->post('department'),
            'paper_title' => $this->input->post('paper_title'),
            'abstract' => $this->input->post('abstract'),
            'keywords' => $this->input->post('keywords'),
            'co_authors' => $co_author_data,
            
            'file' => 'uploads/' . $file_data['file_name'],
            'user_id' => $this->user['id'],
            'submission_status' => 0,
        ];

        $res = $this->UserModel->insert_research_submission($data);

        if ($res) {
            $result = [
                'status' => 200,
                'message' => 'Research submitted successfully!',
                'data' => array_merge(['id' => $res], $data),
            ];
        } else {
            $result = [
                'status' => 500,
                'message' => 'Failed to submit the research!',
            ];
        }
    }
} else {
    // Validation failed
    $result = [
        'status' => 400,
        'message' => strip_tags(validation_errors()),
    ];
}

// Send response
$this->response($result, RestController::HTTP_OK);
}




public function get_journals_join_requests_get()
{
    $userId = $this->user['id'];

    $where = [];
    if($this->user['type'] == USER_TYPE::REVIEWER){
       $where['journal_join_requests.user_id'] = $userId;
    }else{
        $jouranls = $this->UserModel->getPublisherJournals($userId);
        $journalIds = array_column($jouranls,'journal_id');
        $this->db->where_in('journal_join_requests.journal_id',!empty($journalIds) ? $journalIds : [0]);
    }
    
    $journals = $this->UserModel->getJournalsJoinRequests($where);   
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


public function approve_reject_request_post($req_id)
{
    $status = $this->input->post('approval_status'); 

    if (empty($req_id) || empty($status)) {
        $result = [
            'status' => 400,
            'message' => 'Request ID and status are required',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }

    
    if (!in_array($status, [APPROVAL_STATUS::APPROVED,APPROVAL_STATUS::REJECTED ])) {
        $result = [
            'status' => 400,
            'message' => 'Invalid status value',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }

    $request = $this->UserModel->getReviewerRequestsById($req_id);

    if(empty($request)){
        $result = [
            'status' => 404,
            'message' => 'Request not found',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }
    if($request['sender'] == $this->user['type']){
        $result = [
            'status' => 401,
            'message' => 'Can not approve own requests ',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }


    $current_status = $request['approval_status'];

    if ($current_status != APPROVAL_STATUS::PENDING) {
        $result = [
            'status' => 400,
            'message' => "Action already taken",
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }

    
    $update_data = [
        'approval_status' => $status,
    ];

    $updated = $this->UserModel->updateRequestStatus($req_id, $update_data);

    if ($updated) {
        if ($status === APPROVAL_STATUS::APPROVED) {
           
            $request_data = $this->UserModel->getReviewerRequestsById($req_id);
            $link_data = [
                'journal_id' => $request['journal_id'],
                'reviewer_id' => $request['user_id'],
                'request_id' => $req_id,
                'created_at' => get_datetime()
            ];

            $link_inserted = $this->UserModel->insertJournalReviewerLink($link_data);

            if (!$link_inserted) {
                $result = [
                    'status' => 500,
                    'message' => 'Request status updated, but failed to create journal_reviewer_link entry',
                ];
                $this->response($result, RestController::HTTP_OK);
                return;
            }
        }

        $result = [
            'status' => 200,
            'message' => 'Request status updated successfully',
        ];
    } else {
        $result = [
            'status' => 500,
            'message' => 'Failed to update request status',
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
            $user = $this->UserModel->getUserId($this->user['id']);
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
            $updated = $this->UserModel->updateUser($this->user['id'], $updateData);

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




    
//     public function get_publish_requests_get()
// {
//     $this->load->model('UserModel');
//     $requests = $this->UserModel->getPublishRequestsByUserId($this->user['id']);
//     if ($requests) {
//         $result = [
//             'status' => 200,
//             'message' => 'Published Requests fetched successfully',
//             'data' => $requests];
//     } else {
//         $result = [
//             'status' => 404, 'message' => 'No Published Requests found',
//             'data' => []
//         ];
//     }

//     $this->response($result, RestController::HTTP_OK);
// }




    public function change_publish_request_status_post($id=null)
    {
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list['.implode(',',PR_STATUS::ALL).']');

        if ($this->form_validation->run()) {
            $status = $this->input->post('status');
            $result = $this->UserModel->update_publish_request_status($id, $status); 

            if ($result === true) { 
                $this->response([
                    'status' => 200,
                    'message' => 'Request status updated successfully.'
                ], RestController::HTTP_OK);
            } elseif ($result === false) { 
                $this->response([
                    'status' => 400, 
                    'message' => 'Request status is already '.$status.'.'
                ],  RestController::HTTP_OK);
            } else { 
                $this->response([
                    'status' => 500, 
                    'message' => 'Failed to update request status.' 
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => 400,
                'message' => strip_tags(validation_errors())
            ]);
        }
        $this->response($result, RestController::HTTP_OK);
    }

    // API to join a journal
public function join_journal_post($journal_id = null)
{
        $this->load->model('UserModel');
        $this->load->helper('url');
    
        if (empty($journal_id)) {
            $result = ['status' => 400, 'message' => 'Journal ID missing.'];
            $this->response($result, RestController::HTTP_BAD_REQUEST);
            return;
        }

        if($this->UserModel->canCreateJoinJournalRequest($journal_id, $this->user['id']) == false){
            $result = ['status' => 400, 'message' => 'Already Joined or Join Request is pending'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
    
        $data = [
            'journal_id' => $journal_id,
            'user_id' => $this->user['id'],
            'sender'=>USER_TYPE::REVIEWER,
            'approval_status' => APPROVAL_STATUS::PENDING,
        ];
    
        $res = $this->UserModel->join_journal($data);
    
        if ($res) {
            $result = [
                'status' => 200,
                'message' => 'Joined journal request sent successfully!',
                'data' => array_merge(['id' => $res], $data),
            ];
        } else {
            $result = ['status' => 500, 'message' => 'Failed to join the journal!'];
        }
        $this->response($result, RestController::HTTP_OK);
}

public function publisher_join_journal_post($journal_id = null, $user_id = null)
{
        $journal_id = intval($journal_id);
        $user_id = intval($user_id);
        $this->load->model('UserModel');
        $this->load->helper('url');
        if (empty($journal_id) || empty($user_id)) {
            $result = ['status' => 400, 'message' => 'Journal ID or User ID missing.'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
        //validate journal id
        if(!$this->UserModel->publisherHasJournal($journal_id,$this->user['id'])){
            $result = ['status' => 401, 'message' => 'Invalid Journal ID.'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }

        if($this->UserModel->canCreateJoinJournalRequest($journal_id, $user_id) == false){
            $result = ['status' => 400, 'message' => 'Already Joined or Join Request is pending'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
    
        $data = [
            'journal_id' => $journal_id,
            'user_id' => $user_id,
            'sender'=> USER_TYPE::PUBLISHER,
            'approval_status' => APPROVAL_STATUS::PENDING,
        ];
    
        $res = $this->UserModel->join_journal($data);
    
        if ($res) {
            $result = [
                'status' => 200,
                'message' => 'Joined journal request sent successfully!',
                'data' => array_merge(['id' => $res], $data),
            ];
        } else {
            $result = ['status' => 500, 'message' => 'Failed to join the journal!'];
        }
        $this->response($result, RestController::HTTP_OK);
}

public function research_paper_search_get($limit = 10, $page = 1){
    $filters = $this->input->get() ?? [];
    $searchString = $this->input->get('search',true) ?? '';
    $limit = abs($limit) < 1 ? 10 : abs($limit) ;
    $page = abs($page) < 1 ? 1 : abs($page);

    $offset = ($page - 1) * $limit;
    $res = $this->UserModel->getResearchPaper($filters, $limit, $offset, $searchString);
    $count = $this->UserModel->getResearchPaperCount($filters, $searchString);
    
    $this->response([
        'status' => 200,
        'message' => 'Success',
        'data' => $res,
        'totalPages' => ceil($count / $limit),
        'currentPage'=> $page,
    ], RestController::HTTP_OK);
}

public function publisher_join_paper_post($journal_id = null, $paper_id = null)
{
        $journal_id = intval($journal_id);
        $paper_id = intval($paper_id);
        $this->load->model('UserModel');
        $this->load->helper('url');
        if (empty($journal_id) || empty($paper_id)) {
            $result = ['status' => 400, 'message' => 'Journal ID or Paper ID missing.'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }
        //validate journal id
        if($this->user['type'] == USER_TYPE::PUBLISHER){
             if(!$this->UserModel->publisherHasJournal($journal_id,$this->user['id'])){
                $result = ['status' => 401, 'message' => 'Invalid Journal ID.'];
                $this->response($result, RestController::HTTP_OK);
                return;
            }
        }
        else if($this->user['type'] == USER_TYPE::AUTHOR){
            if(!$this->UserModel->authorHasPaper($paper_id,$this->user['id'])){
                $result = ['status' => 401, 'message' => 'Invalid Paper ID.'];
                $this->response($result, RestController::HTTP_OK);
                return;
            }
        }
    

        if($this->UserModel->canCreateJoinPaperRequest($journal_id, $paper_id) == false){
            $result = ['status' => 400, 'message' => 'Request already submitted or pending'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }

        if($this->user['type'] == USER_TYPE::AUTHOR){
            $author_id = $this->user['id'];
            $journals_details = $this->UserModel->getJournalById($journal_id);
            $publisher_id = $journals_details['user_id'];
        }
        else if($this->user['type'] == USER_TYPE::PUBLISHER){
            $publisher_id = $this->user['id'];
            $paper_details = $this->UserModel->getPaperById($paper_id);
            $author_id = $paper_details['user_id'];
        }
        $data = [
            'journal_id' => $journal_id,
            'author_id' => $author_id,
            'paper_id' => $paper_id,
            'publisher_id'=>$publisher_id,
            'sender'=> $this->user['type'],
            'pr_status' => PR_STATUS::PENDING,
            'payment_status' => PAYMENT_STATUS::NONE,
        ];
    
        $res = $this->UserModel->join_paper($data);
    
        if ($res) {
            $result = [
                'status' => 200,
                'message' => 'Publish Paper request sent successfully!',
                'data' => array_merge(['id' => $res], $data),
            ];
        } else {
            $result = ['status' => 500, 'message' => 'Failed to send the request!'];
        }
        $this->response($result, RestController::HTTP_OK);
}

public function get_publish_requests_get()
{
    $userId = $this->user['id'];

    $where = [];
    if($this->user['type'] == USER_TYPE::AUTHOR){
       $where['publish_requests.author_id'] = $userId;
    }else{
        $where['publish_requests.publisher_id'] = $userId;
    }
    
    $journals = $this->UserModel->getResearchPaperRequests($where);   
    if ($journals) {
        $result = [
            'status' => 200,
            'message' => 'Publish Requests fetched successfully',
            'data' => $journals
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No Requests found',
            'data' => []
        ];
    }
    $this->response($result, RestController::HTTP_OK);
}

public function approve_reject_publish_request_post($req_id)
{
    $status = $this->input->post('status'); 
    if (empty($req_id) || empty($status)) {
        $result = [
            'status' => 400,
            'message' => 'Request ID and status are required',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }


    
    // if (!in_array($status, [PR_STATUS::ACCEPT,PR_STATUS::REJECT])) {
    //     $result = [
    //         'status' => 400,
    //         'message' => 'Invalid status value',
    //     ];
    //     $this->response($result, RestController::HTTP_OK);
    //     return;
    // }

    $request = $this->UserModel->getAuthorRequestsById($req_id);

    if(empty($request)){
        $result = [
            'status' => 404,
            'message' => 'Request not found',
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }
    if($this->user['type']==USER_TYPE::AUTHOR && !in_array($status, [PR_STATUS::ACCEPT,PR_STATUS::REJECT])){
            $result = [
                'status' => 401,
                'message' => 'Invalid Status',
            ];
            $this->response($result, RestController::HTTP_OK);
            return;
    }
    

    $current_status = $request['pr_status'];

    if ($current_status != PR_STATUS::PENDING) {
        $result = [
            'status' => 400,
            'message' => "Action already taken",
        ];
        $this->response($result, RestController::HTTP_OK);
        return;
    }

    
    $update_data = [
        'pr_status' => $status,
    ];

    $updated = $this->UserModel->updatePublishRequestStatus($req_id, $update_data);

    if ($updated) {
        if ($status === PR_STATUS::ACCEPT) {
            $link_data = [
                'journal_id' => $request['journal_id'],
                'paper_id' => $request['paper_id'],
                'pr_id' => $req_id,
                'updated_at' => get_datetime()
            ];
        }

        $result = [
            'status' => 200,
            'message' => 'Request status updated successfully',
        ];
    } else {
        $result = [
            'status' => 500,
            'message' => 'Failed to update request status',
        ];
    }

    $this->response($result, RestController::HTTP_OK);
}

// get joined journals

public function list_joined_journals_get()
{
    $userId = $this->user['id'];

    $where = [];
    if($this->user['type'] == USER_TYPE::REVIEWER){
       $where['journal_reviewer_link.reviewer_id'] = $userId;
    }else{
        $jouranls = $this->UserModel->getPublisherJournals($userId);
        $journalIds = array_column($jouranls,'journal_id');
        $this->db->where_in('journal_reviewer_link.journal_id',!empty($journalIds) ? $journalIds : [0]);
    }
    
    $journals = $this->UserModel->get_joined_journals($where);   
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









// leave from joined journals

public function leave_journal_post()
    {
        $requestId = $this->input->post('request_id');

        if (empty($requestId)) {
            $result = [
                'status' => 400,
                'message' => 'Request ID is required',
            ];
            $this->response($result, RestController::HTTP_BAD_REQUEST);
            return;
        }



        $where = [];
        if($this->user['type'] == USER_TYPE::REVIEWER){
           $where['journal_reviewer_link.reviewer_id'] = $userId;
       
        }

        

        $deleteStatus = $this->UserModel->leaveJoinedJournal($requestId);

         
            $result = [
                'status' => 200,
                'message' => 'Record removed successfully',
            ];
            $this->response($result, RestController::HTTP_OK);
        
    }








public function get_research_papers_get()
{
    $this->load->model('UserModel');
    $journals = $this->UserModel->getresearchpapersByUserId($this->user['id']);
    if ($journals) {
        $result = [
            'status' => 200,
            'message' =>'research paper fetched successfully',
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



public function delete_research_get($id = 0)
{
    // $this->load->database();
    $this->load->model('UserModel');

    $id = intval($id);
    if ($id > 0) {
        $result = $this->UserModel->deleteResearchPaperById($id, $this->user['id']);
    } else {
        $result = ['status' => 400, 'message' => 'Valid ID is required.'];
    }

   
    $this->response($result, RestController::HTTP_OK);
}



public function update_research_paper_post($id = null)
{
    $this->load->model('UserModel');
    $this->load->helper('url');

    if (empty($id)) {
        $this->response(['status' => 400, 'message' => 'Invalid research paper ID.'], RestController::HTTP_BAD_REQUEST);
        return;
    }

    $fields = [
        'author_name',
        'author_contact',
        'author_email',
        'country',
        'affiliation',
        'department',
        'paper_title',
        'abstract',
        'keywords',
    ];

    $update_data = [];
    foreach ($fields as $field) {
        $input_value = $this->input->post($field);
        if ($input_value !== null && $input_value !== '') { 
            $update_data[$field] = $input_value;
        }
    }

  
     $co_authors = $this->input->post('co_authors');
     if (is_string($co_authors)) {
        $co_authors = json_decode($co_authors, true);
    }

   
    if (!empty($update_data) || !is_null($co_authors)) {
        $updated = $this->UserModel->update_research_paper($id, $update_data, $co_authors);

        if ($updated) {
            $this->response([
                'status' => 200,
                'message' => 'Research paper updated successfully!',
                'data' => array_merge(['paper_id' => $id], $update_data, ['co_authors' => $co_authors]),
            ], RestController::HTTP_OK);
        } else {
            $this->response(['status' => 500, 'message' => 'Failed to update the research paper.'], RestController::HTTP_BAD_REQUEST);
        }
    } else {
        $this->response(['status' => 400, 'message' => 'No valid data to update.'], RestController::HTTP_BAD_REQUEST);
    }
}

public function get_joined_reviewers_get()
{
    $userId = $this->user['id'];
    if ($this->user['type'] != USER_TYPE::PUBLISHER) {
        $this->response([
            'status' => 403,
            'message' => 'Access denied. Only publishers can view reviewers.',
            'data' => []
        ], RestController::HTTP_OK);
        return;
    }
    $journals = $this->UserModel->getPublisherJournals($userId);
    $journalIds = array_column($journals, 'journal_id');

    if (empty($journalIds)) {
        $this->response([
            'status' => 200,
            'message' => 'No journals found for this publisher.',
            'data' => []
        ], RestController::HTTP_OK);
        return;
    }
    $this->db->select('users.id, users.name, users.email, journal_reviewer_link.journal_id');
    $this->db->from('journal_reviewer_link');
    $this->db->join('users', 'journal_reviewer_link.reviewer_id = users.id');
    $this->db->where_in('journal_reviewer_link.journal_id', $journalIds);

    $reviewers = $this->db->get()->result_array();

    if (!empty($reviewers)) {
        $this->response([
            'status' => 200,
            'message' => 'Reviewers fetched successfully.',
            'data' => $reviewers
        ], RestController::HTTP_OK);
    } else {
        $this->response([
            'status' => 404,
            'message' => 'No reviewers found for your journals.',
            'data' => []
        ], RestController::HTTP_OK);
    }
}



public function get_research_by_id_get($id = null)
{
   
    $this->load->model('UserModel');

   
    if (!$id) {
        $result = [
            'status' => 400,
            'message' => 'Research paper  ID is required',
            'data' => null
        ];
        return $this->response($result, RestController::HTTP_BAD_REQUEST);
    }

    
    $research_paper = $this->UserModel->get_research_by_id($id);

  
    if ($research_paper) {
        $result = [
            'status' => 200,
            'message' => 'Research paper  fetched successfully',
            'data' => $research_paper
        ];
    } else {
        $result = [
            'status' => 404,
            'message' => 'No Research paper found with the given ID',
            'data' => null
        ];
    }

   
    return $this->response($result, RestController::HTTP_OK);
}




public function assign_request_to_reviewer_post($reviewer_id = null, $pr_id = null)
{
        $reviewer_id = intval($reviewer_id);
        $pr_id = intval($pr_id);
        $this->load->model('UserModel');
        $this->load->helper('url');
        if (empty($reviewer_id) || empty($pr_id)) {
            $result = ['status' => 400, 'message' => 'Reviewer ID or Paper ID missing.'];
            $this->response($result, RestController::HTTP_OK);
            return;
        }

        if(!$this->UserModel->publishRequestExists($pr_id)){
            $result = ['status' => 400, 'message' => 'Invalid Request ID.'];
                $this->response($result, RestController::HTTP_OK);
                return;
        }
        

        if(!$this->UserModel->reviewerExists($reviewer_id)){
            $result = ['status' => 400, 'message' => 'Invalid reviewer ID.'];
                $this->response($result, RestController::HTTP_OK);
                return;
        }
        
        //validate journal id
        if($this->user['type'] != USER_TYPE::PUBLISHER){
                return $this->response([
                    'status' => 400,
                    'message' => 'Only publishers can assign reviewers.',
                ], RestController::HTTP_OK);
            }
    
        $request = $this->UserModel->getPublishRequest($pr_id);
    
        if (!$request) {
            return $this->response([
                'status' => 404,
                'message' => 'Publish request not found.'
            ], RestController::HTTP_OK);
        }

        if ($request['pr_status'] !== 'accept') {
            return $this->response(['status' => 400, 'message' => 'Request status must be "accept" to assign a reviewer.'], RestController::HTTP_OK);
        }
        if ($request['publisher_id'] !== $this->user['id']) {
            return $this->response(['status' => 401, 'message' => 'You are not authorized to assign a reviewer to this request.'], RestController::HTTP_OK);
        }
        $update_data = [
            'assigned_reviewer' => $reviewer_id,
            'reviewer_remarks' => ''
        ];
        $update_result = $this->UserModel->updatePublishRequest($pr_id, $update_data);
    
        if ($update_result) {
            return $this->response(['status' => 200, 'message' => 'Reviewer assigned successfully.','data'=>$update_result], RestController::HTTP_OK);
        } else {
            return $this->response(['status' => 500, 'message' => 'Failed to assign reviewer.'], RestController::HTTP_OK);
        }
    }
}