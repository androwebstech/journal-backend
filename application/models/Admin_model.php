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


    


// public function getAuthors()
// {
    
//     $this->db->select('
//         users.*, 
//         CONCAT("' . base_url('') . '", users.profile_image) AS profile_image
//     ');
//     $this->db->from('users');
//     $this->db->where('type', USER_TYPE::AUTHOR);
//     $query = $this->db->get();

//     if ($query->num_rows() > 0) {
//         return $query->result_array();
//     } else {
//         return null;
//     }
// }
   

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

    public function deleteReviewer($id)
    {
        $this->db->where('id', $id);
        $this->db->where('type', USER_TYPE::REVIEWER);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $this->db->where('id', $id);
            $this->db->where('type', USER_TYPE::REVIEWER);
            $this->db->delete('users');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Reviewer deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the reviewer.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Reviewer found with the provided ID.'];
        }
    }


    public function deleteJournal($id)
    {
        $this->db->where('journal_id', $id);
        $query = $this->db->get('journals');

        if ($query->num_rows() > 0) {
            $this->db->where('journal_id', $id);
            $this->db->delete('journals');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Journal deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the Journal.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Journal found with the provided ID.'];
        }
    }

    public function deleteAuthor($id)
    {
        $this->db->where('id', $id);
        $this->db->where('type', USER_TYPE::AUTHOR);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $this->db->where('id', $id);
            $this->db->where('type', USER_TYPE::AUTHOR);
            $this->db->delete('users');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Author deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the author.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Author found with the provided ID.'];
        }
    }

    public function deletePublication($id)
    {
        $this->db->where('ppuid', $id);
        $query = $this->db->get('published_papers');

        if ($query->num_rows() > 0) {
            $this->db->where('ppuid', $id);
            $this->db->delete('published_papers');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Publication deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the Publication.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Publication found with the provided ID.'];
        }
    }

    public function deleteResearchPaper($id)
    {
        $this->db->where('paper_id', $id);
        $query = $this->db->get('research_papers');

        if ($query->num_rows() > 0) {
            $this->db->where('paper_id', $id);
            $this->db->delete('research_papers');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Research Paper deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the Research Paper.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Research Paper found with the provided ID.'];
        }
    }

    public function deleteContactUs($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('contact_table');

        if ($query->num_rows() > 0) {
            $this->db->where('id', $id);
            $this->db->delete('contact_table');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Contact deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the Contact.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Contact found with the provided ID.'];
        }
    }
public function getAuthors($filters = [], $limit = 500, $offset = 0, $searchString = '')
{
    $this->applyAuthorSearchFilter($filters, $searchString);
    
    $this->db->select('*,"" as password,(SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name,
        IF(profile_image="","",CONCAT("' . base_url('') . '", profile_image)) as profile_image,
        IF(doc1="","",CONCAT("' . base_url('') . '", doc1)) as doc1,
        IF(doc2="","",CONCAT("' . base_url('') . '", doc2)) as doc2,
        IF(doc3="","",CONCAT("' . base_url('') . '", doc3)) as doc3
    ');
    
    $this->db->order_by('id', 'ASC');
    $this->db->limit($limit, $offset);
    return $this->db->get('users')->result_array();
}

public function applyAuthorSearchFilter($filters = [], $searchString = '')
{
    $searchColumns = ['name', 'research_area'];
    $filterColumns = ['department', 'designation', 'country', 'state', 'approval_status'];
    
    if (!empty($searchString)) {
        $this->db->or_group_start();
        foreach ($searchColumns as $column) {
            $this->db->or_like($column, $searchString);
        }
        $this->db->group_end();
    }
    
    if (!empty($filters) && is_array($filters)) {
        foreach ($filters as $key => $value) {
            if (!in_array($key, $filterColumns) || empty($value)) {
                continue;
            }
            if (is_numeric($value)) {
                $this->db->where($key, $value);
            } elseif (is_string($value)) {
                $this->db->like($key, $value);
            }
        }
    }
    
    $this->db->where('type', USER_TYPE::AUTHOR);
}

public function getAuthorsCount($filters = [], $searchString = '')
{
    $this->applyAuthorSearchFilter($filters, $searchString);
    return $this->db->count_all_results('users');
}
public function getPublicationns($filters = [], $limit = 500, $offset = 0, $searchString = '')
{
    $this->applyPublicationSearchFilter($filters, $searchString);
    
    $this->db->select('*,(SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name,
    ');
    
    $this->db->order_by('id', 'ASC');
    $this->db->limit($limit, $offset);
    return $this->db->get('published_papers')->result_array();
}

public function applyPublicationSearchFilter($filters = [], $searchString = '')
{
    $searchColumns = ['paper_title', 'indexing_with', 'publication_year'];
    $filterColumns = ['paper_type', 'designation', 'country', 'state'];
    
    if (!empty($searchString)) {
        $this->db->or_group_start();
        foreach ($searchColumns as $column) {
            $this->db->or_like($column, $searchString);
        }
        $this->db->group_end();
    }
    
    if (!empty($filters) && is_array($filters)) {
        foreach ($filters as $key => $value) {
            if (!in_array($key, $filterColumns) || empty($value)) {
                continue;
            }
            if (is_numeric($value)) {
                $this->db->where($key, $value);
            } elseif (is_string($value)) {
                $this->db->like($key, $value);
            }
        }
    }

}

public function getPublicationsCount($filters = [], $searchString = '')
{
    $this->applyPublicationSearchFilter($filters, $searchString);
    return $this->db->count_all_results('published_papers');
}
}