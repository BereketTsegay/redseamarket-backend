<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{
    
    private $stripe;
    public function __construct()
    {
        $this->stripe =  Stripe::setApiKey(env('STRIPE_SECRET_KEY'));;
    }

  

    public function postPaymentStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'cardNumber' => 'required',
            'month' => 'required',
            'year' => 'required',
            'cvv' => 'required'
        ]);

        $amount=$request->amount;
        $user_id = Auth::user()->id;
        
        if ($validator->fails()) {
            return response()->json(["status" => 0,"message"=>$validator->errors()->first()]);

        }

        $token = $this->createToken($request);
        if (!empty($token['error'])) {
            return response()->json(["status" => 0,"message"=>$token['error']]);

        }
        if (empty($token['id'])) {
            return response()->json(["status" => 0,"message"=>"Payment failed"]);

        }

        $charge = $this->createCharge($token['id'], $amount*100);
        if (!empty($charge) && $charge['status'] == 'succeeded') {

            $payment                = new Payment();
            $payment->payment_id    = $charge['id'];
            $payment->amount        = $request->amount;
            $payment->ads_id        = 0;
            $payment->name          = $request->name;
            $payment->email         = $request->email;
            $payment->phone         = $request->phone;
            $payment->payment_type  = 0; // 0 for Payment through stripe
            $payment->status        = 'Payment started';
            $payment->save();

            $details = [
                'name'      => $request->name,
                'amount'    => $request->amount,
                'id'        => $charge['id'],
                'date'      => $payment->created_at,
            ];
    
            Mail::to($request->email)->send(new MailPayment($details));
            
            return response()->json(["status" => 1,"message"=>"succeeded","payment_id"=>$charge['id']]);

        } else {
            return response()->json(["status" => 0,"message"=>"Payment failed"]);

        }
        return response()->json(["status" => 0,"message"=>""]);
    }

    private function createToken($cardData)
    {
        $token = null;
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'number' => $cardData['cardNumber'],
                    'exp_month' => $cardData['month'],
                    'exp_year' => $cardData['year'],
                    'cvc' => $cardData['cvv']
                ]
            ]);
        } catch (CardException $e) {
            $token['error'] = $e->getError()->message;
        } catch (Exception $e) {
            $token['error'] = $e->getMessage();
        }
        return $token;
    }

    private function createCharge($tokenId, $amount)
    {
        $charge = null;
        try {
            $charge = $this->stripe->charges->create([
                'amount' => $amount*100,
                'currency' => 'usd',
                'source' => $tokenId,
                'description' => 'My first payment'
            ]);

           

        } catch (Exception $e) {
            $charge['error'] = $e->getMessage();
        }
        return $charge;
    }
}
