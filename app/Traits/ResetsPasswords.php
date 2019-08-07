<?php

namespace App\Traits;

use App\Models\User\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

trait ResetsPasswords {
    /**
     * Enviar un enlace de reinicio al usuario dado.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'grant_type' => 'required|in:password'
        ]);

        /** @var PasswordBroker $broker */
        $broker = $this->getBroker();
        $broker = Password::broker($broker);
        
        /** @var User $user */
        $user = $broker->getUser([
            'email' => $this->getSendResetLinkEmailCredentials($request),
            'grant_type' => $request->get('grant_type')
        ]);
        if ($user) {
            $token = $broker->createToken(
                $user
            );
            $resetLink = URL::to('/') . '/password/reset?token=' . $token . '&email='
            . $request->get('email') . '&grant_type=' . $request->get('grant_type');
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
    protected function getSendResetLinkEmailCredentials(Request $request) {
        return $request->only('email');
    }
    /**
     * Obtiene la respuesta después de que el enlace de restablecimiento se haya enviado correctamente.
     *
     * @param string $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getSendResetLinkEmailSuccessResponse() {
        return response()->json('SERVER.EMAIL_READY', 200);
    }
    /**
     * Obtiene la respuesta después de que no se haya podido enviar el enlace de restablecimiento.
     *
     * @param string $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getSendResetLinkEmailFailureResponse() {
        return response()->json('SERVER.WRONG_USER', 404);
    }
    /**
     * Muestra la vista de restablecimiento de contraseña para el token dado.
     *
     * @param string $token
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function showResetForm(Request $request) {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'grant_type' => 'required|in:password'
        ]);
        $token = $request->get('token');
        $email = $request->get('email');
        $grant_type = $request->get('grant_type');
        $password = $request->get('password');
        $password_confirmation = $request->get('password_confirmation');
        $error = '';
        return view('auth.emails.password')->with(compact('token', 'email', 'grant_type', 'password', 'password_confirmation', 'error'));
    }
    /**
     * Restablece la contraseña del usuario dado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function putReset(Request $request) {
        $token = $request->get('token');
        $email = $request->get('email');
        $grant_type = $request->get('grant_type');
        $password = $request->get('password');
        $password_confirmation = $request->get('password_confirmation');
        $error = '';
        if (!$password || !$password_confirmation) {
            $error = 'La contraseña es requerida';
        }
        if ($password != $password_confirmation) {
            $error = 'La contraseña no coincide';
        }
        if (strlen($password) < 6) {
            $error = 'La contraseña debe tener mínimo 6 caracteres';
        }
        if ($error != '') {
            return view('auth.emails.password')->with(compact(
                'token', 'email', 'grant_type', 'password', 'password_confirmation', 'error'
            ));
        }
        $response = Password::broker($this->getBroker())->reset($request->only(
            'email', 'password', 'password_confirmation', 'token', 'grant_type'
        ), function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect(env('APP_REDIRECTS_LINK', '../'));
            default:
                $error = 'Token invalido';
                return view('auth.emails.password')->with(compact(
                    'token', 'email', 'grant_type', 'password', 'password_confirmation', 'error'
                ));
        }
    }
    /**
     * Obtiene las reglas de validación de restablecimiento de contraseña.
     *
     * @return array
     */
    protected function getResetValidationRules() {
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
    protected function resetPassword($user, $password) {
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
    protected function getResetSuccessResponse($response) {
        return response()->json('SERVER.RESET_SUCCESS', 200);
    }
    /**
     * Usa el intermediario 'broker' restablecimiento de contraseña.
     *
     * @return string|null
     */
    public function getBroker() {
        return property_exists($this, 'broker') ? $this->broker : null;
    }
}