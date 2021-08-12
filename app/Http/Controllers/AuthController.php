<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController as RC;
use Zttp\Zttp;
use Zttp\ZttpRequest;
use Zttp\ZttpResponse;
use App\Models\User;

class AuthController extends RC {

    public function login(Request $request) {
        $email = $request->email;
        $password = $request->password;
        $url = config('service.passport.login_endpoint');

        if (empty($email) || empty($password)) {
            return $this->sendError('error', 'Debes insgresar un usuario y contraseña');
        }
        try {
            //aqui agregamos los demas datos requeridos para formar menu y rol
            $response = Zttp::post($url, [
                        "client_secret" => config('service.passport.client_secret'),
                        "grant_type" => "password",
                        "client_id" => config('service.passport.client_id'),
                        "username" => $request->email,
                        "password" => $request->password
            ]);
            return $this->sendResponse($response->json(), 'Login exitoso');
        } catch (BadResponseException $ex) {
            return $this->sendError('error', $ex->getMessage());
        }
    }

    public function register(Request $request) {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        if (empty($name) || empty($email) || empty($password)) {
            return $this->sendError('error', 'Debes llenar todos los campos');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->sendError('error', 'Debes ingresar un email válido');
        }

        if (strlen($password) < 6) {
            return $this->sendError('error', 'La contraseña debe tener al menos 6 caracteres');
        }

        if (User::where('email', '=', $email)->exists()) {
            return $this->sendError('error', 'El correo ingresado ya existe');
        }

        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = app('hash')->make($password);

            if ($user->save()) {
                return $this->sendResponse($email . ' creado exitosamente', 'Creado exitosamente');
            }
        } catch (Exception $ex) {
            return $this->sendError('error', $ex->getMessage());
        }
    }

    public function logout(Request $request) {
        try {
            auth()->user()->tokens()->each(function ($token) {
                $token->delete();
            });
            return $this->sendResponse("exito", "Session cerrada correctamente");
        } catch (Exception $ex) {
            return $this->sendError('error', $ex->getMessage());
        }
    }

}
