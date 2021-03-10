<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Http\Controllers\UsersController;
use App\Models\CatalogUserRoles;
use App\Models\ConfigUserNotaryOffice;
use App\Models\NotaryOffice;
use App\Models\Mailmessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailer;
use App\Notifications\NotaryNotification;
use Illuminate\Notifications\Notifiable;

class NotaryOfficesController extends Controller
{
	public function createUsersNotary($id){
		$users= request()->users;
		$response = [];
		$relationships = [];
		$addUsers=[];
		$error = null;
		$notary = null;	
		$users = to_object($users);
		$notaryOffice =NotaryOffice::where("id", $id)->first();

		$role = CatalogUserRoles::where("id", $users->role_id)->first();

		
		try{			
			$userCtrl = new UsersController();
			$u = $userCtrl->signup($users);
			$relationships[] = $u["users"]["id"];
			$response["notary_office"][$u["users"]["id"]] = $u;
			try {
				//$this->notify($u["users"]["id"], $users->password);					
				$answer = $this->notifyTable($u["users"]["id"], $users->password);	
				
			} catch (\Exception $e) {
				return ["status"=>403];
			}
			
			
		} catch (\Exception $e) {
			$error = $e;
		}
		
		if(!$error) 
		foreach ($relationships as $user_id) {			
				/**Si se agrega otro titular a la notaria pero ya existe uno, se quita el anterior y se guardan los nuevos archivos */
			if($role->name=="notary_titular"){
				if(!empty($notaryOffice->titular_id)){

					$id_titular_anterior = $notaryOffice->titular_id;

					$sat=$users->sat_constancy_file;
					$notary=$users->notary_constancy_file;

					$file=$this->savefiles($sat, $notary, $notaryOffice->notary_number);

					$notaryOffice->update([
						"titular_id"=>$user_id,
						"sat_constancy_file"=>$file["sat_constancy_file"],
						"notary_constancy_file"=>$file["notary_constancy_file"]
					]);
					$updateUser = User::where("id", $id_titular_anterior)
					->update(["status"=> 0]);
					// throw new ShowableException(422, "Only can exits one titular.");

				}

			}

			/**Si se agrega otro suplente a la notaria pero ya existe uno, se quita el anterior y se guarda el nuevo */		
			
			if($role->name=="notary_substitute"){
				if(!empty($notaryOffice->substitute_id)){	
					$id_suplente_anterior = $notaryOffice->substitute_id;					
					$updateUser = User::where("id", $id_suplente_anterior)
					->update(["status" => 0]);	
				}	
				$notaryOffice->update(["substitute_id"=>$user_id]);

			}

			ConfigUserNotaryOffice::create([
				"notary_office_id" => $id,
				"user_id" => $user_id
			]);
		
		}

		if($error) throw $error;

		$response["notary_office"] = array_merge($response["notary_office"]);
	
		return $response;

	}

