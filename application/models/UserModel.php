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
    $this->db->trans_start(); // Start transaction

    // Insert only id, email, and type into the users table
    $user_entry = [
        'email' => $user_data['email'],
        'type' => $user_data['type'],
    ];
    $this->db->insert('users', $user_data);

    if ($this->db->affected_rows() > 0) {
        $inserted_id = $this->db->insert_id();
        $profile_data['user_id'] = $inserted_id;

        // Insert remaining data into the respective table
        if ($user_data['type'] === 'author') {
            $this->db->insert('authors', $profile_data);
        } elseif ($user_data['type'] === 'reviewer') {
            $this->db->insert('reviewers', $profile_data);
        }

        $this->db->trans_complete(); // Complete transaction

        if ($this->db->trans_status() === false) {
            return false; // Transaction failed
        }

        // Return the minimal user information from the users table
        return $this->db->where('id', $inserted_id)->get('users')->row_array();
    }

    return false; // User registration failed
}



}