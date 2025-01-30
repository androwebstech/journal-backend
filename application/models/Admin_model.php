<?php
defined('BASEPATH') or exit('No Access');
class Admin_model extends CI_model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(['common_helper']);
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
    $this->db->select('*');
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
    $this->db->select('*');
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

}