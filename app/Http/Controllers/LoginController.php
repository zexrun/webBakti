<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    public function login() {
        if (Auth::check()) { return redirect()->route('home'); }
        return view('auth.login');
    }
    public function actionlogin(Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'],'password' => ['required'],]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($user->role == 'supervisor') {
                return redirect()->route('supervisor.dashboard');
            }
            if ($user->role == 'student') {
                return redirect()->route('student.dashboard');
            }
            return redirect()->route('home');
        }
        return back()->withErrors(['email' => 'Email atau Password salah.'])->onlyInput('email');
    }
    public function actionlogout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}