<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubUser;
use App\Models\Session;
use App\Models\Information;
use Carbon\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;


class SubUsersController extends Controller
{
		protected $user;
 
    public function __construct()
    {
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

		public function getSubUser(Request $request, $id)
		{
			
				$subuser = User::where('id', $id)->get();

				if (count((array)$subuser) > 0) {
						return response()->json(['status' => 'success', 'user' => $subuser]);
				} else {
						return response()->json(['status' => 'fail'], 401);
				}
		}

		public function editSubUser(Request $request, $id){	
			
			$user = User::where('id', $id)
			->where('name', '=', $request->name)
			->where('email', '<>', $request->email)->first();
			if ($user == true) {
				return response()->json(['error'=>'User already exists', 'status'=>401]);
			}
			$password = base64_decode($request->password);

			$request->merge([
				'password' => $password,
			]);
			
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email|max:255',
				'name'=>'string',
				'password' => ['required',
				 'string',
				 'min:8',
				 'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
				 ]
			]);

			$subuser = User::find($id);
		
			if (!$subuser) {
				$information = Information::create([
					'user_id' => auth()->user()->id,
					'action_date'=>Carbon::now()->format('Y-m-d H:i:s'),
					'description'=>'Modificar sub usuario',	
					'modified_variables'=>"",
					'device_type'=>$result->deviceType(),
					'browser_type'=>$result->browserType()
				]);
				$information->save();
				return response()->json([
						'success' => false,
						'message' => 'Sorry, subuser cannot be found'
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
							"status"=>401,
							'message' => 'Sorry, subuser could not be updated'
					] );
			}
		}
		public function getSessionSubUser($id){

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