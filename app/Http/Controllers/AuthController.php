<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\User;
use App\Session;
use Validator;
use hisorange\BrowserDetect\Parser as Browser;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

		public function __construct()
		{
				// $this->middleware('auth');
				$this->middleware('jwt', ['except' => ['login', 'logout']]);
				
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
	
		protected function onAuthorized($token)
    {
				$browser = $this->browserType();
				$device = $this->deviceType();
				$session = new Session;
				$session->user_id = auth()->user()->id;
				$session->device_type = $device;
				$session->browser_type = $browser;
				$session->login_datetime = Carbon::now()->format('Y-m-d H:i:s');		
				$session->save();


        return new JsonResponse([
				'token' => $token,
				'token_type'=> 'Bearer',
				'expires_in' =>auth()->factory()->getTTL() * 60 * 24 * 30,
				'user'=> auth()->user()->name
        ]);
		}
		protected function onUnauthorized()
    {
        return new JsonResponse([
            'message' => 'invalid_credentials'
        ], Response::HTTP_UNAUTHORIZED);
    }

		protected function onJwtGenerationError()
    {
        return new JsonResponse([
						'message' => 'could not create token',
						'status'=> 403,
        ]);
		}
		public function period($start, $end){
			$period = CarbonPeriod::create($start, $end);

			foreach ($period as $date) {
				echo $date->format('Y-m-d');
						
			}
			return $period->toArray();

		}

		public function logout()
    {

			$user_id = auth()->user()->id;
			$session = Session::find($user_id);

			$start = $session->login_datetime;
			$end = Carbon::now()->format('Y-m-d H:i:s');
			$session->logout_datetime = $end;	
			$minutesDiff=$start->diffInMinutes($end);

			$session->session_lifetime = $minutesDiff;
			$session->save();

			// $token =  $request->header('Authorization');
			// $this->jwt->parseToken()->invalidate();
			// 		return response()->json(['message' => 'Successfully logged out']);
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
		public function login(Request $request)
		{
			JWTAuth::factory()->setTTL($myTTL);
			  $validator = Validator::make($request->all('email', 'password'), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);

				if ($validator->fails()) {
				    return response()->json(['error'=>$validator->errors()], 401);
				}
				$credentials = $request->only('email', 'password');

				try {
					if (!$token = JWTAuth::attempt($credentials)) {
						return $this->onUnauthorized();
				}
				} catch (JWTException $e) {
					return $this->onJwtGenerationError();
				}

				return $this->onAuthorized($token);
		}

		public function refresh()
    {
        $token = JWTAuth::parseToken();

        $newToken = $token->refresh();

        return new JsonResponse([
            'message' => 'token_refreshed',
            'data' => [
                'token' => $newToken
            ]
        ]);
    }


	
} 
     
