<?php

defined('BASEPATH') or exit('No Access');
class Authentication_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['common_helper']);
    }

    public function validate_login($email, $password, $type)
    {
        $user = $this->db->select('*')->where('email', $email)->where('linked_ac', 0)->get('users')->row_array();
        if (!empty($user) && password_verify($password, $user['password']) && $type == $user['type']) {
            unset($user['password']);
            return $user;
        }

        return false;
    }

    // Function to insert contact data
    public function insert_contact($data)
    {
        return $this->db->insert('contact_table', $data);
    }


}
