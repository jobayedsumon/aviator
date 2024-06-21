<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AamarpayController extends Controller
{
    private $baseUrl;
    private $storeId;
    private $signatureKey;

    public function __construct()
    {
        $this->baseUrl = env('AAMARPAY_BASE_URL');
        $this->storeId = env('AAMARPAY_STORE_ID');
        $this->signatureKey = env('AAMARPAY_SIGNATURE_KEY');
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $paymentAmount = $request->amount;
        $payerInformation = User::find(user('id'));
        $paymentMethod = 'aamarpay';
        $transactionId = $paymentMethod . date("dmyhis");

        $trn = new Transaction;
        $trn->userid = $payerInformation->id;
        $trn->platform = $paymentMethod;
        $trn->transactionno = $transactionId;
        $trn->type = 'credit';
        $trn->amount = $paymentAmount;
        $trn->category = 'recharge';
        $trn->remark = 'Processing';
        $trn->status = '0';

        if ($trn->save()) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseUrl . '/jsonpost.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "store_id": "'.$this->storeId.'",
                "tran_id": "'.$transactionId.'",
                "success_url": "'.route('aamarpay.success').'",
                "fail_url": "'.route('aamarpay.fail').'",
                "cancel_url": "'.route('aamarpay.cancel').'",
                "amount": "'.$paymentAmount.'",
                "currency": "BDT",
                "signature_key": "'.$this->signatureKey.'",
                "desc": "Aviator Deposit Recharge",
                "cus_name": "'.$payerInformation->name.'",
                "cus_email": "'.$payerInformation->email.'",
                "cus_add1": "Dhaka",
                "cus_add2": "Dhaka",
                "cus_city": "Dhaka",
                "cus_state": "Dhaka",
                "cus_postcode": "1200",
                "cus_country": "Bangladesh",
                "cus_phone": "'.$payerInformation->mobile.'",
                "opt_a": "'.$payerInformation->id.'",
                "type": "json"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $responseObj = json_decode($response);

            if(isset($responseObj->payment_url) && !empty($responseObj->payment_url)) {
                $paymentUrl = $responseObj->payment_url;
                return redirect()->away($paymentUrl);
            } else {
                echo $response;
            }

        } else {
            return redirect()->back()->with('error', 'Transaction failed!');
        }
    }

    public function success(Request $request)
    {
        $requestId = $request->mer_txnid;
        $amount = $request->amount;
        $userId = $request->opt_a;

        $url = "$this->baseUrl/api/v1/trxcheck/request.php?request_id=$requestId&store_id=$this->storeId&signature_key=$this->signatureKey&type=json";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $responseObj = json_decode($response);
        if (isset($responseObj->status_code) && $responseObj->status_code === '2' && isset($responseObj->pay_status) && $responseObj->pay_status === 'Successful') {
            try
            {
                DB::beginTransaction();
                $this->approveRecharge($userId, $amount, $requestId);
                DB::commit();
                return redirect('/deposit_withdrawals');
            }
            catch (Exception $exception)
            {
                Log::error($exception->getMessage());
                DB::rollBack();
                return redirect('/deposit')->withErrors(['error' => 'Transaction failed!']);
            }
        }
    }

    public function fail(Request $request)
    {
        return redirect('/deposit')->withErrors(['error' => 'Transaction failed!']);
    }

    public function cancel(Request $request)
    {
        return redirect('/deposit')->withErrors(['error' => 'Transaction cancelled!']);
    }

    private function approveRecharge($userId, $amount, $transactionId)
    {
        $firstrecharge = Transaction::where('userid', $userId)->where('category', 'recharge')->where('status','0')->get();
        if (count($firstrecharge) == 0) {
            $level1 = User::where('id', user('promocode', $userId))->first();
            if ($level1) {
                $level1amount = ($amount / 100 ) * setting('level1commission');
                // return $level1amount;
                addwallet($level1->id, $level1amount);
                addtransaction($level1->id, 'Level', date("ydmhsi"), 'credit', $level1amount, 'Level_bonus', 'Success', '1');

                $level2 = User::where('id', $level1->promocode)->first();
                if ($level2) {
                    $level2amount = ($amount / setting('level2commission')) * 100;
                    addwallet($level2->id, $level2amount);
                    addtransaction($level2->id, 'Level', date("ydmhsi"), 'credit', $level2amount, 'Level_bonus', 'Success', '1');

                    $level3 = User::where('id', $level2->promocode)->first();
                    if ($level3) {
                        $level3amount = ($amount / setting('level3commission')) * 100;
                        addwallet($level3->id, $level3amount);
                        addtransaction($level3->id, 'Level', date("ydmhsi"), 'credit', $level3amount, 'Level_bonus', 'Success', '1');
                    }
                }
            }
        }

        Transaction::where('transactionno', $transactionId)->update([
            "remark" => 'Success',
            "status" => '1',
        ]);

        addwallet($userId, $amount);
    }
}
