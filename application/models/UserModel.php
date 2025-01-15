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
        if($this->db->insert('users', $user)){
            $new = $this->db->where('id',$this->db->insert_id())->get('users')->row_array();
            unset($new['password']);
            return $new;
        }
        return false;
    }

    public function getUserById($id)
    {
        $user = $this->db->where('id', $id)->get('users')->row_array();
        if(isset($user['password']))
            unset($user['password']);
        return $user;
    }


    public function get_journal_by_id($id)
    {
        $this->db->where('journal_id', $id);
        $query = $this->db->get('journals'); 
        if ($query->num_rows() > 0) {
            return $query->row_array(); 
        }

        return null; 
    }


    public function searchJournalsByName($name)
    {
        $this->db->like('journal_name', $name);
        $query = $this->db->get('journals');

        return $query->result_array();
    }


}