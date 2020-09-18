<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Http\Controllers\UsersController;
use App\Models\CatalogUserRoles;
use App\Models\ConfigUserNotaryOffice;
use App\Models\NotaryOffice;
use App\Models\User;

class NotaryOfficesController extends Controller
{
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
			} catch (\Exception $e) {
				$error = $e;
			}
		}

		if(!$error) $notary = NotaryOffice::create($notary_office);
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

	public function getUsers($id){
		$notary = NotaryOffice::where("id", $id)->with(["users"])->first();
		if(!$notary)
			throw new ShowableException(404, "Notary Office ID ($id) does not found.");
		return [
			"notary_office_users" => $notary->users->toArray()
		];
	}
}