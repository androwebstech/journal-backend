<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH.'/libraries/RestController.php';
require_once APPPATH.'/libraries/Format.php';
use chriskacerguis\RestServer\RestController;
use chriskacerguis\RestServer\Format;

class Web extends RestController
{
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->library(['form_validation']);
        $this->load->helper(['url','common_helper','security']);
        $this->load->model(['Authentication_model']);
        $this->load->model('UserModel');
    }
    public function token_get()
    {
        echo password_hash('password', PASSWORD_BCRYPT);
    }
    public function print_get()
    {
        $this->load->view('print_records');
    }

    public function reviewers_search_get($limit = 10, $page = 1)
    {
        $filters = $this->input->get() ?? [];
        $searchString = $this->input->get('search', true) ?? '';
        $limit = abs($limit) < 1 ? 10 : abs($limit) ;
        $page = abs($page) < 1 ? 1 : abs($page);

        $offset = ($page - 1) * $limit;
        $res = $this->UserModel->getReviewers($filters, $limit, $offset, $searchString);
        $count = $this->UserModel->getReviewersCount($filters, $searchString);

        $this->response([
            'status' => 200,
            'message' => 'Success',
            'data' => $res,
            'totalPages' => ceil($count / $limit),
            'currentPage' => $page,
        ], RestController::HTTP_OK);
    }


    public function journals_search_get($limit = 10, $page = 1)
    {
        $filters = $this->input->get() ?? [];
        $searchString = $this->input->get('search', true) ?? '';
        $limit = abs($limit) < 1 ? 10 : abs($limit) ;
        $page = abs($page) < 1 ? 1 : abs($page);

        $offset = ($page - 1) * $limit;
        $filters['status'] = APPROVAL_STATUS::APPROVED;
        $res = $this->UserModel->getJournals($filters, $limit, $offset, $searchString);
        $count = $this->UserModel->getJournalsCount($filters, $searchString);

        $this->response([
            'status' => 200,
            'message' => 'Success',
            'data' => $res,
            'totalPages' => ceil($count / $limit),
            'currentPage' => $page,
        ], RestController::HTTP_OK);
    }





    // public function journal_search_get($limit = 10, $offset = 0 ){
    //     $filters = $this->input->get() ?? [];
    //     $limit = intval($limit);
    //     $offset = intval($offset);
    //     $res = $this->UserModel->getJournals($filters, $limit, $offset);
    //     // echo $this->db->last_query();exit;
    //     $this->response([
    //         'status' => 200,
    //         'message' => 'Success',
    //         'data' => $res,
    //     ], RestController::HTTP_OK);
    // }



    // public function reviewer_search_post()
    // {
    //     $this->load->library('form_validation');

    //     $name = $this->input->post('name', true);

    //     $reviewers = $this->UserModel->searchReviewersByName($name);

    //     if (!empty($reviewers)) {
    //         $this->response([
    //             'status' => 200,
    //             'message' => 'Reviewers found.',
    //             'data' => $reviewers,
    //         ], RestController::HTTP_OK);
    //     } else {
    //         $this->response([
    //             'status' => 404,
    //             'message' => 'No reviewers found matching the given name.',
    //         ], RestController::HTTP_NOT_FOUND);
    //     }
    // }

    // public function journal_search_post()
    // {
    //     // $this->load->database();
    //     $this->load->library('form_validation');

    //     $this->form_validation->set_rules('name', 'Name', 'trim');

    //     if (!$this->form_validation->run()) {
    //         $this->response([
    //             'status' => 400,
    //             'message' => strip_tags(validation_errors()),
    //         ], RestController::HTTP_BAD_REQUEST);
    //         return;
    //     }

    //     $name = $this->input->post('name', true);

    //     $journals = $this->UserModel->searchJournalsByName($name);

    //     if (!empty($journals)) {
    //         $this->response([
    //             'status' => 200,
    //             'message' => 'Journals found.',
    //             'data' => $journals,
    //         ], RestController::HTTP_OK);
    //     } else {
    //         $this->response([
    //             'status' => 404,
    //             'message' => 'No journals found matching the given name.',
    //         ], RestController::HTTP_NOT_FOUND);
    //     }
    // }

    public function get_countries_get()
    {
        $countries = $this->UserModel->getCountries();
        $this->response([
            'status' => 200,
            'message' => 'Success',
            'data' => $countries,
        ], RestController::HTTP_OK);
    }

    public function get_states_get($country_id = 0)
    {

        $country_id = intval($country_id);
        if (!empty($country_id)) {
            $states = $this->UserModel->getStates($country_id);
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $states,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Country Id missing',
            ], RestController::HTTP_OK);
        }

    }



    public function get_journal_by_id_get($id = null)
    {

        $this->load->model('UserModel');


        if (!$id) {
            $result = [
                'status' => 400,
                'message' => 'Journal ID is required',
                'data' => null
            ];
            return $this->response($result, RestController::HTTP_BAD_REQUEST);
        }


        $journal = $this->UserModel->get_journal_by_id($id);


        if ($journal) {
            $result = [
                'status' => 200,
                'message' => 'Journal fetched successfully',
                'data' => $journal
            ];
        } else {
            $result = [
                'status' => 404,
                'message' => 'No journal found with the given ID',
                'data' => null
            ];
        }


        return $this->response($result, RestController::HTTP_OK);
    }

    public function get_reviewer_by_id_get($id = null)
    {

        $this->load->model('UserModel');


        if (!$id) {
            $result = [
                'status' => 400,
                'message' => 'reviewer ID is required',
                'data' => null
            ];
            return $this->response($result, RestController::HTTP_BAD_REQUEST);
        }


        $reviewer = $this->UserModel->get_reviewer_by_id($id);


        if ($reviewer) {
            $result = [
                'status' => 200,
                'message' => 'reviewer fetched successfully',
                'data' => $reviewer
            ];
        } else {
            $result = [
                'status' => 404,
                'message' => 'No reviewer found with the given ID',
                'data' => null
            ];
        }


        return $this->response($result, RestController::HTTP_OK);
    }

    public function get_publication_by_User_id_get($id = null)
    {
        $this->load->model('UserModel');

        if (!$id) {
            $result = [
                'status' => 400,
                'message' => 'Publication ID is required',
                'data' => null
            ];
            return $this->response($result, RestController::HTTP_BAD_REQUEST);
        }


        $publication = $this->UserModel->get_approved_publication_by_id($id);

        if ($publication) {
            $result = [
                'status' => 200,
                'message' => 'Publication fetched successfully',
                'data' => $publication
            ];
        } else {
            $result = [
                'status' => 404,
                'message' => 'No approved publication found with the given ID',
                'data' => []
            ];
        }

        return $this->response($result, RestController::HTTP_OK);
    }



    public function get_published_papers_by_journal_get($journal_id = null)
    {
        $this->load->model('UserModel');

        if (!$journal_id) {
            $result = [
                'status' => 400,
                'message' => 'Journal ID is required',
                'data' => null
            ];
            return $this->response($result, RestController::HTTP_BAD_REQUEST);
        }


        $published_papers = $this->UserModel->get_published_research_papers($journal_id);

        if (!empty($published_papers)) {
            $result = [
                'status' => 200,
                'message' => 'Published research papers fetched successfully',
                'data' => $published_papers
            ];
        } else {
            $result = [
                'status' => 404,
                'message' => 'No published research papers found for the given journal ID',
                'data' => []
            ];
        }

        return $this->response($result, RestController::HTTP_OK);
    }


    public function razorpay_webhook()
    {
        $this->load->model('AdminModel');
        //process razor pay status
        $finalResult = 'RazorPay-';
        $headers = $this->input->request_headers();
        $sign = isset($headers['X-Razorpay-Signature']) ? $headers['X-Razorpay-Signature'] : false ;
        $key                = 'XXXXXXSECRETKEY';
        $jsonString         = $this->input->raw_input_stream; // raw webhook request body
        $received_signature = $sign;
        $res = json_decode($jsonString, true);
        if (empty($sign)) {
            $finalResult .= 'Signature Missing';
            log_message('debug', $finalResult);
            echo $finalResult;
            exit;
        }
        $expected_signature = hash_hmac('sha256', $jsonString, $key);
        if ($expected_signature == $received_signature) {

            if ($res['event'] == 'order.paid') {
                $payment = $res['payload']['payment']['entity'];
                $order   = $res['payload']['order']['entity'];
                if ($order['status'] == 'paid') {
                    $booking = $this->AdminModel->get_booking_by_payment_id($order['id']);
                    if ($booking['payment_status'] == 0) {
                        $this->AdminModel->mark_booking_paid($order['id'], json_encode($payment));
                        //send mail
                        $booking['payment_status'] = 1;
                        $this->load->library('Mailer_Lib');
                        $this->mailer_lib->send_mail($booking['email'], 'Appointment Scheduled', appointment_template($booking));
                    }
                    $finalResult .=  "success:Order-".$order['id'];
                } else {
                    $finalResult .=  "failed:Order-".$order['id'].'status:'.$order['status'];
                }
            } else {
                $finalResult .= 'Failed: event-'.$res['event'];
            }
        } else {
            $finalResult .= "Signature Mismatch";
        }
        echo $finalResult;
        log_message('debug', $finalResult);
    }



}
