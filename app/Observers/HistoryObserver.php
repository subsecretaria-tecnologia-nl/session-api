<?php

namespace App\Observers;
use App\Models\UserHistory;
use  App\Models\User;


trait HistoryObserver{
	protected static function boot(){
		parent::boot();

		static::created(function($data){		
			foreach($data->getDirty() as $key => $value){
				$original = $data->getOriginal($key);				
				$old[] = [$key => $value];
				$new[]= [$key => $original];				
			}
				$history = new UserHistory();
				$history->comment = "Crear subsuario";
				$history->old_data = json_encode($old);
				$history->new_data = json_encode($new);
				$history->created_by =auth()->user()->id;
				$history->save();
		});
		static::updated(function($data){
			foreach($data->getDirty() as $key => $value){
				$original = $data->getOriginal($key);				
				$old[] = [$key => $value];
				$new[]= [$key => $original];				
			}
				$history = new UserHistory();
				$history->comment = "Editar subsuario";
				$history->old_data = json_encode($old);
				$history->new_data = json_encode($new);
				$history->created_by =auth()->user()->id;
				$history->save();
			
		});

	}
}