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
	

		if($notaryOffice->titular_id !=null){
			if($role->name=="notary_titular"){
				throw new ShowableException(422, "Only can exits one titular.");

			}

		}

		if($notaryOffice->substitute_id !=null){	
			if($role->name=="notary_substitute"){
				throw new ShowableException(422, "Only can exits one substitute.");	
			}
		
		}

		
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

		if(NotaryOffice::where("notary_number", $notary_office["notary_number"])->count() > 0)
			throw new ShowableException(422, "The Notary Number ({$notary_office["notary_number"]}) already exists.");
	
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
		if(array_key_exists ("titular_id",  $notary_office) ){
			throw new ShowableException(422, "Sorry, titular could not be updated");	
		}

		if(array_key_exists("sat_constancy_file",$notary_office)){
			$sat = $notary_office["sat_constancy_file"];
			$file=$this->savefiles($sat, $notary="", $notary_office["notary_number"]);
			$deleteFiles =$this->deleteFile($id, "sat_constancy_file");
			$notary_office["sat_constancy_file"]=$file["sat_constancy_file"];
		}

		if(array_key_exists("notary_constancy_file", $notary_office)){
			$notary =$notary_office["notary_constancy_file"];
			$file=$this->savefiles($sat="" ,$notary, $notary_office["notary_number"]);
			$deleteFiles =$this->deleteFile($id, "notary_constancy_file");
			$notary_office["notary_constancy_file"]=$file["notary_constancy_file"];
		} 
	
			

		$notary = NotaryOffice::find($id);		
		$notary->fill($notary_office);		
		$original =$notary->getOriginal();
	
		if ($notary->save()) {		
			$substitute = $notary->getChanges("substitute_id");
			if(!empty($substitute)){
				$create =ConfigUserNotaryOffice::create([
					"notary_office_id" => $id,
					"user_id" => $notary->substitute_id
				]);
				if($create->save()){
					$delete =ConfigUserNotaryOffice::where("user_id", $original["substitute_id"])
					->where("notary_office_id", $id)->delete();
				}
				
						
			}
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
		$users_notary = request()->all();
		$relation = ConfigUserNotaryOffice::where('user_id', $user_id)->where('notary_office_id', $id)->first();
	
		if(!$relation){
			throw new ShowableException(401, "Sorry, user does not correspond to notary.");
		}
		$request = new Request($users_notary);
		try{
			$userCtrl = new UsersController();
			$u = $userCtrl->editSubUser($request);
			if($u){
				$response["notary_users"] =$u;
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
		$notary = NotaryOffice::select("id", "notary_number", "titular_id")->whereHas("titular", function($q) use($id) {
			$q->where('config_id', '=', $id); 
		})->get()->toArray();
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

}