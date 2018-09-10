<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

trait ResetsPasswords
{
    /**
     * Enviar un enlace de reinicio al usuario dado.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'source' => 'required'
        ]);

        /** @var PasswordBroker $broker */
        $broker = $this->getBroker();
        $broker = Password::broker($broker);
        
        /** @var User $user */
        $user = $broker->getUser(array('email' => $this->getSendResetLinkEmailCredentials($request), 'source' => $request->get('source')));
        if ($user) {
            $token = $broker->createToken(
                $user
            );
            $resetLink = URL::to('/') . '/password/reset/' . $token . '?email=' . $request->get('email') . '&source=' . $request->get('source');
            $response = $user->notify(new ResetPasswordNotification($resetLink, $user['lang']));
            if ($response == '') {
                return $this->getSendResetLinkEmailSuccessResponse($response);
            } else {
                return $this->getSendResetLinkEmailFailureResponse($response);
            }
        } else {
            return $this->getSendResetLinkEmailFailureResponse();
        }
    }
    /**
     * Obtiene las credenciales necesarias para enviar el enlace de reinicio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getSendResetLinkEmailCredentials(Request $request)
    {
        return $request->only('email');
    }
    /**
     * Obtiene la respuesta después de que el enlace de restablecimiento se haya enviado correctamente.
     *
     * @param string $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getSendResetLinkEmailSuccessResponse()
    {
        return response()->json('SERVER.EMAIL_READY', 200);
    }
    /**
     * Obtiene la respuesta después de que no se haya podido enviar el enlace de restablecimiento.
     *
     * @param string $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getSendResetLinkEmailFailureResponse()
    {
        return response()->json('SERVER.WRONG_USER', 404);
    }
    /**
     * Muestra la vista de restablecimiento de contraseña para el token dado.
     *
     * @param string $token
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function showResetForm($token, Request $request)
    {
        $source = $request->get('source');
        $email = $request->get('email');
        return view('auth.emails.password')->with(compact('token', 'email', 'source'));
    }
    /**
     * Restablece la contraseña del usuario dado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules());
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token', 'source'
        );
        $broker = $this->getBroker();
        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect(env('APP_REDIRECTS_LINK', '../'));
            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }
    /**
     * Obtiene las reglas de validación de restablecimiento de contraseña.
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
     * Restablece la contraseña del usuario dado.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        return response()->json('SERVER.SUCCESS', 200);
    }
    /**
     * Obtiene la respuesta después de un reinicio de contraseña exitoso.
     *
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetSuccessResponse($response)
    {
        return response()->json('SERVER.RESET_SUCCESS', 200);
    }
    /**
     * Obtiene la respuesta después de un restablecimiento de contraseña fallido.
     *
     * @param  Request  $request
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetFailureResponse(Request $request, $response)
    {
        return response()->json('SERVER.RESET_FAIL', 400);
    }
    /**
     * Usa el intermediario 'broker' restablecimiento de contraseña.
     *
     * @return string|null
     */
    public function getBroker()
    {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
}