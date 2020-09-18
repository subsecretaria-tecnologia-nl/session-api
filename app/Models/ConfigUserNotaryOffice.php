<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConfigUserNotaryOffice extends Model
{
	protected $fillable = [
		"notary_office_id",
		"user_id"
	];
	
	public $timestamps = false;
}
