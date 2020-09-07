<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\User;
use App\Models\Session;
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
		$this->middleware('auth:api', ['except' => ['login','logout']]);	
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
				// 'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
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

	public function refresh(){
		return $this->respondWithToken(auth()->refresh());
	}

	public function logout(){
		$user_id = auth()->user()->id;
		$browser = $this->browserType();
		$device = $this->deviceType();

		$session = Session::select(['*'])	
		->where('user_id', $user_id)
		->where('device_type', $device)
		->where('browser_type', $browser)->first();



		$start = Carbon::parse($session->login_datetime);
		$end = Carbon::now()->format('Y-m-d H:i:s');
		$session->logout_datetime = $end;	
		$minutesDiff=$start->diffForHumans($end);


		$session->session_lifetime = $minutesDiff;
		$session->save();

		auth()->logout();
		return response()->json(['message' => 'Successfully logged out']);
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

	protected function browserType(){
		return $browser = Browser::browserFamily();
	}
	
	protected function onAuthorized($token){
		$browser = $this->browserType();
		$device = $this->deviceType();
		$session = new Session;
		$session->user_id = auth()->user()->id;
		$session->device_type = $device;
		$session->browser_type = $browser;
		$session->login_datetime = Carbon::now()->format('Y-m-d H:i:s');		
		$session->save();


		return [
			'token' => $token,
			'token_type'=> 'Bearer',
			'expires_in' =>auth()->factory()->getTTL() * 60 * 24 * 30,
			'user'=> auth()->user()->name,
			'status'=> 200,
		];
	}

	protected function onUnauthorized(){
		throw new ShowableException(401, "Unauthorized.", "Invalid Credentials");
		// return new JsonResponse([
		// 	'message' => 'invalid_credentials'
		// ], Response::HTTP_UNAUTHORIZED);
	}

	protected function onJwtGenerationError(){
		return new JsonResponse([
			'message' => 'could not create token',
			'status'=> 403,
		]);
	}

	protected function respondWithToken($token){
		return response()->json([
			'access_token' => $token,
			'token_type' => 'bearer',
			'expires_in' => auth()->factory()->getTTL() * 60,
			'status'=> 200,
		]);
	}
} 

