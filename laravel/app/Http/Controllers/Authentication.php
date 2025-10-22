<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Hash;
use Illuminate\Http\Request;

class Authentication extends Controller
{
    public function login(Request $r)
    {
        $validated = $r->validate([
            'username' => 'required',
            'body' => 'password',
        ]);
        $data = "";
        $isSuccess = false;
        $message = "";
        $usernameexist = User::where('mobile', $r->username)->first();
        if ($usernameexist) {
            if (Hash::check($r->password, $usernameexist->password)) {
                $r->session()->put('userlogin', $usernameexist);
                $message = "Login Successfull";
                $isSuccess = true;
            } else {
                $message = "Incorrect Password!";
            }
        } else {
            $message = "Username not found!";
        }
        $res = array("data" => $data, "isSuccess" => $isSuccess, "message" => $message);
        return response()->json($res);
    }

    public function register(Request $r)
    {
        $validated = $r->validate([
            'name' => 'required',
            'password' => 'required'
        ]);
        $data = "";
        $isSuccess = false;
        $message = "Something wen't wrong!";
        $promocode = '';
        if ($r->promocode != '') {
            $existpromocode = User::where('id', $r->promocode)->first();
            if ($existpromocode) {
                $olddata = User::where('mobile', $r->mobile)->get();
                if (count($olddata) > 0) {
                    $message = "Dublicate Mobile No., Please enter Unique Mobile No.";
                } else {
                    $wallet = new Wallet;
                    $user = new User;
                    $user->name = $r->name;
					$user->image = "/images/avtar/av-".rand(1,72).".png";
                    $user->mobile = $r->mobile;
                    $user->password = Hash::make($r->password);
                    $user->currency = $r->currency;
                    $user->gender = $r->gender;
                    $user->country = $r->country;
                    $user->status = '1';
                    $user->promocode = $r->promocode;
                    if ($user->save()) {
                        $afterregisterdata = User::where('mobile', $r->mobile)->orderBy('id', 'desc')->first();
                        if ($afterregisterdata) {
                            $wallet->userid = $afterregisterdata->id;
                            $wallet->amount = setting('initial_bonus');
                            if ($wallet->save()) {
                                $data = array("username" => $afterregisterdata->mobile, "password" => $r->password, "token" => csrf_token());
                                $isSuccess = true;
                                $message = "Registration Successfull";
                            }
                        }
                    }
                }
            }else{
                $data = array();
                $message = "Invalid Promocode";
            }
        } else {
            $olddata = User::where('mobile', $r->mobile)->get();
            if (count($olddata) > 0) {
                $message = "Dublicate Mobile No., Please enter Unique Mobile No.";
            } else {
                $wallet = new Wallet;
                $user = new User;
                $user->name = $r->name;
                $user->mobile = $r->mobile;
                $user->password = Hash::make($r->password);
                $user->currency = $r->currency;
                $user->gender = $r->gender;
                $user->country = $r->country;
                $user->status = '1';
                $user->promocode = $r->promocode;
                if ($user->save()) {
                    $afterregisterdata = User::where('mobile', $r->mobile)->orderBy('id', 'desc')->first();
                    if ($afterregisterdata) {
                        $wallet->userid = $afterregisterdata->id;
                        $wallet->amount = setting('initial_bonus');
                        if ($wallet->save()) {
                            $data = array("username" => $afterregisterdata->mobile, "password" => $r->password, "token" => csrf_token());
                            $isSuccess = true;
                            $message = "Registration Successfull";
                        }
                    }
                }
            }
        }
        $res = array("data" => $data, "isSuccess" => $isSuccess, "message" => $message);
        return response()->json($res);
    }

    public function adminlogin(Request $r)
    {
        $validated = $r->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $response = array('status' => 0, 'title' => "Oops!!", 'message' => "Invalid Credential!");

      $usernameexist = User::where('mobile', $r->username)
          ->where(function ($query) {
            $query->where('isadmin', '1')
                ->orWhere('isModerator', true);
        })->first();

        if ($usernameexist) {
            if (Hash::check($r->password, $usernameexist->password)) {
                $r->session()->put('adminlogin', $usernameexist);
                $response = array('status' => 1, 'title' => "Success!!", 'message' => "Login Successfully!");
            } else {
                $response = array('status' => 0, 'title' => "Oops!!", 'message' => "Incorrect Password!");
            }
        } else {
            $response = array('status' => 0, 'title' => "Oops!!", 'message' => "Username not exists!");
        }

        return response()->json($response);
    }
}
