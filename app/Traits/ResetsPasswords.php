<?php

namespace App\Traits;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Password;

trait ResetsPasswords
{
    /**
     * Enviar un enlace de reset al usuario.
     *
     * @param  $request
     * @return $response
     */
    public function postEmail(Request $request)
    {
        return $this->sendResetLinkEmail($request);
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
     * Respuesta cuando el enlace de restablecimiento se haya enviado correctamente
     *
     * @param  string  $response
     * @return $response
     */
    protected function getSendResetLinkEmailSuccessResponse($response)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Respuesta cuando el enlace de restablecimiento no se pudo enviar
     *
     * @param  string  $response
     * @return $response
     */
    protected function getSendResetLinkEmailFailureResponse($response)
    {
      return response()->json(['success' => false]);
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
                return $this->getResetSuccessResponse($response);

            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }

    /**
     * Validaciones para contraseña
     *
     * @return array
     */
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
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

        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Respuesta cuando se restablecio la contraseña
     *
     * @param  string  $response
     * @return $response
     */
    protected function getResetSuccessResponse($response)
    {
        return response()->json(['success' => true]);
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
        return response()->json(['success' => false]);
    }

    /**
     * Broker usado en el restablecimiento de la contraseña
     *
     * @return string|null
     */
    public function getBroker()
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
}