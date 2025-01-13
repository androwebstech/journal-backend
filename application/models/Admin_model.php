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
        $this->db->insert('journals', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }
    
    public function update_journal($journal_id, $update_data)
    {
        $this->db->where('journal_id', $journal_id);
        $this->db->update('journals', $update_data);
    
        return $this->db->affected_rows() > 0;
    }
    



}