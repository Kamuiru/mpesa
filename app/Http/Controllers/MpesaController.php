<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MpesaController;

class MpesaController extends Controller
{
    public function generateAccessToken()
    {
       
        $consumerKey =  "VsMzPH1GQIohTBEOTIfg0TSujF46qam7";
        $consumerSecret = "RIUXYaBRr4IUDUGR";
        
        $headers = ['Content-Type:application/json; charset=utf8'];
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);

        $access_token = $result->access_token;
        Log::info($access_token);
        
        return $access_token;
        
    }

    public function myMpesaPassword()
    {
        $timestamp = Carbon::rawParse('now')->format('YmdHms');

        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = 174379;
        
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);

        return $lipa_na_mpesa_password;
    }

    public function mpesaSTKPush()
    {
        $curl_post_data = [
            'BusinessShortCode' => 174379,
            'Password' => $this->myMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => 254795822141,
            'PartyB' => 174379,
            'PhoneNumber' => 254795822141,
            'CallBackURL' => 'https://dry-basin-83547.herokuapp.com/receive',
            'AccountReference' => "E-SHOP",
            'TransactionDesc' => "Testing stk push on sandbox"
        ];
        
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessToken()));

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

       

        return $curl_response;
        
    }
    public function index(){
        $payments = Payment::get();
        return view('dashboard')->with('payments', $payments);
    }
    // public function resData(Request $request){
    //     $response = json_decode($request->getContent());
    //     Log::info(json_encode($response));
    //     dd($response);
    //     $amount = $response['Body']['stkCallback']['CallbackMetadata']['Item[0]']['Value'];
    //     $transaction_id = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
    //     $date = $response->Body->stkCallback->CallbackMetadata->Item[2]->Value;
    //     $phone = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;

    //     // $payment = new Payment;
    //     // $payment->transaction_id = $transaction_id;
    //     // $payment->transaction_date = $date;
    //     // $payment->phone_number = $phone;
    //     // $payment->amount = $amount;
    //     // $payment->save();
    //     Payment::create([
    //         "transaction_id" => $transaction_id,
    //         "transaction_date" => $date,
    //         "phone_number" => $phone,
    //         "amount" => $amount
        

    //     ]);
        
       
    // }
    // public function index(){
    //     $payments = Payment::get();
    //     return view('dashboard')->with('payments', $payments);
    // }
}
