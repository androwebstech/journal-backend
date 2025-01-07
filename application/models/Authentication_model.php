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
	


	// function validate_login() {
	// 	$users = $this->db->select('*')->get('users')->result_array();
	
	// 	foreach ($users as $user) {
	// 		$hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);
	// 		$this->db->where('id', $user['id'])->update('users', ['password' => $hashed_password]);
	// 	}
	// }
	
	
}