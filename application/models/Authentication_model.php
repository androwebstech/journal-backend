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
		if (!empty($user) && password_verify($password,$user['password']) && $type==$user['type']) { 
			unset($user['password']);
			return $user; 
		}
		
		return false;
	}

	// Function to insert contact data
    function insert_contact($data)
    {
        return $this->db->insert('contact_table', $data);
    }

	
}