<?php

defined('BASEPATH') or exit('No Access');

function get_token_data($user)
{
    return [
        'id' => $user['id'],
        'email' => $user['email'],
        'type' => $user['type'],
    ];
}
function get_hash($str)
{
    return hash_hmac('md5', $str, '@caproject#');
}
function safe_image($path)
{
    if (!empty($path) && file_exists(FCPATH . $path)) {
        return base_url($path);
    } else {
        return base_url('/assets/no-user.jpg');
    }
}
function get_datetime()
{
    return date('Y-m-d H:i:s');
}

function csrf($key)
{
    $CI = &get_instance();
    if ($key == 'name') {
        return $CI->security->get_csrf_token_name();
    } elseif ($key == 'hash') {
        return $CI->security->get_csrf_hash();
    }
}
function getUniqueCode($n = 6)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $code .= $characters[$index];
    }
    $CI = &get_instance();
    $chk = $CI->db->where('code', $code)->count_all_results('users');
    if ($chk > 0) {
        $code = getUniqueCode();
    }
    return $code;
}

function password_reset_request_mail($token)
{

    $frontend = isDevEnv() ? 'http://localhost:3000' : 'http://ifrj-app.solutionspool.in';

    $link = $frontend.'/password-reset/' . base64_encode($token);
    return <<<EMAIL
<html>
<head><title>Password Reset Request</title></head><body>
<div>Please reset your password using below link.</div>
<div><i><strong>Note:</strong> Link is valid only for 10 minute(s).</i></div>
<div><a href="$link">$link</a></div>
</body>
</html>
EMAIL;
}
function isDevEnv()
{
    return $_SERVER['HTTP_HOST'] == 'localhost';
}
