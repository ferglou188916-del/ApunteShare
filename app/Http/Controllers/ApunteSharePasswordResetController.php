<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ApunteSharePasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.recovery_request');
    }

    public function requestLink(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        Password::sendResetLink(['email' => mb_strtolower(trim($data['email']))]);

        return back()->with('status', 'Si el correo está registrado, se ha generado un enlace de recuperación.');
    }

    public function resetForm(Request $request, string $token)
    {
        return view('auth.recovery_reset', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);
        $data['email'] = mb_strtolower(trim($data['email']));

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'El enlace no es válido o ha expirado. Solicita uno nuevo.'])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
}
