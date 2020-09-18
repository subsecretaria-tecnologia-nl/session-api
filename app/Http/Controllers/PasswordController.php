<?php

namespace App\Http\Controllers;

use App\Exceptions\ShowableException;
use App\Models\User;
use App\Traits\ResetsPasswords;
use App\Models\PasswordResets;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


class PasswordController extends Controller
{
		protected $expires = 7200;
    use ResetsPasswords;

    public function __construct()
    {
        $this->broker = 'users';
		}
   /**
     * Determina si el token es valido
     *
     * @param  $request
     */
		public function validateToken(Request $request)
    {	 
			$item = PasswordResets::where('email', $request->email)->first();
			$token = Hash::check( $request->token, $item->token);
			

				if(!$token){
					throw new ShowableException(401, "Invalid token");
				}			

        return $this->tokenExpired($item['created_at']);
		}

    /**
     * Determina si el token ha expirado
     *
     * @param  string  $createdAt
     * @return bool
     */
    public function tokenExpired($createdAt)
    {
			$expired = Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
			if($expired){
				throw new ShowableException(401, "Token is expired");
			}

			return ['message' => 'Token valid', 'success' => true, 'status'=>200];
        
		}
		
}