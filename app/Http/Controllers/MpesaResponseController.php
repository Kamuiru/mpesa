<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaResponseController extends Controller
{
    public function mpesaSTKPush(Request $request){
        $response = json_decode($request->getContent());
        Log::info(json_encode($response));
        dd($response);
        $amount = $response['Body']['stkCallback']['CallbackMetadata']['Item[0]']['Value'];
        $transaction_id = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
        $date = $response->Body->stkCallback->CallbackMetadata->Item[2]->Value;
        $phone = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;

        // $payment = new Payment;
        // $payment->transaction_id = $transaction_id;
        // $payment->transaction_date = $date;
        // $payment->phone_number = $phone;
        // $payment->amount = $amount;
        // $payment->save();
        Payment::create([
            "transaction_id" => $transaction_id,
            "transaction_date" => $date,
            "phone_number" => $phone,
            "amount" => $amount
        

        ]);
        
       
    }
    public function index(){
        $payments = Payment::get();
        return view('dashboard')->with('payments', $payments);
    }
}