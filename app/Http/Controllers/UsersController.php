<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;

class UsersController extends Controller
{


    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
						return response([
							'message' => 'Validation errors', 
							'errors' =>  $validator->errors(), 
							'status' => false]);
        }

        $input = $request->all();
				$input['password'] = Hash::make($input['password']);
				$input['api_token'] = Str::random(150);
				$user = User::create($input);
				
				$token = $user->api_token;

        return response([
					'token' => $token, 
					'message' => 'Cuenta creada', 
					'status' => 200
					]);
		} 

	
     
}