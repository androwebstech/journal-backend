<?php
defined('BASEPATH') or exit('No Access');
class Admin_model extends CI_model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(['common_helper']);
        $this->load->helper('url');

	}
// 	public function register($data)
// {
//     $this->db->insert('users', $data);
//     if ($this->db->affected_rows()>0) {
//         $inserted_id = $this->db->insert_id();
//         return $this->db->where('id', $this->db->insert_id())->get('users')->row_array();
//     }
//     return false;
// }

function validate_login($email, $password) {
    $user = $this->db->select('*')->where('email', $email)->get('admin')->row_array();
    if (!empty($user) && password_verify($password,$user['password'])) { 
        unset($user['password']);
        return $user; 
    }
    
    return false;
}

public function register($data)
{
    $this->db->insert('admin', $data);
    if ($this->db->affected_rows() > 0) {
        $insert_id = $this->db->insert_id();
        return $this->db->where('admin_id', $insert_id)->get('admin')->row_array();
    }

    return false;
}



public function insert_contact($data)
    {
        $this->db->insert('contact_table', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    } 


    


public function getAuthors()
{
    
    $this->db->select('
        users.*, 
        CONCAT("' . base_url('') . '", users.profile_image) AS profile_image
    ');
    $this->db->from('users');
    $this->db->where('type', USER_TYPE::AUTHOR);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    } else {
        return null;
    }
}
   

public function getPublishers()
{
    $this->db->select('
        users.*, 
        CONCAT("' . base_url() . '", users.profile_image) AS profile_image
    ');
    $this->db->from('users');
    $this->db->where('type', USER_TYPE::PUBLISHER);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    } else {
        return null;
    }
}

public function getReviewers()
{
    $this->db->select('*');
    $this->db->from('users');
    $this->db->where('type', USER_TYPE::REVIEWER);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    } else {
        return null;
    }
}

public function getReviewerDetail($userId){
    $this->db->select('*');
    $this->db->from('users');
    $this->db->where('id', $userId);
    $this->db->where('type', USER_TYPE::REVIEWER);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->row_array();
    } else {
        return null;
    }
}

public function approveRejectReviewer($userId,$status){

    $this->db->where('id', $userId);
    $this->db->where('type', USER_TYPE::REVIEWER);
    $this->db->update('users', ['approval_status' => $status]);
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    return false;
}

public function approveRejectJournal($journalId,$status){
    $this->db->where('journal_id', $journalId);
    $this->db->update('journals', ['approval_status' => $status]);
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    return false;
}



public function getPublications()
{
    $this->db->select('*');
      $this->db->order_by('ppuid', 'DESC');
    $this->db->from('published_papers');
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    } else {
        return null;
    }
}




public function approveRejectPublication($ppuid,$status){
    $this->db->where('ppuid', $ppuid);
    $this->db->update('published_papers', ['approval_status' => $status]);
    if ($this->db->affected_rows() > 0) {
        return true;
    }
    return false;
}
public function getResearchPaperRequests()
    {
        $this->db->select('
        publish_requests.*,
            research_papers.paper_title, 
            users.name,
            journals.journal_name,
            CONCAT("' . base_url() . '", journals.image) as journal_image,
            CONCAT("' . base_url() . '", users.profile_image) as author_image,
            research_papers.department,
            (Select name from users where users.id = publish_requests.assigned_reviewer) AS reviewer_name
        ');
        $this->db->from('publish_requests');
        $this->db->join('users', 'publish_requests.author_id = users.id');
        $this->db->join('journals', 'publish_requests.journal_id = journals.journal_id');
        $this->db->join('research_papers', 'publish_requests.paper_id = research_papers.paper_id');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }


   public function getAdminId($adminId)
    {
        $query = $this->db->get_where('admin', ['admin_id' => $adminId]);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }


        public function updateAdmin($adminId, $data)
    {
        $this->db->where('admin_id', $adminId);
        return $this->db->update('admin', $data);
    }


}