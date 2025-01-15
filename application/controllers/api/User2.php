<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class User2 extends RestController {  
	private $user = [];
	function __construct()
	{
		parent::__construct();
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}

		$this->load->library('Authorization_Token');
		$this->load->library(['form_validation']);
		$this->load->helper(['url','common']);
		
		$headers = $this->input->request_headers(); 
		if (isset($headers['Authorization'])) {
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
			if($decodedToken['status']){
				$this->user = (array)$decodedToken['data'];
			}else{
				$this->response(['status'=>401,'message'=>$decodedToken['message']], RestController::HTTP_UNAUTHORIZED);
			}
		}
		else {
			$this->response(['status'=>400,'message'=>'Token Header missing'], RestController::HTTP_BAD_REQUEST);
		}

		// if($this->input->post('user_id')){
		// 	$uid = token_to_userid($_POST['user_id']);
		// 	if(empty($uid)){
		// 		$this->response(['status'=>false,'message'=>'Invalid or expired token'],RestController::HTTP_OK);
		// 		exit;
		// 	}
		// 	$_POST['user_id'] = $uid;
		// }
	}


	public function submit_application_post(){
		sleep(4);
		//Personal
		$this->form_validation->set_rules('first_name','First Name','trim|required');
		$this->form_validation->set_rules('last_name','Last Name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required|valid_email');
		$this->form_validation->set_rules('phone_code','Phone Code','trim|required');
		$this->form_validation->set_rules('mobile_no','Mobile No','trim|required');
		$this->form_validation->set_rules('country','Country','trim|required');
		$this->form_validation->set_rules('address','Address','trim|required');

		//Documents
		if(empty($_FILES['tax_id']['name']))
		$this->form_validation->set_rules('tax_id','Tax ID','trim|required');
		if(empty($_FILES['gov_id_front']['name']))
		$this->form_validation->set_rules('gov_id_front','Gov ID Front','trim|required');
		if(empty($_FILES['gov_id_back']['name']))
		$this->form_validation->set_rules('gov_id_back','Gov ID Back','trim|required');
		if(empty($_FILES['bank_statement']['name']))
		$this->form_validation->set_rules('bank_statement','Bank Statement','trim|required');

		//Loan details
		$this->form_validation->set_rules('loan_type','Loan Type','trim|required');
		$this->form_validation->set_rules('biance_id','Biance ID','trim|required');
		$this->form_validation->set_rules('loan_amount','Loan Amount','trim|required');
		if(empty($_FILES['wallet_qr']['name']))
		$this->form_validation->set_rules('wallet_qr','Wallet QR','trim|required');
		$this->form_validation->set_rules('relative_name','Relative Name','trim|required');
		$this->form_validation->set_rules('relative_mobile','Relative Mobile No','trim|required');

		//Lenders
		$this->form_validation->set_rules('lenders','Lenders','trim|required');
		
		//Payment
		if(empty($_FILES['payment_ss']['name']))
		$this->form_validation->set_rules('payment_ss','Payment Screenshot','trim|required');

		if($this->form_validation->run())
		{	
			$config['upload_path']          = './uploads/';
			$config['allowed_types']        = 'gif|jpg|png|jpeg|pdf';
			$config['encrypt_name']			= true;
			$config['max_size']             = 10000;

			$this->load->library('upload',$config);
			$uploaded = true;
			$data = [];
			if($uploaded && $this->upload->do_upload('tax_id')) $data['tax_id'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded && $this->upload->do_upload('gov_id_front')) $data['gov_id_front'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded && $this->upload->do_upload('gov_id_back')) $data['gov_id_back'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded && $this->upload->do_upload('bank_statement')) $data['bank_statement'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded && $this->upload->do_upload('wallet_qr')) $data['wallet_qr'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded && $this->upload->do_upload('payment_ss')) $data['payment_ss'] = $this->upload->data()['file_name']; else $uploaded = false;
			if($uploaded){
				$data['user_id'] = $this->user['id'];
				$data['first_name'] = $this->input->post('first_name');
				$data['last_name'] = $this->input->post('last_name');
				$data['email'] = $this->input->post('email');
				$data['phone_code'] = $this->input->post('phone_code');
				$data['mobile_no'] = $this->input->post('mobile_no');
				$data['country'] = $this->input->post('country');
				$data['address'] = $this->input->post('address');

				$data['loan_type'] = $this->input->post('loan_type');
				$data['biance_id'] = $this->input->post('biance_id');
				$data['loan_amount'] = $this->input->post('loan_amount');

				$data['relative_name'] = $this->input->post('relative_name');
				$data['relative_mobile'] = $this->input->post('relative_mobile');
				$data['lenders'] = $this->input->post('lenders');
				$data['created_at'] = get_datetime();
				$this->db->insert('applications',$data);
				$result = ['status'=>200,'message'=>'Application sent successfully.'];
			}else{
				$result = ['status'=>400,'message'=>$this->upload->display_errors()];
			}
		}else{
			$result = ['status'=>400,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
		//$this->form_validation->set_rules('personal','user_id','trim|required|numeric');	
	}

	public function application_list_get()
	{	
		$list = $this->db->where('user_id',$this->user['id'])->get('applications')->result_array();
		$this->response(['status'=>200,'list'=>$list],RestController::HTTP_OK);
	}

	public function user_data_post()
	{	
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$user_id = $this->input->post('user_id');
		 	$row = $this->db->where('id',$user_id)->get('users')->row_array();
    		
		   	if(!empty($row)){
		   	    $row['image']  = empty($row['image']) ? '' : base_url($row['image']);
		   	    $result = ['status'=>true,'message'=>'Success','data'=>$row];
		   	}
		   	else
		   		$result = ['status'=>false,'message'=>'Not found'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}

	public function update_user_post()
	{
		$this->load->database();
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('name','Full Name','trim|required');
		$this->form_validation->set_rules('mobile_no','Mobile No','trim|required');
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');

		if($this->form_validation->run())
		{
		   $user_id = $this->input->post('user_id');
		   
		   $count = $this->db->query("SELECT * from users where email ='".$_POST['email']."' and id != ".$user_id)->num_rows();
		   if($count){
		       $this->response(['status'=>false,'message'=>'Email already registered'],RestController::HTTP_OK);
		   }
		   $data  = array();
		   $data['name']        = $_POST['name'];
		   $data['email']       = $_POST['email'];
		   $data['mobile_no']   = $_POST['mobile_no'];
		   if(isset($_POST['is_suspicious'])){
			    $data['is_suspicious'] = $_POST['is_suspicious'];
			}
			if(isset($_POST['is_regular'])){
			    $data['is_regular'] = $_POST['is_regular'];
			}

		   if(!empty($_POST['password']))
		        $data['password'] = md5($_POST['password']);
		        
		   if(!empty($_FILES['image']['size']) && $_FILES['image']['size'] > 0)
		   {
		       if(in_array(strtolower($_FILES['image']['type']),['image/jpeg','image/jpg','image/png'])){
		           
		           $path    = '/uploads/users/'.time().'_'.$_FILES['image']['name'];
		           if(move_uploaded_file($_FILES['image']['tmp_name'],"./".$path)){
		               $data['image'] = $path;
		           }
		       }
		   }
		   
		   $res = $this->db->set($data)->where('id',$user_id)->update('users');
		   if($res)
		   {
		   		$result = ['status'=>true,'message'=>'Updated Successfully!'];
		   }
		   else
		   {
		   		$result = ['status'=>false,'message'=>'Somthing Went Wrong!'];
		   }
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}

	public function event_details_post()
	{	
		$this->form_validation->set_rules('event_id','event_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$event_id = $this->input->post('event_id');
		 	$row = $this->db->where('events_id',$event_id)->get('events')->row_array();
		   	if(!empty($row))
		   		$result = ['status'=>true,'message'=>'Success','data'=>$row];
		   	else
		   		$result = ['status'=>false,'message'=>'Not found'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}

    public function master_category_list_get()
	{	
		$list = $this->Category_model->get_master_category_list("status = 1");
        foreach($list as $key => $row)
        {
            empty($list[$key]['image']) || $list[$key]['image'] = base_url($list[$key]['image']);
            unset($list[$key]['status']);
        }
            
		$result = ['status'=>true,'message'=>'Success','data'=>$list];
		$this->response($result,RestController::HTTP_OK);
	}
	public function category_list_post()
	{	
	    
	    $this->form_validation->set_rules('master_category','master_category','trim|required|numeric');
	    $this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
		    $post = $this->input->post();
		    $list = $this->db->query("SELECT *,(SELECT COUNT(*) from requests where user_id = '".$post['user_id']."' and category_id = category.id and status = 1) as purchased FROM category where master_id = ".$post['master_category'])->result_array();
            foreach($list as $key => $row)
            {
                empty($list[$key]['image']) || $list[$key]['image'] = base_url($list[$key]['image']);
            }
            $result = ['status'=>true,'message'=>'Success','data'=>$list];
		}else{
		    $result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
	    
	    $this->response($result,RestController::HTTP_OK);
	}

	public function plan_list_post()
	{
		$this->form_validation->set_rules('category_id','category_id','trim|required|numeric|callback_category_exist',
										array('category_exist'=>'Invalid %s'));
		if($this->form_validation->run())
		{	
			$cat_id = $this->input->post('category_id');
		 	$list = $this->Plan_model->get_plan_list("category_id = ".$cat_id);
		    foreach($list as $key => $row){
		        empty($list[$key]['image']) || $list[$key]['image'] = base_url($list[$key]['image']);
		        unset($list['key']['status']);
		    }
           
		   	$result = ['status'=>true,'message'=>'Success','data'=>$list];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}

	public function plan_details_post()
	{
		$this->form_validation->set_rules('plan_id','plan_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$plan_id = $this->input->post('plan_id');
		 	$list = $this->Plan_model->get_plan_list("plans_id = ".$plan_id);
		   	if(!empty($list))
		   		$result = ['status'=>true,'message'=>'Success','data'=>$list[0]];
		   	else
		   		$result = ['status'=>false,'message'=>'Plan does not exists.'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}	

	function category_exist($id)
	{
		return (boolean)$this->db->where('id',$id)->count_all_results('category');
	}

	public function request_post()
	{
		$this->load->database();
		$this->form_validation->set_rules('plan_id','plan_id','trim|required|numeric');
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		$this->form_validation->set_rules('paid_amount','paid_amount','trim|required');
		$this->form_validation->set_rules('payment_reference','payment_reference','trim|required');
		$this->form_validation->set_rules('saccount_type','saccount_type','trim|required');
		//$this->form_validation->set_rules('saccount_id','saccount_id','trim|required');
		$this->form_validation->set_rules('saccount_password','saccount_password','trim|required');
		$this->form_validation->set_rules('game_name','game_name','trim');
		if($this->form_validation->run())
		{
			$plan = $this->db->where('plans_id',$_POST['plan_id'])->get('plans')->row_array();
			$user = $this->db->where('id',$_POST['user_id'])->get('users')->row_array();
			if(!empty($_POST['paid_from_wallet']) && $_POST['paid_from_wallet'] > $user['wallet_balance']){
			    $result = ['status'=>false,'message'=>'User has not sufficient balance in wallet']; 
			}
			else if(!empty($plan) && !empty($user))
			{
				$data = $_POST;
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['status']	= 0;
				$data['category_id'] = $plan['category_id'];
				$data['name'] = $user['name'];
				$data['mobile_no'] = $user['mobile_no'];
				$data['email']	= $user['email'];
				$data['no_of_diamonds'] = $plan['no_of_diamonds'];
				$data['plan_price'] = $plan['price'];
				$data['game_name'] = empty($_POST['game_name'])  ? '' : $_POST['game_name'];
				$data['saccount_type'] = $_POST['saccount_type'];
				$data['saccount_id'] = !empty($_POST['saccount_id']) ? $_POST['saccount_id'] : "";
				$data['saccount_password'] = $_POST['saccount_password'];
				
				$res = $this->db->insert('requests',$data);
				if($res){
				    if(!empty($data['paid_from_wallet'])){
				        $balance = $user['wallet_balance'] - $data['paid_from_wallet'];
				        $this->db->where('id',$user['id'])->set('wallet_balance',$balance)->update('users');
				        $this->db->set(['user_id'=>$user['id'],
				                        'amount'=>$data['paid_from_wallet'],
				                        'transaction_type'=>'debit',
				                        'timestamp'=>date('Y-m-d H:i:s'),
				                        'code'=>'USED_IN_ORDER',
				                        'remarks'=>'',
				            ])->insert('wallet_transaction');
				    }
				        $result = ['status'=>true,'message'=>'Request Created Successfully!'];   
				}
		   		else
		   			$result = ['status'=>false,'message'=>'Somthing Went Wrong!'];   
			}
		   	else
		   	{
		   		$result = ['status'=>false,'message'=>'Plan or user not exists!'];
		   	}
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}
    
    public function add_to_wallet_post()
	{
		$this->load->database();
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		$this->form_validation->set_rules('amount','amount','trim|required|numeric');
		$this->form_validation->set_rules('code','code','trim|required|in_list[ADDED_TO_WALLET,BY_ADMIN]');
		$this->form_validation->set_rules('remark','remark','trim');
		
		if($this->form_validation->run())
		{   
		    $userid = $this->input->post('user_id');
		    $amount = floatval($this->input->post('amount'));
		    $code = $this->input->post('code');
		    $remark = $this->input->post('remark') ?? "";
		    $in = $this->db->set(['user_id'=>$userid,
				                        'amount'=>$amount,
				                        'transaction_type'=>'credit',
				                        'timestamp'=>date('Y-m-d H:i:s'),
				                        'code'=>$code,
				                        'remarks'=>$remark,
				            ])->insert('wallet_transaction');
			if($in){
			    $this->db->query("UPDATE users set wallet_balance = wallet_balance + ".$amount." where id = ".$userid);
			    $result = ['status'=>1,'message'=>'Success'];
			}else{
			    $result =['status'=>0,'message'=>'Failed'];
			}
				
		}else{
		    $result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}

	public function get_request_list_post()
	{
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$user_id = $this->input->post('user_id');
		 	$list = $this->db->select('*,(SELECT pname from plans where plans_id = requests.plan_id ) as plan_name')->where("user_id = ".$user_id)->get('requests')->result_array();
		   	if(!empty($list))
		   		$result = ['status'=>true,'message'=>'Success','data'=>$list];
		   	else
		   		$result = ['status'=>false,'message'=>'No Records found.'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}

	public function shop_status_get()
	{	
		$res = $this->db->get('table_admin')->row_array();
		if(!empty($res))
		{   
		    $image = empty($res['popup_image']) ? '' : base_url($res['popup_image']);
		    $image2 = empty($res['popup_image2']) ? '' : base_url($res['popup_image2']);
			$result = ['status'=>true,
			            'message'=>'Success',
			            'shop_status'=>$res['shop_status'],
			            'wallet_status'=>$res['wallet_status'],
			            'popup_status'=>$res['popup_status'],
			            'popup_text'=>$res['popup_text'],
			            'popup_image'=>$image,
			            'popup_status2'=>$res['popup_status2'],
			            'popup_text2'=>$res['popup_text2'],
			            'popup_image2'=>$image2,
			            'app_update_url'=>$res['app_update_url'],
			            'app_update'=>$res['app_update'],
			            'app_version'=>$res['app_version']
			           ];
		}
		else
			$result = ['status'=>false,'message'=>'something went wrong'];	
		
		$this->response($result,RestController::HTTP_OK);	
	}
	
	public function help_get()
	{	
		$res = $this->db->get('table_admin')->row_array();
		if(!empty($res))
		{   
			$result = ['status'=>true,
			            'message'=>'Success',
			            'telegram_url'=>$res['telegram_url'],
			            'instagram_url'=>$res['instagram_url'],
			            'phone_url'=>$res['phone_url'],
			            'whatsapp_url'=>$res['whatsapp_url']
			           ];
		}
		else
			$result = ['status'=>false,'message'=>'something went wrong'];	
		
		$this->response($result,RestController::HTTP_OK);	
	}
	
	public function paytm_init_post(){
	        
        /*
        * import checksum generation utility
        * You can get this utility from https://developer.paytm.com/docs/checksum/
        */
        
        $this->form_validation->set_rules('USER_ID','USER_ID','trim|required');
        $this->form_validation->set_rules('ORDER_ID','ORDER_ID','trim|required');
        $this->form_validation->set_rules('AMOUNT','AMOUNT','trim|required|numeric');
        $this->form_validation->set_rules('ACTION','ACTION','trim|required|in_list[WALLET,ORDER]');
		if($this->form_validation->run())
		{	

            require_once("paytmallinone/PaytmChecksum.php");
          //  $mid = "xLOHFW59233346720160";//$_POST['MID'];
            $mid = MID;//$_POST['MID'];
            $Merchant_key = MKEY;//"BiB&IoFbuS6j#cLU";
            
            $orderid = $_POST['ORDER_ID'];
            $amount =  $_POST['AMOUNT'];
            $user_id = $_POST['USER_ID'];
            $action =  $_POST['ACTION'];
            
            
            $mid        = stripslashes($mid);
            $orderid    = stripslashes($orderid);
            $amount     = stripslashes($amount);
            $user_id    = stripslashes($user_id);
            $action     = stripslashes($action);
            
            $paytmParams = array();
            
            $paytmParams["body"] = array(
                "requestType"   => "Payment",
                "mid"           => $mid,
                "websiteName"   => "WEBSTAGING",
                "orderId"       => $orderid,
              //  "callbackUrl"   => base_url('paytm-response'),
               
              "callbackUrl"  =>   "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=".$orderid,
                "txnAmount"     => array(
                    "value"     => $amount,
                    "currency"  => "INR",
                ),
                "enablePaymentMode" => [["mode"=>"UPI"],
                                        ["mode"=>"BALANCE"],
                                        ["mode"=>"PPBL"],
                                        ["mode"=>"NET_BANKING"],
                                        ["mode"=>"CREDIT_CARD"],
                                        ["mode"=>"DEBIT_CARD"]
                                        ],
                "userInfo"      => array(
                    "custId"    => "CUST".$user_id,
                ),
            );
        

            /*
            * Generate checksum by parameters we have in body
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $Merchant_key);
            
            $paytmParams["head"] = array(
                "signature"	=> $checksum
            );
        
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            /* for Staging */
            // $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderid";
            
            /* for Production */
             $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=$mid&orderId=$orderid";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            $decode = json_decode($response,true);
            
            if(curl_errno($ch))
                $result = ['status'=>false,'message'=>'Error:'.curl_error($ch)];
            else if(empty($response) or !is_array($decode))
                $result = ['status'=>false,'message'=>'Something went wrong','responseType'=>$response];
            else{
                
                $status = $decode['body']['resultInfo']['resultStatus'];
                if($status == 'S'){
                    $this->db->set(['order_id'=>$orderid,
                                    'user_id'=>$user_id,
                                    'amount'=>$amount,
                                    'status'=>0,
                                    'action'=>$action,
                                    'created_at'=>date('Y-m-d H:i:s'),
                                ])->insert('paytm_transactions');
                    $result = ['status'=>true,'message'=>$decode['body']['resultInfo']['resultMsg'],'token'=>$decode['body']['txnToken']];
                }
                else
                    $result = ['status'=>false,'message'=>$decode['body']['resultInfo']['resultMsg'],'finalRes'=>$decode];
            }
		}
		else{
		    $result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);
	}
	
	public function validate_id_post() {
	    $this->form_validation->set_rules('game_id','game_id','trim|required');
		if($this->form_validation->run())
		{	
			$game_id = $this->input->post('game_id');
		 	$list = $this->db->query("SELECT * FROM `requests` where TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(saccount_id, ':', -1), ':', -1)) = '".$game_id."'")->row_array();
		   	if(!empty($list))
		   		$result = ['status'=>true,'valid'=>false,'message'=>'Game id already used','data'=>$list];
		   	else
		   		$result = ['status'=>true,'valid'=>true,'message'=>'This is Unique Game ID'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}
	
	public function update_transaction_status_post() {
	    $this->form_validation->set_rules('order_id','order_id','trim|required');
		if($this->form_validation->run())
		{	
		    $order_id =  $this->input->post('order_id');
		    
		    $row = $this->db->where(['order_id'=>$order_id,'status'=>0])->get('paytm_transactions')->row_array();
		    if(empty($row)){
		       $this->response(['status'=>false,'message'=>'Invalid order id or final status already fetched'],RestController::HTTP_OK);	
    		    exit; 
		    }
	        header("Pragma: no-cache");
    		header("Cache-Control: no-cache");
    		header("Expires: 0");
    		require_once("paytmallinone/PaytmChecksum.php");
    		
    		$paytmParams["body"] = array(
    			"mid"           => MID,
    			"orderId"       => $order_id,
    		);
    
    		$url = "https://securegw.paytm.in/v3/order/status";
    		$checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), MKEY);
    		$paytmParams['head']['signature'] = $checksum;
    		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
    		$ch = curl_init($url);
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    			'Content-Type: application/json',                                                                                
    			'Content-Length: ' . strlen($post_data))                                                                       
    		);
    		$response = curl_exec($ch);
    		if(curl_errno($ch))
    		{
    			$res = array('status'=>false,'message'=>curl_error($ch));	
    			curl_close($ch);
    			$this->response($res,RestController::HTTP_OK);	
    			exit;
    		}
    		$payload = json_decode($response,true);
    	
    		$paytmChecksum = "";
    		if(!empty($payload['head']['signature']))
    			$paytmChecksum = $payload['head']['signature'];
    		
    		$isVerifySignature = PaytmChecksum::verifySignature(json_encode($payload['body'],JSON_UNESCAPED_SLASHES), MKEY, $paytmChecksum);
    
    		if($isVerifySignature)
    		{
    			$result = $payload['body'];
    			if($result['resultInfo']['resultStatus'] == 'TXN_FAILURE')
    			{
    				$this->db->set(array('status'=>2,
    						'updated_at'=>date('Y-m-d H:i:s'),
    						'resp_code'=>$result['resultInfo']['resultCode'],
    						'resp_msg'=>$result['resultInfo']['resultMsg'],
    						)
    				)->where('order_id',$order_id)->update('paytm_transactions');
    				$res = array('status'=>true,'result'=>$result['resultInfo']);
    			}
    			else if($result['resultInfo']['resultStatus'] == 'TXN_SUCCESS')
    			{
    				$row = $this->db->where('order_id',$order_id)->get('paytm_transactions')->row_array();
    				$this->db->set(array('status'=>1,
    						'updated_at'=>date('Y-m-d H:i:s'),
    						'resp_code'=>$result['resultInfo']['resultCode'],
    						'resp_msg'=>$result['resultInfo']['resultMsg'],
    						'txn_id'=>$result['txnId'],
    						'txn_amount'=>$result['txnAmount'],
    						'bank_txn_id'=>$result['bankTxnId'],
    						)
    				)->where('order_id',$order_id)->update('paytm_transactions');
    				
    				$res = array('status'=>true,'result'=>$result['resultInfo']);
    			}
    			else{
    				$res = array('status'=>true,'result'=>$result['resultInfo']);
    			}
    		}
    		else{
    			$res = array('status'=>false,'message'=>'Unverified');	
    		}
		}
		else
		{
			$res = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($res,RestController::HTTP_OK);	
	}
	
	public function get_transaction_list_post()
	{
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$user_id = $this->input->post('user_id');
		 	$list = $this->db->select('*')->where("user_id = ".$user_id)->order_by('transaction_no','desc')->get('upi_transactions')->result_array();
		   	if(!empty($list))
		   		$result = ['status'=>true,'message'=>'Success','data'=>$list];
		   	else
		   		$result = ['status'=>false,'message'=>'No Records found.'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}
	
	public function get_paytm_transaction_list_post()
	{
		$this->form_validation->set_rules('user_id','user_id','trim|required|numeric');
		if($this->form_validation->run())
		{	
			$user_id = $this->input->post('user_id');
		 	$list = $this->db->select('*')->where("user_id = ".$user_id)->order_by('transaction_no','desc')->get('paytm_transactions')->result_array();
		   	if(!empty($list))
		   		$result = ['status'=>true,'message'=>'Success','data'=>$list];
		   	else
		   		$result = ['status'=>false,'message'=>'No Records found.'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}

    public function generateUPIURL_post(){
        
        $this->form_validation->set_rules('user_id','user_id','trim|required');
        $this->form_validation->set_rules('client_txn_id','txn_id','trim|required');
        $this->form_validation->set_rules('amount','amount','trim|required');
        $this->form_validation->set_rules('p_info','p_info','trim|required');
        $this->form_validation->set_rules('customer_name','customer_name','trim|required');
        $this->form_validation->set_rules('customer_email','customer_email','trim|required');
        $this->form_validation->set_rules('customer_mobile','customer_mobile','trim|required');
		if($this->form_validation->run())
		{	
            $user_id = $this->input->post('user_id');
        
            $json_array = array("key"=>"40ebc3bf-b41d-49a2-af95-58aee2e4af95",
                                "client_txn_id"=>$this->input->post('client_txn_id'),
                                "amount"=>$this->input->post('amount'),
                                "p_info"=>$this->input->post('p_info'),
                                "customer_name"=>$this->input->post('customer_name'),
                                "customer_email"=>$this->input->post('customer_email'),
                                "customer_mobile"=>$this->input->post('customer_mobile'),
                                "redirect_url"=>base_url('login/upi_result'),
                            );
             $url = "https://merchant.upigateway.com/api/create_order";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_array));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            if(curl_errno($ch))
                $result = ['status'=>false,'msg'=>'Error:'.curl_error($ch)];
            else{
                $result = json_decode($response,true);
                if($result['status'] == true){
                    $this->db->set(['order_id'=>$json_array['client_txn_id'],
                                    'user_id'=>$user_id,
                                    'amount'=>$json_array['amount'],
                                    'status'=>0,
                                    'action'=>'',
                                    'created_at'=>date('Y-m-d H:i:s'),
                                ]);
                    if(!$this->db->insert('upi_transactions')){
                        $result = ['status'=>false,'Message'=>'Unable to create record in DB'];      
                    }
                }
                //$result = ['status'=>true,'Message'=>'Error:'.curl_error($ch)];
            }
            
		}else{
		 $result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
    }

    public function update_upi_transaction_status_post() {
	    $this->form_validation->set_rules('order_id','order_id','trim|required');
		if($this->form_validation->run())
		{	
		    $order_id =  $this->input->post('order_id');
		    
		    $row = $this->db->where(['order_id'=>$order_id,'status'=>0])->get('upi_transactions')->row_array();
		    if(empty($row)){
		       $this->response(['status'=>false,'message'=>'Invalid order id or final status already fetched'],RestController::HTTP_OK);	
    		    exit; 
		    }
	        
	        $json_array = array('key'=>'40ebc3bf-b41d-49a2-af95-58aee2e4af95',
	                            'client_txn_id'=>$this->input->post('order_id'),
	                            'txn_date'=>date('d-m-Y',strtotime($row['created_at'])),
	                           );
	        
    		$url = "https://merchant.upigateway.com/api/check_order_status";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_array));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            
    		if(curl_errno($ch))
    		{
    			$res = array('status'=>false,'message'=>curl_error($ch));	
    			curl_close($ch);
    			$this->response($res,RestController::HTTP_OK);	
    			exit;
    		}
    		$result = json_decode($response,true);
    		if($result['status'] == true)
    		{
    			if($result['data']['status'] == 'created' || $result['data']['status'] == 'scanning')
    			{
    				$res = array('status'=>true,'result'=>'Transaction is pending');
    			}
    			else if($result['data']['status'] == 'success')
    			{
    				$row = $this->db->where('order_id',$order_id)->get('upi_transactions')->row_array();
    				$this->db->set(array('status'=>1,
    						'updated_at'=>date('Y-m-d H:i:s'),
    						'resp_code'=>$result['data']['status'],
    						'resp_msg'=>$result['data']['remark'],
    						'txn_id'=>$result['data']['upi_txn_id'],
    						'txn_amount'=>$result['data']['amount'],
    						)
    				)->where('order_id',$order_id)->update('upi_transactions');
    				
    				$in = $this->db->set(['user_id'=>$row['user_id'],
				                        'amount'=>$result['data']['amount'],
				                        'transaction_type'=>'credit',
				                        'timestamp'=>date('Y-m-d H:i:s'),
				                        'code'=>'ADDED_TO_WALLET',
				                        'remarks'=>'#'.$result['data']['upi_txn_id'].' . Amount added to wallet by UPI',
				            ])->insert('wallet_transaction');
        			
        			if($in){
        			    $this->db->query("UPDATE users set wallet_balance = wallet_balance + ".$result['data']['amount']." where id = ".$row['user_id']);
        			}
    				
    				$res = array('status'=>true,'result'=>$result);
    			}
    			else{
    			    $this->db->set(array('status'=>2,
    						'updated_at'=>date('Y-m-d H:i:s'),
    						'resp_code'=>$result['data']['status'],
    						'resp_msg'=>$result['data']['remark'],
    						)
    				)->where('order_id',$order_id)->update('upi_transactions');
    				
    				$res = array('status'=>true,'result'=>$result);
    			}
    		}
    		else{
    			$res = array('status'=>false,'message'=>$result);	
    		}
		}
		else
		{
			$res = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($res,RestController::HTTP_OK);	
	}


	public function add_to_cart_post() {

	    $this->form_validation->set_rules('user_id','User ID','trim|required');
		$this->form_validation->set_rules('product_id','Product ID','trim|required');
		if($this->form_validation->run())
		{	
			
			$pro = $this->input->post('product_id');
			$user_id = $this->input->post('user_id');
			$chk = $this->db->where('product_id',$pro)->where('user_id',$user_id)->get('cart')->row_array();
			$product = $this->db->where('plans_id',$pro)->get('plans')->row_array();
			if(!empty($product)){

				if(empty($chk))
					$this->db->insert('cart',['product_id'=>$pro,'user_id'=>$user_id,'type'=>$product['type'],'qty'=>1]);
				else if($product['type'] == 'voucher')
					$this->db->where(['product_id'=>$pro,'user_id'=>$user_id,])->set("qty","qty+1",false)->update('cart');
					
				$result = ['status'=>true,'message'=>'Added to cart'];
			}else{
				$result = ['status'=>true,'message'=>'Unknown product'];
			}
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}

	public function delete_from_cart_post() {

	    $this->form_validation->set_rules('user_id','User ID','trim|required');
		$this->form_validation->set_rules('product_id','Product ID','trim|required');
		$this->form_validation->set_rules('remove','Remove','trim|required');
		if($this->form_validation->run())
		{	
			$pro = $this->input->post('product_id');
			$user_id = $this->input->post('user_id');
			$remove = $this->input->post('remove');

			$chk = $this->db->where('product_id',$pro)->where('user_id',$user_id)->get('cart')->row_array();
			if(!empty($chk))
			{
				if($chk['qty'] == '1' || $remove == 'yes')
					$this->db->where('cart_id',$chk['cart_id'])->delete('cart');
				else
					$this->db->where(['product_id'=>$pro,'user_id'=>$user_id,])->set("qty","qty-1",false)->update('cart');
			}
			$result = ['status'=>true,'message'=>'Deleted from cart'];
		}
		else
		{
			$result = ['status'=>false,'message'=>strip_tags(validation_errors())];
		}
		$this->response($result,RestController::HTTP_OK);	
	}

}
