<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotaryOffice extends Model
{
	protected $fillable = [
		"notary_number",
		"phone",
		"fax",
		"email",
		"street",
		"number",
		"district",
		"federal_entity_id",
		"city_id",
		"zip",
		"titular_id",
		"substitute_id"
	];

	public function titular () {
		return $this->hasOne("App\Models\User", "id", "titular_id");
	}

	public function substitute () {
		return $this->hasOne("App\Models\User", "id", "substitute_id");
	}

	public function users () {
		return $this->belongsToMany("App\Models\User", "App\Models\ConfigUserNotaryOffice", "notary_office_id");
	}
}
