<?php
defined('BASEPATH') or exit('No Access');

function get_hash($str)
{
	return hash_hmac('md5',$str, '@caproject#');
}
function token_to_userid($token = false){
	$CI = &get_instance();
	$token = $token ? $token : $CI->session->userdata('auth_token');
	if(!empty($token)){
		$user = $CI->db->where('auth_token',$token)
						->where('status','1')
						->order_by('auth_id','desc')
						->get('auth_table')
						->row_array();
		if(!empty($user)){
			return $user['user_id'];
		}
	}
	return 0;
}
function get_userdata($uid)
{
	$CI = &get_instance();
	$user = $CI->db->where('id',$uid)->get('users')->row_array();
	$user['cart'] = $CI->db->where('user_id',$uid)->get('cart')->result_array();
	return $user;
}

function get_cart_with_data($uid)
{
	if(!empty($uid)){
		$CI = &get_instance();
		$cart = $CI->db->select('cart.*,plans.*')
						->from('cart')
						->join('plans','plans.plans_id=cart.product_id','left')
						->where('cart.user_id',$uid)
						->get();
						
		$data = $cart->result_array();
		return $data;
	}
	else
	return [];

}

function default_image($for)
{
	switch ($for) {
		case 'category':
			return 'admin_assets/noimage.png';
		case 'plans':
			return 'admin_assets/noimage.png';
		case 'events':
			return 'admin_assets/noimage.png';
		default:
			return '';
	}
}
function get_datetime()
{
	return date('Y-m-d H:i:s');
}

function csrf($key)
{	
	$CI = &get_instance();
	if($key == 'name')
		return $CI->security->get_csrf_token_name();
	else if($key == 'hash')
		return $CI->security->get_csrf_hash();
}
function getUniqueCode($n = 6){
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
  
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $code .= $characters[$index];
    }
    $CI = &get_instance();
    $chk = $CI->db->where('code',$code)->count_all_results('users');
    if($chk > 0){
        $code = getUniqueCode();
    }
    return $code;  
}