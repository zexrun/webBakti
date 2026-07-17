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
        $user = User::where('activation_token', $hashedToken)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            return redirect('/login')->withErrors(['email' => 'Link aktivasi tidak valid atau sudah kedaluwarsa.']);
        }

        // Check if token has expired
        if ($user->activation_token_expires_at && $user->activation_token_expires_at->isPast()) {
            return redirect('/login')->withErrors(['email' => 'Link aktivasi telah kedaluwarsa. Silahkan minta link aktivasi yang baru.']);
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

        // Check if token has expired
        if ($user->activation_token_expires_at && $user->activation_token_expires_at->isPast()) {
            return back()->withErrors(['token' => 'Link aktivasi telah kedaluwarsa. Silahkan minta link aktivasi yang baru.']);
        }

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'activation_token' => null,
            'activation_token_expires_at' => null,
        ]);
    


        $user->notify(new WelcomeEmail($user));

        Auth::login($user);

        return redirect('/home')->with('success', 'Akun Anda berhasil diaktifkan!');
    }
}