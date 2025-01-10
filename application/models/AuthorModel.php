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
public function get_personal_details($id)
{
    return $this->db->get_where('author', ['id' => $id])->row_array();
}



}