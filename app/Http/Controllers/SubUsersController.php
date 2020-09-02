<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\SubUser;
use App\Session;
use App\Information;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use hisorange\BrowserDetect\Parser as Browser;

class SubUsersController extends Controller
{
		protected $user;
 
    public function __construct()
    {
				// $this->user = JWTAuth::parseToken()->authenticate();
				$this->middleware('auth:api', ['except' => ['login','logout']]);
    }

		protected function informationUsers($object){
			$information = Information::create($object);
		}


    public function signupSubUser(Request $request)
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
				
				$id = auth()->user()->id;

        $input = $request->all();
				$input['password'] = Hash::make($input['password']);
				$input['api_token'] = Str::random(150);
				$input['rol_id'] = 3;
				
				$user = User::create($input);

				$user_id = $user->id;

				$sub_user = SubUser::create([
					'user_id' => $user_id,
					'id_user_created_by'=> $id
				]);


    
        if ($sub_user->save()){
					$token = $user->api_token;

					return response([
						'token' => $token,
						'message' => 'Subusuario creado', 
						'status' => 200
						]);
				}else{
					return response([
						'message' => 'Error al crear subusuario', 
						'status' => 401
						]);
				}


				
				
		} 

		public function getSubUser(Request $request)
		{
				$user = JWTAuth::user();

				$sub = $user->subusers()->pluck('id')->toArray();		

				$subusers = SubUser::whereIn('id', $sub)->get();

				if (count((array)$subusers) > 0) {
						return response()->json(['status' => 'success', 'user' => $subusers]);
				} else {
						return response()->json(['status' => 'fail'], 401);
				}
		}

		public function editSubUser(Request $request){	
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email|max:255',
			]);

			if ($validator->fails()) {
					return response()->json(['error'=>$validator->errors()], 401);
			}



			$user = User::find($request->id);
		
			if (!$user) {
				return response()->json([
						'success' => false,
						'message' => 'Sorry, subuser cannot be found'
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
							'message' => 'Sorry, subuser could not be updated'
					], 500);
			}
		}
		public function getSessionSubUser(Request $request){

				$user = JWTAuth::user();

				$id = $user->subusers()->pluck('id')->toArray();	
				
				$subusers = SubUser::whereIn('id', $id)->pluck('user_id')->toArray();


				$sessions_actived = User::whereIn('id', $subusers)->with(['sessions' => function ($q) {
					$q->sessionsact();
				}])->get();

		

			if (!$sessions_actived) {
				return response()->json([
						'success' => false,
						'message' => 'Sorry, user cannot be found'
				], 400);
		}

			return $sessions_actived;
		}

		public function statusSubUser(Request $request){			

			
			$user = User::find($request->id);
		
			if (!$user) {
				return response()->json([
						'success' => false,
						'message' => 'Sorry, subuser cannot be found'
				], 400);
			}

			$updated = $user->update([
				"activo"=>$request->activo
			]);

			if ($updated->save()) {
					return response()->json([
							'success' => true,
							'status'=> 200
					]);
			} else {
					return response()->json([
							'success' => false,
							'message' => 'Sorry, subuser could not be updated'
					], 500);
			}

		}

		

	
     
}