	public function signup(){
		$notary_office = request()->notary_office;
		$users = [];
		$response = [];
		$relationships = [];
		$error = null;
		$notary = null;	
		extract($notary_office, EXTR_PREFIX_SAME, "notary");
		unset($notary_office["titular"], $notary_office["substitute"], $notary_office["users"]);

		$existNotary=NotaryOffice::where("notary_number", $notary_office["notary_number"])
		->where("federal_entity_id", $notary_office["federal_entity_id"])->first();


		if($existNotary){
			return [
				"code"=> 422,
				"message"=> "El número de la Notaria ({$notary_office["notary_number"]}) ya existe.",
			];
			// throw new ShowableException(422, "The Notary Number ({$notary_office["notary_number"]}) already exists.");
		}
	

	
		$roles = CatalogUserRoles::where("name", "LIKE", "notary_%")->get();
		foreach($roles as $rol){
			preg_match("/notary_(.*)/", $rol->name, $matches);
			if($matches[1] == "users" && isset($notary_users)){
				foreach($notary_users as $ind => $user){
					$notary_users[$ind]["role_id"] = $rol->id;
				}
			}else{
				if(!empty(${$matches[1]})) ${$matches[1]}["role_id"] = $rol->id;
			}
		}
	
	
		if(!empty($titular)) array_push($users, $titular);
		if(!empty($substitute)) array_push($users, $substitute);
		if(!empty($notary_users)) $users = array_merge($users, $notary_users);

		foreach($users as $user){
			try{
				$userCtrl = new UsersController();
				$u = $userCtrl->signup(to_object($user))["users"];
				$roleName = $roles->where("id", $u["role_id"])->first();
				preg_match("/notary_(.*)/", $roleName->name, $matches);
				$notary_office[$matches[1]."_id"] = $relationships[] = $u["id"];
				if($matches[1] == "users") $response["notary_office"][$matches[1]][] = $u;
				else $response["notary_office"][$matches[1]] = $u;

				try {
					$answer = $this->notifyTable($u["id"], $user["password"]);								
					
				} catch (\Exception $e) {
					return ["status"=>403];
				}


			} catch (\Exception $e) {
				$error = $e;
			}
		}

		if(!$error) {
			$sat=$notary_office["sat_constancy_file"];
			$notary=$notary_office["notary_constancy_file"];

			$file=$this->savefiles($sat, $notary, $notary_office["notary_number"]);

			$notary_office["sat_constancy_file"]=$file["sat_constancy_file"];
			$notary_office["notary_constancy_file"]=$file["notary_constancy_file"];

			$notary = NotaryOffice::create($notary_office);
			foreach ($relationships as $user_id) {
				if($notary){
					ConfigUserNotaryOffice::create([
						"notary_office_id" => $notary->id,
						"user_id" => $user_id
					]);
				}else{
					var_dump($user_id);
					User::where("id", $user_id)->delete();
				}
			}
		}
		if($error) throw $error;
		$response["notary_office"] = array_merge($notary_office, $response["notary_office"]);
	
		return $response;
	}

	public function getMany(){
		$notary = NotaryOffice::get();
		return [
			"notary_offices" => $notary->toArray()
		];
	}

	public function getSingle($id){
		$notary = NotaryOffice::where("id", $id)->with(["titular", "substitute"])->first();
		if(!$notary)
			throw new ShowableException(404, "Notary Office ID ($id) does not found.");
		return [
			"notary_office" => $notary->toArray()
		];
	}
	public function getRoles(){
		$roles = CatalogUserRoles::all()->toArray();
		return $roles;
	}

	public function getUsers($id){
		$notary = NotaryOffice::where("id", $id)->with(["users.roles"])->first();
		if(!$notary)
			throw new ShowableException(404, "Notary Office ID ($id) does not found.");
		return [
			"notary_office_users" => $notary->users->toArray()
		];
	}
	public function updateNotary($id){
		$notary_office= request()->all();
		$notary = NotaryOffice::find($id);		
		$notary->fill($notary_office);		
		
		if ($notary->save()) {			
			return [
				'success' => true,
				'status'=> 200
			];

		} else {
			throw new ShowableException(401, "Sorry, notary could not be updated.");
		}
	}

