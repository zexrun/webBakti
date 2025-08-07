<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AbsenUser;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    public function showActivationForm(string $token)
    {
        $hashedToken = hash('sha256', $token);
        $user = User::where('activation_token', $hashedToken)->whereNull('email_verified_at')->first();

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Link aktivasi tidak valid atau sudah kedaluwarsa.']);
        }

        return view('auth.activate', ['token' => $token, 'email' => $user->email]);
    }

    public function activateAccount(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $hashedToken = hash('sha256', $request->token);
        $user = User::where('activation_token', $hashedToken)->firstOrFail();

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'activation_token' => null,
        ]);

        $absenUser = AbsenUser::where('email', $user->email)->first();
        if ($absenUser) {
            $absenUser->update([
            'name' => $user->name,
            'username' => $user->username,
            'password' => $user->password,
        ]);
    }


        $user->notify(new WelcomeEmail($user));

        Auth::login($user);

        return redirect('/home')->with('success', 'Akun Anda berhasil diaktifkan!');
    }
}