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
// 	public function register($data)
// {
//     $this->db->insert('users', $data);
//     if ($this->db->affected_rows()>0) {
//         $inserted_id = $this->db->insert_id();
//         return $this->db->where('id', $this->db->insert_id())->get('users')->row_array();
//     }
//     return false;
// }
public function get_all_journals()
{
	$query = $this->db->get('journals');
	return $query->result_array();
}


public function register($user_data, $profile_data)
{
    $this->db->trans_start();
    
    // Insert into `users` table
    $this->db->insert('users', $user_data);
    $user_id = $this->db->insert_id();

    if ($user_data['type'] === 'author') {
        $profile_data['user_id'] = $user_id;
        $this->db->insert('authors', $profile_data);
    } elseif ($user_data['type'] === 'reviewer') {
        $profile_data['user_id'] = $user_id;
        $this->db->insert('reviewers', $profile_data);
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        return false;
    }

    $user_data['id'] = $user_id;
    return $user_data;
}





public function getProfileByType($user_id, $type)
{
    if ($type === 'author') {
        $this->db->select('authors.author_name, authors.author_details, authors.department, authors.designation');
        $this->db->from('users');
        $this->db->join('authors', 'users.id = authors.user_id');
    } elseif ($type === 'reviewer') {
        $this->db->select('reviewers.reviewer_name, reviewers.profile_image, reviewers.reviewer_details, reviewers.approval_status');
        $this->db->from('users');
        $this->db->join('reviewers', 'users.id = reviewers.user_id');
    } else {
        return [];
    }

    $this->db->where('users.id', $user_id);
    $query = $this->db->get();

    return $query->row_array() ?? [];
}



}