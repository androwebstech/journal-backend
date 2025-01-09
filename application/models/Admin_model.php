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

public function insert_contact($data)
    {
        $this->db->insert('contact_table', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    } 

    public function insert_journal($data)
    {
        $this->db->insert('journal_table', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    public function update_journal($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('journal_table', $data);
    
        return $this->db->affected_rows() > 0;
    }
    



}