	public function updateNotaryUsers($id, $user_id){
		$error = null;
		$flag = null;
		$users_notary = request()->all();
		$relation = ConfigUserNotaryOffice::where('user_id', $user_id)->where('notary_office_id', $id)->first();
		$notaryOffice =NotaryOffice::where("id", $id)->first();
		$usern = User::where("id", $user_id)->first();

	
		if(!$relation){
			throw new ShowableException(401, "Sorry, user does not correspond to notary.");
		}

		if($users_notary["role_id"]==2){
			extract($users_notary);
			unset($users_notary["sat_constancy_file"], $users_notary["notary_constancy_file"]);	
		
		}
		$request = new Request($users_notary);
	
		try{
			$userCtrl = new UsersController();
			$u = $userCtrl->editSubUser($request);
			if($u){		
				
				$response["notary_users"] =$u;	

				if($users_notary["role_id"]==2){				

					if($usern->status==0){	
						$file=$this->savefiles($sat_constancy_file, $notary_constancy_file, $notaryOffice->notary_number);	
						$notary_office["sat_constancy_file"]=$file["sat_constancy_file"];
						$notary_office["notary_constancy_file"]=$file["notary_constancy_file"];
						$notary_office["titular_id"]=$user_id;	

						$id_titular_anterior = $notaryOffice->titular_id;
						if(!empty($notaryOffice->substitute_id) && $notaryOffice->substitute_id==$user_id){
							$notaryOffice->update(["substitute_id"=>0]);
						}
						$notaryOffice->update($notary_office);	
						
						$updateUser = User::where("id", $id_titular_anterior)->update(["status"=> 0]); 
					
					}else{
						if(isset($sat_constancy_file) || isset($notary_constancy_file)){
							$sat_constancy_file = isset($sat_constancy_file)==true ? $sat_constancy_file : "";
							$notary_constancy_file = isset($notary_constancy_file)==true ? $notary_constancy_file : "";
							$file=$this->savefiles($sat_constancy_file, $notary_constancy_file, $notaryOffice->notary_number);
							$notary_office["sat_constancy_file"]=$file["sat_constancy_file"];
							$notary_office["notary_constancy_file"]=$file["notary_constancy_file"];	
							$notary_office["titular_id"]=$user_id;
							$notaryOffice->update($notary_office);
						}	
						
					}
				}		

				

				if($users_notary["role_id"]==5){
					if(!empty($notaryOffice->substitute_id)){	
						$id_suplente_anterior = $notaryOffice->substitute_id;
						$updateUser = User::where("id", $id_suplente_anterior)
						->update(["status" => 0]);					
					}
					$notaryOffice->update(["substitute_id"=>$user_id]);

					
				}
				$usern->update(["status" => 1]);

			}
		
		} catch (\Exception $e) {
			$error = $e;
		}
		
		if($error){
			throw $error;
		} 
	
		return $response;


	}
	public function notify($id, $pass){
		$user = User::findOrFail($id);
		$username= $user->username;		
		$user->notify(new NotaryNotification($user, $username, $pass));
	}

	/**
	 *	
	 * notifyTable. Este metodo es para la modificacion de enviar el correo asincrono
	 *
	 * @param $id => para buscar el id en la tabla, $pass la constante del password
	 *
	 * @return 99 si error 100 si todo correcto
	 *
	 */
	private function notifyTable($id, $pass){
		
		try{

			$table = new Mailmessages();

			$user = User::findOrFail($id);
			$username= $user->email;


			$content = "<p>Claves de acceso:</p><p>Correo:".$username."</p><p>Contraseña:".$pass."</p>";
	        
	        $template = file_get_contents(getenv("PORTAL_HOSTNAME")."/email/template");
	        $template = str_replace(["#_EMAIL_PREHEADER_#"], "Usuario Registrado Notaria", $template);
	        $template = str_replace(["#_EMAIL_HEADER_#"], "Tu cuenta ha sido registrada como usuario de notrario", $template);
	        $template = str_replace(["#_EMAIL_CONTENT_#"], $content, $template);

			
            $table->create(
                [
                    "user"      => $username,
                    "password"  => $pass,
                    "message"   => $template,
                    "sent"      => 0 
                ]
            );

            return 100;

        }catch( \Exception $e ){
            dd($e->getMessage());
            return 99;
        }

	}

	

	public function getNotaryCommunity($id){
		$notary = NotaryOffice::select("id", "notary_number", "titular_id", "federal_entity_id")
		->whereHas("titular", function($q) use($id) {
			$q->where('config_id', '=', $id); 
		})
		->with(["estado:clave,nombre"])
		->get()->toArray();
		return [
			"notary_offices" => $notary
		];
	}

