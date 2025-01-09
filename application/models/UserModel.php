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
	public function register($data)
{
    $this->db->insert('users', $data);
    if ($this->db->affected_rows()>0) {
        $inserted_id = $this->db->insert_id();
        return $this->db->where('id', $this->db->insert_id())->get('users')->row_array();
    }
    return false;
}
public function get_all_journals()
{
	$query = $this->db->get('journal_table');
	return $query->result_array();
}



}