<?php
defined('BASEPATH') or exit('No Access');
class Authentication_model extends CI_model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(['common_helper']);
	}

	function validate_login($email, $password,$type) {
		$user = $this->db->select('*')->where('email', $email)->get('users')->row_array();
		if (!empty($user) && password_verify($password,$user['password']) && $type==$user['type']) { // Compare plain text password
			return $user; 
		}
		
		return false;
	}


	public function getProfileByType($user_id, $type) {
    if ($type === 'author') {
        return $this->db->get_where('authors', ['user_id' => $user_id])->row_array();
    } elseif ($type === 'reviewer') {
        return $this->db->get_where('reviewers', ['user_id' => $user_id])->row_array();
    }
    return [];
}


	// Function to insert contact data
    function insert_contact($data)
    {
        return $this->db->insert('contact_table', $data);
    }

	
}