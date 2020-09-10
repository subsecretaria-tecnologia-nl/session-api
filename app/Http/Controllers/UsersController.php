<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\CatalogUserAction;
use App\Models\CatalogUserRoles;
use App\Models\User;
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

	public function editUser(Request $request){
		$user = User::where('email', $request->email)
    ->orWhere('username', 'like', '%' .  $request->username . '%')->first();
		if ($user == true) {
			throw new ShowableException(401, "User already exists");
		}


		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'username'=>'string',
			'password' => [
				'required',
				'string',
				'min:8',
				'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
			]
		]);

		if ($validator->fails()) {
			throw new ShowableException(401, $validator->errors());
		}

		$id = auth()->user()->id;

		$user = User::find($id);

		if (!$user) {
			return [
				'success' => false,
				'message' => 'Sorry, user cannot be found',
				'status'=> 401
			];
		}
		$input = $request->all();
		$input['password'] = Hash::make($input['password']);
		

		$updated = $user->fill($input)->save();

		if ($updated) {
			return [
				'success' => true,
				'status'=> 200
			];
		} else {
			return [
				'success' => false,
				'message' => 'Sorry, user could not be updated'
			];
		}
	}
	public function getUser(Request $request){
		$user = JWTAuth::user();
		if (count((array)$user) > 0) {
			return [ "user" => $user ];
		} else {
			throw new ShowableException(401, "Unauthorized");
		}
	}
	public function getSessionUser(){
		$user = JWTAuth::user();
		$sessions = $user->tokens()->get()->toArray();

		if (!$sessions) {
			throw new ShowableException (404, "User cannot be found.");
		}
		return $sessions;
	}
	public function signup(Request $request){		
		$roles = CatalogUserRoles::where('id', $request->role_id)->first();
		if($roles->name=='Funcionario' || $roles->name=='Notario'){
			throw new ShowableException(401, "$roles->name tiene que ser creado por un usuario con privilegios master");
		}
		$user = User::where('username', '=', $request->username)->where('email', '<>', $request->email)->first();
		if ($user == true) {
			throw new ShowableException(401, "User already exists");
		}

		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'username'=>'string',
			'password' => [
				'required',
				'string',
				'min:8',
				'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
			]
		]);

		if($validator->fails()){
			throw new ShowableException(401, $validator->errors());
		}

		$auth = auth()->user();
		if($auth){
			$user_id =$auth->id;
		}else{
			$user_id ="";
		}

		$input = $request->all();
		$input['status']=1;
		$user = User::create($input);

		$token = auth()->login($user);
		$seconds = auth()->factory()->getTTL() * 60 * 24 * 30;
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');

		return [
			'status' => 200,
			'acount'=>[
				'name'=>$user->username,
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
		];
	} 	
}