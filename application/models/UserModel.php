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

    // public function getJournals($filters = [], $limit = 500, $offset = 0){

    //     $filterColumns = ['journal_name','publisher_name','country','state'];

    //     if(!empty($filters) &&  is_array($filters)){
    //         foreach($filters as $key => $value){
    //             if(!in_array($key, $filterColumns)) continue;
    //             if(is_numeric($value))
    //                 $this->db->where($key, $value);
    //             else if(is_string($value))
    //                 $this->db->like($key, $value);
    //         }
    //     }
    //     $this->db->select('*,"" as password,(SELECT name from countries where id = journals.country) as country_name, (SELECT name from states where id = journals.state) as state_name');
    //     // $this->db->join('')
    //     $this->db->limit($limit, $offset);
    //     // return $this->db->where('type',USER_TYPE::REVIEWER)->get('users')->result_array();
    //     $query = $this->db->get('journals');

    //     return $query->result_array();
    // }
    

    
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
                if(!in_array($key, $filterColumns) || empty($value)) continue;
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



    public function getJournals($filters = [], $limit = 500, $offset = 0, $searchString = ''){

        $this->applyJournalSearchFilter($filters, $searchString);

        $this->db->select('*,"" as password,(SELECT name from countries where id = journals.country) as country_name, (SELECT name from states where id = journals.state) as state_name');
        $this->db->limit($limit, $offset);
        return $this->db->get('journals')->result_array();
    }
    public function applyJournalSearchFilter($filters = [], $searchString = '') {
        $searchColumns = ['journal_name','publisher_name','broad_research_area','eissn_no','pissn_no',];
        $filterColumns = ['country', 'publication_type', 'number_of_issue_per_year','review_type',];

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

        // $this->db->where('type',USER_TYPE::REVIEWER);
    //     $query = $this->db->get('journals');

    //    return $query->result_array();
    }
    public function getJournalsCount($filters = [], $searchString = '') {
        $this->applyJournalSearchFilter($filters, $searchString);
        return $this->db->count_all_results('journals');
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

    // public function searchReviewersByName($name)
    // {
    //     $this->db->like('reviewer_name', $name);
    //     $query = $this->db->get('reviewers');

    //     return $query->result_array();
    // }


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
        $user['profile_image'] =  safe_image($user['profile_image']);
        return $user;
    }


    public function updateUserById($id, $data)
    {
        // Exclude sensitive or non-updatable fields
        unset($data['email'], $data['created_at'], $data['password']);
    
        // Update user details in the database
        $this->db->where('id', $id);
         $this->db->update('users', $data);
        return $this->getUserById( $id);
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

    public function get_publication_by_id($id)
    {
        $this->db->where('ppuid', $id);
        $query = $this->db->get('published_papers'); 
        if ($query->num_rows() > 0) {
            return $query->row_array(); 
        }

        return null; 
    }
public function get_reviewer_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->where('type', 'reviewer');
        $this->db->select('*, "" as password');
        $query = $this->db->get('users'); 
        if ($query->num_rows() > 0) {
            return $query->row_array(); 
        }

        return null; 
    }
    // public function searchJournalsByName($name)
    // {
    //     $this->db->like('journal_name', $name);
    //     $query = $this->db->get('journals');
    // }
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

   
    return true;
}

return false;
}



// public function insert_research_submission($data)
// {
//     $this->db->insert('research_papers', $data);
public function insert_publication($data)
{
    $this->db->insert('published_papers', $data);
    if ($this->db->affected_rows() > 0) {
        return $this->db->insert_id();
    }
    return false;
}




public function getPublicationByUserId($id)
{
    $this->db->where('user_id', $id);
    $query = $this->db->get('published_papers'); 
    if ($query->num_rows() > 0) {
        return $query->result_array(); 
    }

    return false; 
}

public function deletePublicationById($id, $user_id)
{
    $this->db->where('ppuid', $id);
    $this->db->where('user_id', $user_id);
    $query = $this->db->get('published_papers');

    if ($query->num_rows() > 0) {
        $this->db->where('ppuid', $id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('published_papers');

        if ($this->db->affected_rows() > 0) {
            return ['status' => 200, 'message' => 'Publication deleted successfully!'];
        } else {
            return ['status' => 500, 'message' => 'Failed to delete the Publication.'];
        }
    } else {
        return ['status' => 404, 'message' => 'No Publication found with the provided ID and User ID.'];
    }
}

public function update_publication($id, $update_data)
{

    if ($id && !empty($update_data)) {
        $this->db->select('ppuid');
        $this->db->from('published_papers');
        $this->db->where('ppuid', $id);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            $this->db->where('ppuid', $id);
            $this->db->update('published_papers', $update_data);
            return true;
        }
        return false;
    }
    return false;
}    

public function getUserId($userId)
    {
        $query = $this->db->get_where('users', ['id' => $userId]);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }


public function updateUser($userId, $data)
    {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }


    
public function getPublishRequestsByUserId($id)
    {
        $this->db->select('
            publish_requests.pr_id,
            research_papers.author_name,
            research_papers.paper_title,
            journals.journal_name,
            users.name,
            publish_requests.sender,
            publish_requests.pr_status,
            publish_requests.payment_status,
            publish_requests.live_url,
            publish_requests.created_at,
            publish_requests.updated_at, 
        ');
        $this->db->from('publish_requests');
        $this->db->join('research_papers', 'research_papers.paper_id = publish_requests.paper_id', 'left');
        $this->db->join('users', 'users.id = publish_requests.publisher_id', 'left');
        $this->db->join('journals', 'journals.journal_id = publish_requests.journal_id', 'left');
        $this->db->where('publish_requests.publisher_id', $id);
    
        $query = $this->db->get();
        return $query->result_array();
    }

public function update_publish_request_status($id, $status)
    {
        $this->db->where('pr_id', $id);
        $query = $this->db->get('publish_requests');
        $current_status = $query->row('pr_status'); 
        // echo $current_status;
        // exit;
        if ($current_status != $status) {
            $this->db->where('pr_id', $id);
            $this->db->update('publish_requests', ['pr_status' => $status]);

            return true; 
        } else {
            return false; 
        }
    }


    public function join_journal($data)
{
    $this->db->insert('journal_join_requests', $data);
    if ($this->db->affected_rows() > 0) {
        return $this->db->insert_id();
    }
    return false;
}

}