<?php

defined('BASEPATH') or exit('No direct script access allowed');
// require_once APPPATH . 'libraries/src/Api.php';


/**
 * Custom Payment Gateway Library
 * ----------------------------------------------------------
 *
 * @author: Shivam Gautam
 * @version: 0.0.1
 */
use Razorpay\Api\Api;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

class Payment_Lib
{
    public const RAZORPAY = 'razorpay';
    public const PAYPAL = 'paypal';
    private $ids        = [
        self::RAZORPAY => 'rzp_test_lo1DpXawQRBUZe',
        self::PAYPAL  => 'XXXXXXX'
    ];
    private $secrets    = [
        self::RAZORPAY => 'WSXitLb2ccR2NeliX2z8TtuE',
        self::PAYPAL  => 'YYYYYYYYY'
    ];
    private $gateways = [];
    private $active = null;
    public function __construct()
    {
        $this->gateways[self::RAZORPAY] = new Api($this->ids[self::RAZORPAY], $this->secrets[self::RAZORPAY]);
        $this->gateways[self::PAYPAL] = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->ids[self::PAYPAL],
                $this->secrets[self::PAYPAL]
            )
        );
    }

    public function activate($type)
    {
        $this->active = $type;
    }

    public function create_payment(PaymentParams $params)
    {

        if ($this->active == self::RAZORPAY) {
            try {
                $ord = array('receipt' => $params->orderId, 'amount' => $params->amount, 'currency' => 'INR');
                $res = $this->gateways[self::RAZORPAY]->order->create($ord);
                return ['status' => true,'data' => $res];
            } catch (Exception $e) {
                return ['status' => false,'message' => $e->getMessage()];
            }
        } 
        elseif ($this->active == self::PAYPAL) {

            $amountValue = $params->amount;
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $amount = new Amount();
            $amount->setTotal($amountValue);
            $amount->setCurrency('USD');

            $transaction = new Transaction();
            $transaction->setAmount($amount);
            $transaction->setDescription('Maatri Upchaar');

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(base_url('paypal-success'))
                         ->setCancelUrl(base_url('paypal-cancel'));

            $payment = new Payment();
            $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setTransactions([$transaction])
                    ->setRedirectUrls($redirectUrls);

            try {
                $payment->create($this->gateways[self::PAYPAL]);
                $approvalUrl = $payment->getApprovalLink();
                $paymentID = $payment->getId();
                return ['status' => true,'data' => ['redirectURL' => $approvalUrl,'paymentId' => $paymentID]];
            } catch (Exception $ex) {
                return ['status' => false,'message' => $ex->getMessage()];
            }
        }
    }
    public function paypal_success($paymentId, $payerId)
    {

        $payment = Payment::get($paymentId, $this->gateways[self::PAYPAL]);
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);
        try {
            $result = $payment->execute($execution, $this->gateways[self::PAYPAL]);
            return ['status' => true,'data' => $result];
        } catch (Exception $ex) {
            return ['status' => false,'message' => $ex->getMessage()];
        }
    }
}

class PaymentParams
{
    public $amount;
    public $orderId;
    public function __construct(int $amount, string $orderId)
    {
        $this->amount   = $amount;
        $this->orderId  = $orderId;
    }
}
