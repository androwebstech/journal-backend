<?php
defined('BASEPATH') or exit('No Access');

function get_token_data($user){
	return [
		'id'=>$user['id'],
		'email'=>$user['email'],
		'type'=>$user['type'],
	];
}
function get_hash($str)
{
	return hash_hmac('md5',$str, '@caproject#');
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