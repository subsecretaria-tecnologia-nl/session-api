<?php

namespace App\Observers;
use  App\Models\User;
use App\Models\UserHistory;


trait HistoryObserver{
	protected static function boot(){
		parent::boot();
	
		static::created(function($data){		
			$id =$data->getOriginal()["id"];	
			foreach($data->getDirty() as $key => $value){
				$original = $data->getOriginal($key);				
				$old[] = [$key => $value];
				$new[]= [$key => $original];				
			}
				$history = new UserHistory();
				$history->comment = $data->function;
				$history->old_data = json_encode($old);
				$history->new_data = json_encode($new);
				$history->table_name= $data->table;
				$history->change_id=$id;
				$history->created_by =auth()->user()->id;
				$history->save();
		});
		
		static::updated(function($data){
			$id =$data->getOriginal()["id"];		
			foreach($data->getDirty() as $key => $value){
				$original = $data->getOriginal($key);
				$old[] = [$key => $value];
				$new[]= [$key => $original];				
			}
				$history = new UserHistory();
				$history->comment = $data->function;
				$history->old_data = json_encode($old);
				$history->new_data = json_encode($new);
				$history->table_name= $data->table;
				$history->change_id=$id;
				$history->created_by =auth()->user()->id;
				$history->save();
			
		});

	}
}