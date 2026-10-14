<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentDownload;
use App\Models\Favorite;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ApunteShareAuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.live_login');
    }

    public function registerForm()
    {
        return view('auth.live_register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['reader', 'contributor'])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => mb_strtolower(trim($data['email'])),
            'password' => $data['password'],
        ]);
        $user->role = $data['role'];
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('apunteshare.home')->with('status', 'Cuenta creada correctamente.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (!Auth::attempt([
            'email' => mb_strtolower(trim($data['email'])),
            'password' => $data['password'],
            'is_active' => true,
        ], (bool) $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas o cuenta deshabilitada.',
            ]);
        }

        $request->session()->regenerate();
        return redirect()->route('apunteshare.home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('apunteshare.home');
    }

    public function profile()
    {
        $user = auth()->user();
        $publications = Document::with(['academicArea', 'documentType'])->where('user_id', $user->id)->latest()->get();
        $downloads = DocumentDownload::with('document')->where('user_id', $user->id)->latest()->get();
        $favorites = Favorite::with(['document' => function ($query) {
            $query->where('status', 'published');
        }])->where('user_id', $user->id)->latest()->get();
        $reports = Report::with('document')->where('user_id', $user->id)->latest()->get();
        return view('profile.live_show', compact('publications', 'downloads', 'favorites', 'reports'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);
        $user->update([
            'name' => $data['name'],
            'email' => mb_strtolower(trim($data['email'])),
        ]);
        return back()->with('status', 'Perfil actualizado.');
    }
}
