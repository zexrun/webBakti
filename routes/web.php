<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\StudentController;

use App\Http\Controllers\Supervisor\TaskController as SupervisorTaskController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Grup rute yang HANYA bisa diakses oleh ADMIN
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/plotting', [AdminController::class, 'plotting'])->name('plotting');
    Route::post('/plotting/assign', [AdminController::class, 'assign'])->name('plotting.assign');

    Route::resource('users', UserController::class);
});

// Grup Supervisor
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
    Route::post('submissions/{submission}/grade', [SupervisorTaskController::class, 'grade'])->name('submissions.grade');
    Route::get('/view-student', [SupervisorController::class, 'viewStudent'])->name('students.index');

    Route::resource('tasks', SupervisorTaskController::class);
});

// Grup Student
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::post('tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('tasks.submit');

    Route::resource('tasks', StudentTaskController::class)->only(['index', 'show']);
});


Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';



/* // Rute untuk Tamu (halaman login)
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/', [LoginController::class, 'actionlogin'])->name('actionlogin'); */

/* // Rute untuk semua pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [LoginController::class, 'actionlogout'])->name('logout');
}); */

/* 
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
 */

 // Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
