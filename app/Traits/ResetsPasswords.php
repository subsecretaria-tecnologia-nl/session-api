<?php

namespace App\Traits;
use App\Exceptions\ShowableException;
use App\Models\PasswordResets;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Password;

trait ResetsPasswords
{
		

	 /**
     * Broker usado en el restablecimiento de la contraseña
     *
     * @return string|null
     */
    public function getBroker()
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
	  /**
     * Enlace de restablecimiento.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        return property_exists($this, 'subject') ? $this->subject : 'Enlace reset password';
		}
		 /**
     * Respuesta cuando no se pudo restablecer la contraseña
     *
     * @param  Request  $request
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetFailureResponse(Request $request, $response)
    {
			throw new ShowableException(401, "Password could not be reset");
    }
		 /**
     * Respuesta cuando se restablecio la contraseña
     *
     * @param  string  $response
     * @return $response
     */
    protected function getResetSuccessResponse($response, $email)
    {
	
        return ['success' => true, 'email'=>$email, 'status'=>200];
    }

    /**
     * Respuesta cuando el enlace de restablecimiento se haya enviado correctamente
     *
     * @param  string  $response
     * @return $response
     */
		 /**
     * Validaciones para contraseña
     *
     * @return array
     */
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|string|email|max:255',
            'password' => [
							'required',
							'string',
							'min:8',
							'regex:/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,}$/',
						],
        ];
    }
   

		protected function getSendResetLinkEmailSuccessResponse($response)
    {
			return ['success' => true, 'status'=>200];
    }

    /**
     * Respuesta cuando el enlace de restablecimiento no se pudo enviar
     *
     * @param  string  $response
     * @return $response
     */
    protected function getSendResetLinkEmailFailureResponse($response)
    {
      return ['success' => true, 'status'=>200];
		}
		
    /**
     * Enviar un enlace de reset al usuario.
     *
     * @param  $request
     * @return $response
     */
    public function postEmail(Request $request)
    {	
			$user=User::where('email', $request->email)->first();
			if($user){
				return $this->sendResetLinkEmail($request);
			}else{
				throw new ShowableException(401, "User not exists");
			}
        
		}
		
    /**
     * Cambio de contraseña.
     *
     * @param $request
     * @return $response
     */
    public function postReset(Request $request)
    {
        return $this->reset($request);
    }

    
    /**
     * Cambio de contraseña.
     *
     * @param $request
     * @return $response
     */
    public function reset(Request $request)
    {
			
        $this->validate($request, $this->getResetValidationRules());

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
				);				

				$broker = $this->getBroker();			

        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
				});
				
			

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->getResetSuccessResponse($response, $request->email);

            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }   

    /**
     * Restablecer la contraseña de usuario
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
				$user->unsetEventDispatcher();
        $user->save();

        return ['success' => true, 'status'=>200];
    }

   /**
     * Enviar un enlace de reset al usuario.
     *
     * @param  $request
     * @return $response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $broker = $this->getBroker();

        $response = Password::broker($broker)->sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->getSendResetLinkEmailSuccessResponse($response);

            case Password::INVALID_USER:
            default:
                return $this->getSendResetLinkEmailFailureResponse($response);
        }
		} 	
		
   
}