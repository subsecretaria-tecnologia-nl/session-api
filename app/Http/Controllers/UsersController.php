<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\CatalogTokenType;
use App\Models\CatalogUserRoles;
use App\Models\User;
use App\Models\UserHistory;
use App\Models\UserRelationships;
use App\Models\UserToken;
use App\Models\UserTokenSession;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
		$user = User::where('email', $request->input("email"))
		->orWhere('username', 'like', '%' . $request->input("username") . '%')->first();
		if ($user == true) {
			throw new ShowableException(401, "User already exists");
		}


		$validator = Validator::make($request->all(), [
			'email' => 'string|email|max:255',
			'username'=>'string',

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
		$user->function = "Editar usuario";
		$input = $request->all();		

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

	public function editSubUser(Request $request){	

		$user = User::where('email', $request->email)
		->orWhere('username', 'like', '%' .  $request->username . '%')->first();

		if ($user == true) {
			throw new ShowableException(401, "Sorry, User already exists");				
		}		

		$validator = Validator::make($request->all(), [
			'email' => 'string|email|max:255',
			'username'=>'string',

		]);

		$user = User::find($request->id);		
		$user->function = "Editar sub usuario";

		$input = $request->all();	
		$updated = $user->fill($input);


		if ($updated->save()) {
			return [
				'success' => true,
				'status'=> 200
			];
		} else {
			throw new ShowableException(401, "Sorry, subuser could not be updated");
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

	public function getSubUser(Request $request){
		$user = User::where('id', $request->id)->first();
		$permission = $user->permission()->get()->first();

		if($permission->name=="Superadmin"){
			$subusers= User::all();
		}else{
			$relation=$user->subusers()->get()->pluck('user_id')->toArray();
			$subusers = User::whereIn('id', $relation)->get()->toArray();	
		}

		if (count((array)$subusers) > 0) {
			return [ "user" => $subusers ];
		} else {
			throw new ShowableException(401, "Unauthorized");
		}
	}

	public function getSubusers(){
		$user =auth()->user();
		$role = $user->roles->name;
		if($role=="Funcionario"){
			$subusers = User::where('created_by', $user->id)->get()->toArray();
		}else{
			$relation=$user->subusers()->get()->pluck('user_id')->toArray();
			$subusers = User::whereIn('id', $relation)->get()->toArray();			
		}


		if (count((array)$subusers) > 0) {
			return ['status' => 'success', 'user' => $subusers];
		} else {
			return response()->json(['status' => 'fail'], 401);
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

	public function getSessionSubUser($id){
		$user =auth()->user();
		$users=$user->subusers()->get()->pluck('user_id')->toArray();
		$sessions = UserToken::whereIn('user_id', $users)->get();			

		if (!$sessions) {
			throw new ShowableException (404, "Sessions cannot be found.");
		}
		return $sessions;		
	}

	public function signup($request=null){
		if(!$request) $request = request()->all();

		$roles = CatalogUserRoles::where('id', $request->role_id)->first();
		if($roles->name=='Funcionario' || $roles->name=='Notario'){
			throw new ShowableException(403, "$roles->name tiene que ser creado por un usuario con privilegios master");
		}
		$user = User::where('email', $request->email)
		->orWhere('username', $request->username)->first();

		if ($user == true) {
			throw new ShowableException(422, "User already exists with username ($request->username) or email ($request->email).");
		}

		$validator = Validator::make(to_array($request), [
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

		$input = to_array($request);
		$input['password'] = Hash::make($input['password']);
		$user = User::create($input);

		$token = auth()->login($user);
		$seconds = auth()->factory()->getTTL() * 60 * 24 * 30;
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');

		$response = [
			'users' => [
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
		$response["users"] = array_merge($user->toArray(), $response["users"]);
		return $response;
	}

	public function signupSubUser(Request $request){	

		$user = User::where('email', $request->input("email"))
		->orWhere('username', 'like', '%' .  $request->input("username") . '%')->first();

		if ($user) {
			throw new ShowableException(401, "User already exists");
		}
		$id = auth()->user()->id;				

		$permission = $this->permissionCreatedUser($id, $request->input("role_id"), $request->input("assingUser"), $request->input("subuser"));

		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email|max:255',
			'username'=>'string'

		]);
		if($validator->fails()){
			throw new ShowableException(401, $validator->errors());
		}

		$input = $request->all();			
		$input['password'] = Hash::make($input['password']);

		if($permission["permiso"]<>1 || $permission["permiso"]<>3){
			$input["created_by"]=$id;
		}	

		$user = User::create($input);

		if($permission["permiso"]==1 || $permission["permiso"]==2 || $permission["permiso"]==3){
			$relation["super_admin_id"]=$request->assingUser;
			$relation["user_id"]=$user->id;
			$subuser = UserRelationships::create($relation);
		}
		$token = auth()->login($user);
		$seconds = auth()->factory()->getTTL() * 60 * 24 * 30;
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');

		$type = $permission["type"];		
		$response = [
			'status' => 200,
			'message'=>"$type has been created." ,
			'acount'=>[
				'name'=>$user->username,
				'email'=>$user->email,
				'access_token'=>[
					'token'=>$token,
					'valid_until'=>[
						'date'=>$date,
						'seconds' =>$seconds,
					]
				]
			]
		];

		if ($user->save()){
			return $response;
		}else{
			throw new ShowableException(401, "$type could not be created");
		}	
	} 

	public function statusSubUser(Request $request){					
		$user = User::find($request->id);

		if (!$user) {
			throw new ShowableException(401, "Sorry, subuser cannot be found");
		}

		$user->function = "Cambiar status";
		$updated = $user->update([
			"status"=>$request->status			
		]);

		if ($updated) {
			return [
				'success' => true,
				'status'=> 200
			];
		} else {
			throw new ShowableException(401, "Sorry, subuser could not be updated");
		}
		$input = $request->all();
		$input['password'] = Hash::make($input['password']);

		$updated = $user->fill($input)->save();
	}	

	protected function permissionCreatedUser($userId, $roleId, $assingUser="", $subuser){	
		//Verificar que permiso tiene el usuario	
		$user=User::where('id', $userId)->first();
		$permission = $user->permission[0]->name;	

		//Ver cual rol es el que esta asignando
		$roles = CatalogUserRoles::where('id', $roleId)->first();

		//si el usuario tiene permisos subsuarios solo podra crear cuentas de subsuario para el
		if($permission=="Subusers"){
		//Validación de Funcionario y Notario solo pueden ser creados por usuarios con permisos master
			if($roles->name=='Funcionario' || $roles->name=='Notario'){
				throw new ShowableException(401, "$roles->name tiene que ser creado por un usuario con privilegios master");
			}
		//valida que no asigne usuarios
			else if($assingUser){
				throw new ShowableException(401, "Solo tienes permiso para agregar subusuarios a tu cuenta");
			}else{
				return [
					"permiso"=>1,
					"type"=>"Subuser"
				];
			}


		//si el usuario tiene permisos master para crear usuarios, subusuarios para su cuenta, o asignar subusuarios
		}else if($permission=="Superadmin"){
		//verifica si creara subusuarios
			if($subuser){
		//verifica si el usuario creado se asignara a otra cuenta
				if($assingUser){
					return [
						"permiso"=>2,
						"type"=>"Subuser"
					];
				}else{
					return [				
						"permiso"=>3,
						"type"=>"Subuser"
					];
				}
		//en el caso que solo quiera crear otra cuenta
			}else{
				return [
					"permiso"=>4,
					"type"=>"User"
				];
			}
		}
	}
}