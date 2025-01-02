<?php
defined('BASEPATH') or exit('No Access');
class Admin_model extends CI_model
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

function get_staff() {
        ///SELECT users.*, department.name as dep_name FROM `users` JOIN department on users.department = department.id;
    return $this->db->select(' users.*, department.name ')
            ->from('users')
                ->join('department','users.department = department.id')
                ->get()->result_array();

	// return $this->db->where('user_type','staff')->get('users')->result_array();
}

function get_reports( $startdate = null, $enddate = null, $user_id = null, $department_id = null) {

    $this->db->select('daily_reports_table.*, users.fname, users.lname, department.name AS department_name');
    $this->db->from('daily_reports_table');
    $this->db->join('users', 'daily_reports_table.user_id = users.user_id');
    $this->db->join('department', 'daily_reports_table.department = department.id', 'left'); 




    if ($startdate && $enddate) {
        $this->db->where('daily_reports_table.created_at >=', $startdate);
        $this->db->where('daily_reports_table.created_at <=', $enddate.' 23:59:59');
    }

    if ($user_id) {
        $this->db->where('daily_reports_table.user_id', $user_id);
    }

    if ($department_id) {
        $this->db->where('daily_reports_table.department', $department_id);
    }

    $this->db->order_by('daily_reports_table.created_at', 'DESC');
    return $this->db->get()->result_array();
}


}