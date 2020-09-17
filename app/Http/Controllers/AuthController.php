<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\CatalogTokenType;
use App\Models\CatalogUserAction;
use App\Models\CatalogUserRoles;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserRelationships;
use App\Models\UserToken;
use App\Models\UserTokenSession;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthController extends Controller
{

	public function __construct(){
		$this->middleware('auth:api', ['except' => ['login','logout', 'refresh']]);	
	}
	protected function browserType(){
		return $browser = Browser::browserFamily();
	}

	protected function deviceType(){
		if(Browser::isDesktop()){
			return "Desktop";
		}elseif(Browser::isMobile()){
			return "Mobile";
		}elseif(Browser::isTablet()){
			return "Tablet";
		}else{
			return "Unknown";
		}
	}
	protected function onAuthorized($token){		
		$browser = $this->browserType();
		$device = $this->deviceType();
		$tokenType= CatalogTokenType::where('name', 'Session')->select('id', 'name')->first();

		$seconds = auth()->factory()->getTTL() * 60;		
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');
		$user=  auth()->user()->id;

		$userToken =UserToken::where('user_id', $user)
		->where('token_type_id', $tokenType->id)
		->first();
		$sum=1;	

		if($userToken){
			$userTokenSession = UserTokenSession::where('token_id', $userToken->id)->first();

			$sum += $userTokenSession->quantity;
			$userTokenSession->update([
				'quantity' => $sum
			]);
		}else{
			$userToken = new UserToken();
			$userToken->user_id = auth()->user()->id;
			$userToken->token = $token;
			$userToken->token_type_id = $tokenType->id;
			$userToken->created_by = auth()->user()->id;
			$userToken->valid_until = $date;
			$userToken->save();
			$quantity =0;
			$sum += $quantity;
			UserTokenSession::create(['token_id' =>$userToken->id, 'quantity' => $sum]);
		}

		return [
			'token' => $token,
			'token_type'=> $tokenType->name,
			'expires_in' =>$date,
			'seconds'=>$seconds,
			'user'=> auth()->user()->name,
			'status'=> 200,
		];
	}
	protected function onJwtGenerationError(){
		return [
			'message' => 'could not create token',
			'status'=> 403,
		];
	}
	protected function onUnauthorized(){
		throw new ShowableException(401, "Unauthorized.", "Invalid Credentials");
	
	}
	protected function respondWithToken($token){
		$tokenType= CatalogTokenType::where('name', 'Refresh')->select('id', 'name')->first();
		$myTTL = 43800;
		JWTAuth::factory()->setTTL($myTTL);
		$seconds = auth()->factory()->getTTL() * 24;		
		$date = Carbon::now()->addSeconds($seconds)->format('Y-m-d H:i:s');

		return [
			"status" => 200,
			"access_token"=>[
				"token" => $token,
				"type" => $tokenType,
				"valid_until"=>[
					"date" => $date,
					"seconds" => $seconds
				]       
			]
		];
	}

	public function login(Request $request){
		if(!$request->header('Authorization'))
			throw new ShowableException(401, "Unauthorized", "Basic Authorization Headers required.");

		$data = explode(' ', $request->header('Authorization'))[1];
		$decoded = base64_decode($data);
		$list = list($username, $password) = explode(":",$decoded);

		$myTTL = 43200;
		JWTAuth::factory()->setTTL($myTTL);

		$r = new Request([
			"email" => $username,
			"password" => $password
		]);

		$validator = Validator::make($r->all("email", "password"), [
			'email' => 'required|string|email|max:255',
			'password' => [
				'required',
				'string',
				'min:8',
				'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
			]
		]);

		if ($validator->fails()) {
			throw new ShowableException(401, "Unauthorized.", $validator->errors());
		}

		try {
			if (!$token = JWTAuth::attempt(array("email" => $username, "password" => $password))) {
				return $this->onUnauthorized();
			}
		} catch (JWTException $e) {
			return $this->onJwtGenerationError();
		}

		return $this->onAuthorized($token);
	}
	public function logout(){
		$user_id = auth()->user()->id;
		$browser = $this->browserType();
		$device = $this->deviceType();

		$userToken = UserToken::where('user_id', $user_id)
		->first();

		$end = Carbon::now()->format('Y-m-d H:i:s');
		$userToken->closed_at = $end;	

		$userToken->save();

		$userTokenSession = UserTokenSession::where('token_id', $userToken->id)->first();		
		$res=$userTokenSession->quantity;
		$res--;
		$userTokenSession->quantity = $res;
		$userTokenSession->save();

		auth()->logout();
		return response()->json(['message' => 'Successfully logged out']);
	}

	public function refresh(){
		return $this->respondWithToken(auth()->refresh());
	}

		
} 

