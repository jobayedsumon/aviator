<?php

namespace App\Http\Controllers;

use App\Models\Bankdetail;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;
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

    public function adminModerator()
    {
        $adminModerators = User::where('isadmin', 1)->orWhere('isModerator', 1)->orderBy('id', 'desc')->get();
        return view("admin.admin-moderator", compact("adminModerators"));
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

    public function adminModeratorEdit($id)
    {
        $adminModerator = User::where('id', $id)->first();
        return view("admin.admin-moderator-edit", compact("adminModerator"));
    }

    public function adminModeratorCreate()
    {
        return view("admin.admin-moderator-create");
    }

    public function adminModeratorStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->status = '1';

        if ($request->role === 'admin') {
            $user->isadmin = '1';
            $user->isModerator = false;
        } else {
            $user->isModerator = true;
            $user->isadmin = null;
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect('/admin/admin-moderator')->with('success', 'User created successfully');
    }

    public function adminModeratorUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('id', $id)->first();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;

        if ($request->role === 'admin') {
            $user->isadmin = '1';
            $user->isModerator = false;
        } else {
            $user->isModerator = true;
            $user->isadmin = null;
        }

        $user->save();

        return redirect('/admin/admin-moderator')->with('success', 'User updated successfully');
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
