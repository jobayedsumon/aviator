<?php

namespace App\Http\Controllers;

use App\Models\Bankdetail;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Admin extends Controller
{
    public function login()
    {
        return view("admin.login");
    }

    public function dashboard()
    {
        $user       = User::all();
        $recharge   = Transaction::where('category', 'recharge')->get();
        $withdrawal = Transaction::where('category', 'withdraw')->get();
        return view("admin.dashboard", [
            "user"       => $user,
            "recharge"   => $recharge,
            "withdrawal" => $withdrawal,
        ]);
    }

    public function userlist()
    {
        $userlist = User::where('isadmin', null)->orderBy('id', 'desc')->get();
        return view("admin.userlist", compact("userlist"));
    }

    public function useredit($id)
    {
        $user = User::where('isadmin', null)->where('id', $id)->first();
        return view("admin.useredit", compact("user"));
    }

    public function chagepassword()
    {
        return view('admin.changepassword');
    }

    public function rechargehistory()
    {
        $history = Transaction::where('category', 'recharge')->where('type', 'credit')->orderBy('id', 'desc')->get();
        $title   = 'Recharge Hitory';
        return view('admin.rechargehistory', [
            'history' => $history,
            'title'   => $title,
        ]);
    }

    public function createNewDeposit()
    {
        $users = User::where('isadmin', null)->where('status', 1)->orderBy('name')->get();
        return view('admin.deposit', compact('users'));
    }

    public function storeNewDeposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userid = $request->userId;
        $amount = $request->amount;

        newDeposit($userid, $amount, date("ydmhsi"));

        return redirect(route('rechargehistory'))->with('success', 'Deposit added successfully');
    }

    public function withdrawalhistory()
    {
        $history = Transaction::where('category', 'withdraw')->where('type', 'debit')
            //            ->join('bank_details', 'transactions.userid', '=', 'bank_details.userid')
            //            ->select('transactions.*','bank_details.accountno','bank_details.ifsccode','bank_details.branchname','bank_details.upi_id','bank_details.mobile_no')
            ->orderBy('transactions.id', 'desc')->get();
        $title   = 'Withdrawal Hitory';
        return view('admin.withdrawhistory', [
            'history' => $history,
            'title'   => $title,
        ]);
    }

    public function amountsetup($id = null)
    {
        $specificdata = null;
        $settings     = Setting::get();
        $title        = 'Withdrawal Hitory';
        if ($id != null)
        {
            $specificdata = Setting::where('id', $id)->first();
        }
        return view('admin.amountsetup', [
            'setting'      => $settings,
            'id'           => $id,
            'specificdata' => $specificdata,
        ]);
    }

    public function bankdetail()
    {
        $specificdata = null;
        $title        = 'Bank Detail';
        $specificdata = Bankdetail::where('id', '1')->first();
        return view('admin.bankdetail', [
            'bank' => $specificdata,
        ]);
    }

    public function logout()
    {
        if (session()->has('adminlogin'))
        {
            session()->forget('adminlogin');
        }
        return redirect('/admin');
    }
}
