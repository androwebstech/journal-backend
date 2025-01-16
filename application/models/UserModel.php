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

    public function getJournals($filters = [], $limit = 500, $offset = 0){

        $filterColumns = ['journal_name','publisher_name','country','state'];

        if(!empty($filters) &&  is_array($filters)){
            foreach($filters as $key => $value){
                if(!in_array($key, $filterColumns)) continue;
                if(is_numeric($value))
                    $this->db->where($key, $value);
                else if(is_string($value))
                    $this->db->like($key, $value);
            }
        }
        $this->db->select('*,"" as password,(SELECT name from countries where id = journals.country) as country_name, (SELECT name from states where id = journals.state) as state_name');
        // $this->db->join('')
        $this->db->limit($limit, $offset);
        // return $this->db->where('type',USER_TYPE::REVIEWER)->get('users')->result_array();
        $query = $this->db->get('journals');

        return $query->result_array();
    }
    

    
    public function getReviewers($filters = [], $limit = 500, $offset = 0, $searchString = ''){

        $this->applyReviewerSearchFilter($filters, $searchString);

        $this->db->select('*,"" as password,(SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name');
        $this->db->limit($limit, $offset);
        return $this->db->get('users')->result_array();
    }
    public function applyReviewerSearchFilter($filters = [], $searchString = '') {
        $searchColumns = ['name','research_area'];
        $filterColumns = ['department','designation','country','state'];

        if(!empty($searchString)){
            $this->db->or_group_start();
            foreach($searchColumns as $column){
                $this->db->or_like($column, $searchString);
            }
            $this->db->group_end();
        }

        if(!empty($filters) &&  is_array($filters)){
            foreach($filters as $key => $value){
                if(!in_array($key, $filterColumns)) continue;
                if(is_numeric($value))
                    $this->db->where($key, $value);
                else if(is_string($value))
                    $this->db->like($key, $value);
            }
        }

        $this->db->where('type',USER_TYPE::REVIEWER);
    }
    public function getReviewersCount($filters = [], $searchString = '') {
        $this->applyReviewerSearchFilter($filters, $searchString);
        return $this->db->count_all_results('users');
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


    public function updateUserById($id, $data)
    {
        // Exclude sensitive or non-updatable fields
        unset($data['email'], $data['created_at'], $data['password']);
    
        // Update user details in the database
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
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

    // public function searchJournalsByName($name)
    // {
    //     $this->db->like('journal_name', $name);
    //     $query = $this->db->get('journals');

    //     return $query->result_array();
    // }


    public function getCountries(){
        return $this->db->get('countries')->result_array();
    }
    public function getStates($countryId){
        $countryId = intval($countryId);
        return $this->db->where('country_id',$countryId)->get('states')->result_array();
    }





public function insert_journal($data)
{
    $this->db->insert('journals', $data);
    if ($this->db->affected_rows() > 0) {
        return $this->db->insert_id();
    }
    return false;
}

public function update_journal($journal_id, $update_data)
{

if ($journal_id && !empty($update_data)) {
  
    $this->db->where('journal_id', $journal_id);
    $this->db->update('journals', $update_data);

   
    return $this->db->affected_rows() > 0;
}

return false;
}


}