	public function savefiles($sat="", $notary="", $number_notary){		
			if($sat){
				$pdf_sat = str_replace('data:application/pdf;base64,', '', $sat);
				$pdf_sat = str_replace(' ', '+', $pdf_sat);
				$pdf_sat = base64_decode($pdf_sat);
		  
				$attach_sat = "sat_constancia_".$number_notary.".pdf";			
		  
				$path = storage_path('app/'.$attach_sat);
				\Storage::disk('local')->put($attach_sat,  $pdf_sat);

				$data["sat_constancy_file"]=$attach_sat;

			}
			
			if($notary){						
				$pdf_notary = str_replace('data:application/pdf;base64,', '', $notary);
				$pdf_notary = str_replace(' ', '+', $pdf_notary);
				$pdf_notary = base64_decode($pdf_notary);
		
				$attach_notary = "notaria_constancia_".$number_notary.".pdf";		
		
				$path = storage_path('app/'.$attach_notary);
				\Storage::disk('local')->put($attach_notary,  $pdf_notary);

				$data["notary_constancy_file"]=$attach_notary;
			}
			
			return $data;
	}

	public function getFileNotary($id, $type){	
		$notary = NotaryOffice::find($id);
		if($type=='sat'){
			$sat=$notary->sat_constancy_file;			
			$path =\Storage::url($sat);
			return $path;	
			
		}else{			
			$notary=$notary->notary_constancy_file;
			$path =\Storage::url($notary);
			return $path;
			
		}

	}

	public function deleteFile($id, $type){
		try {
			$notary = NotaryOffice::where("id", $id)->first();
			$pathtoFile = storage_path('app/'.$notary->$type);
			unlink($pathtoFile);
			return [
				'success' => true,
				'status'=> 200
			];
		 
		} catch (\Exception $e) {
			return [
				'success' => false,
				'status'=> 409
			];
		
		}	
     
    }

	public function searchUser(){
		$users=request()->all();

		if(array_key_exists("username", $users)){
			
			$user = User::where('username', $users["username"])->first();

			
			if ($user == true) {
				throw new ShowableException(422, "El nombre de usuario ya está en uso.");
			}
			
		}

		if(array_key_exists("email", $users)){

			$correo = User::where('email', $users["email"])->first();

			if ($correo == true) {
				throw new ShowableException(422, "El correo electrónico ya está en uso.");
			}
		}
	
		return [
			"status"=>200
		];

	}

	public function statusUser($id, $user_id){
		$error = null;
		$mensaje =null;
		$request = request()->all();
		$relation = ConfigUserNotaryOffice::where('user_id', $user_id)->where('notary_office_id', $id)->first();
		$notary =NotaryOffice::where("id", $id)->first();
		$user =User::find($user_id);

		if($user->role_id ==2){		
			throw new ShowableException(401, "Necesitas agregar los documentos a notaria.");
		}

		/**Si el usuario es desactivado, es suplente y esta asignado a la tabla de notaria lo quita */
		if($request["status"]==0 && $user_id==$notary->substitute_id){
			$notaryOffice = NotaryOffice::where("id", $id)->update(["substitute_id"=> 0]);
			$user->update(["status"=>0]);
			$mensaje = "Suplente desactivado.";
		}

		/**Si el usuario es activado y el role es suplente lo agrega a notaria */
		if($request["status"]==1 && $user->role_id==5){
			/**Si el usuario es activado pero existe un suplente en la notaria, quita ese suplente y se agrega el nuevo */
			if(!empty($notary->substitute_id)){
				$userSuplenteAnt=User::find($notary->substitute_id);
				$userSuplenteAnt->update(["status"=>0]);
				$notaryOffice = NotaryOffice::where("id", $id)->update(["substitute_id"=> $user_id]);
				$user->update(["status"=>1]);
				$mensaje="Suplente activado.";

			}
			$notaryOffice = NotaryOffice::where("id", $id)->update(["substitute_id"=> $user_id]);
			$user->update(["status"=>1]);
			$mensaje="Suplente activado.";
		}
		$user->update(["status"=>$request["status"]]);
		$data = $request["status"]==1 ? "activado" :  "desactivado";
		$mensaje ="Usuario ".$data; 

		if(!$relation){
			throw new ShowableException(401, "Sorry, user does not correspond to notary.");
		}
	

		return [
			'success' => true,
			'status'=> 200,
			'mensaje'=> $mensaje
		];		

	}

}