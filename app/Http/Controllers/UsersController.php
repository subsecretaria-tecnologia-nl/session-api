<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class UsersController extends Controller
{
	protected $user;

	public function __construct(){
		$this->middleware('auth:api', ['except' => ['login','logout','signup']]);
	}
	
	public function signup(Request $request){
		$user = User::where('name', '=', $request->name)->where('email', '<>', $request->email)->first();
		if ($user == true) {
			return response()->json(['error'=>'User already exists', 'status'=>401]);
		}

		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'name'=>'string',
			'password' => [
				'required',
				'string',
				'min:8',
				'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
			]
		]);

		if($validator->fails()){
			return response([
				'message' => 'Validation errors', 
				'errors' =>  $validator->errors(), 
				'status' => false]);
		}

		$input = $request->all();
		$input['password'] = Hash::make($input['password']);
		$input['rol_id'] = 2;
		$user = User::create($input);

		$token = auth()->login($user);
		$seconds = auth()->factory()->getTTL() * 60 * 24 * 30;
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');

		return response()->json([
			'status' => 200,
			'acount'=>[
				'name'=>$user->name,
				'email'=>$user->email,
				'access_token'=>[
					'token'=>$token,
					'type'=>'bearer',
					'valid_until'=>[
						'date'=>$date,
						'seconds' =>$seconds,
					]
				]
			]
		]);
	} 

	public function getUser(Request $request){
		$user = JWTAuth::user();
		if (count((array)$user) > 0) {
			return response()->json(['status' => 'success', 'user' => $user]);
		} else {
			return response()->json(['status' => 'fail'], 401);
		}
	}

	public function editUser(Request $request){
		$user = User::where('name', '=', $request->name)->where('email', '<>', $request->email)->first();
		if ($user == true) {
			return response()->json(['error'=>'User already exists', 'status'=>401]);
		}

		$request->merge([
			'password' => $password,
		]);

		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'name'=>'string',
			'password' => [
				'required',
				'string',
				'min:8',
				'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
			]
		]);

		if ($validator->fails()) {
			return response()->json(['error'=>$validator->errors()], 401);
		}

		$id = auth()->user()->id;

		$user = User::find($id);

		if (!$user) {
			return response()->json([
				'success' => false,
				'message' => 'Sorry, user cannot be found'
			], 400);
		}
		$input = $request->all();
		$input['password'] = Hash::make($input['password']);
		

		$updated = $user->fill($input)->save();

		if ($updated) {
			return response()->json([
				'success' => true,
				'status'=> 200
			]);
		} else {
			return response()->json([
				'success' => false,
				'message' => 'Sorry, user could not be updated'
			], 500);
		}
	}

	public function getSessionUser(){
		$id = auth()->user()->id;

		$sessions = User::where('id', $id)->with(['sessions' => function ($q) {
			$q->sessionsact();
		}])->get()->toArray();
		$sessionsActived = $sessions[0]["sessions"];

		if (!$sessionsActived) {
			return response()->json([
				'success' => false,
				'message' => 'Sorry, user  cannot be found'
			], 400);
		}

		return $sessionsActived;
	}
}