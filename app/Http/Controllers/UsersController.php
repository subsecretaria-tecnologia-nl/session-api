<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Session;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use hisorange\BrowserDetect\Parser as Browser;

class UsersController extends Controller
{
		protected $user;
 
    public function __construct()
    {
				// $this->user = JWTAuth::parseToken()->authenticate();

				$this->middleware('auth:api', ['except' => ['login','logout']]);
    }



    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8'
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
				$input['rol_id'] = 2;
				$user = User::create($input);
				
				$token = $user->api_token;

        return response([
					'token' => $token, 
					'message' => 'Cuenta creada', 
					'status' => 200
					]);
		} 

		public function getUser(Request $request)
		{
				$user = JWTAuth::user();
				if (count((array)$user) > 0) {
						return response()->json(['status' => 'success', 'user' => $user]);
				} else {
						return response()->json(['status' => 'fail'], 401);
				}
		}

		public function editUser(Request $request){		
			
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email|max:255',
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

			$updated = $user->fill($request->all())->save();

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

			$sessions_actived = User::where('id', $id)->with(['sessions' => function ($q) {
				$q->sessionsact();
			}])->get();

			if (!$sessions_actived) {
				return response()->json([
						'success' => false,
						'message' => 'Sorry, user  cannot be found'
				], 400);
		}

			return $sessions_actived;
		}

	
     
}