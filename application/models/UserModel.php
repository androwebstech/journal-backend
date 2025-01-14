<?php
defined('BASEPATH') or exit('No Access');
class UserModel extends CI_model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(['common_helper']);
	}
public function getJournalsByUserId($user_id)
{
    $this->db->where("user_id",$user_id);
	$query = $this->db->get('journals');
	return $query->result_array();
}

public function deleteJournalById($id, $user_id)
{
    $this->db->where('journal_id', $id);
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('journals');

    if ($query->num_rows() > 0) {
        $this->db->where('journal_id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('journals');

        if ($this->db->affected_rows() > 0) {
            return ['status' => 200, 'message' => 'Journal deleted successfully!'];
        } else {
            return ['status' => 500, 'message' => 'Failed to delete the journal.'];
        }
    } else {
        return ['status' => 404, 'message' => 'No Journal found with the provided ID and User ID.'];
    }
}

public function searchReviewersByName($name)
    {
        $this->db->like('reviewer_name', $name);
        $query = $this->db->get('reviewers');

        return $query->result_array();
    }


public function register($user)
{
    $this->db->trans_start();

    $users_entry =[
        'email'=>$user['email'],
        'password'=>$user['password'],
        'type'=>$user['type'],
    ];
    $this->db->insert('users', $users_entry);

    $user_id = $this->db->insert_id();

    if ($user['type'] === USER_TYPE::AUTHOR) {
        $author = [
            'user_id'=>$user_id,
            'author_name' => $user['name'],
            'contact'=>$user['contact'],
        ];
        $this->db->insert('authors', $author);
    } elseif ($user['type'] === USER_TYPE::REVIEWER) {
        $reviewer = [
            'user_id'=>$user_id,
            'reviewer_name' => $user['name'], 
            'contact'=>$user['contact'],
            'approval_status'=> APPROVAL_STATUS::PENDING  
        ];
        $this->db->insert('reviewers', $reviewer);
    }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return false;
    }
    $users_entry['id'] = $user_id;
    unset($users_entry['password']);
    return $users_entry;
}

public function getProfileByType($user_id, $type)
{
    if ($type === USER_TYPE::AUTHOR) {
        return $this->db->where('user_id',$user_id)->get('authors')->row_array();
    } elseif ($type === USER_TYPE::REVIEWER) {
        return $this->db->where('user_id',$user_id)->get('reviewers')->row_array();
    } else {
        return [];
    }
}



}