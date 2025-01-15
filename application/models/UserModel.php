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
    public function get_all_journals()
    {
        $query = $this->db->get('journals');
        return $query->result_array();
    }


    public function register($user)
    {
        if($this->db->insert('users', $user)){
            $new = $this->db->where('id',$this->db->insert_id())->get('users')->row_array();
            unset($new['password']);
            return $new;
        }
        return false;
    }

    public function getUserById($id)
    {
        $user = $this->db->where('id', $id)->get('users')->row_array();
        if(isset($user['password']))
            unset($user['password']);
        return $user;
    }

}