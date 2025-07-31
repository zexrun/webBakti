<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\DocumentController;



use App\Http\Controllers\Supervisor\FinalAssessmentController;

use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Supervisor\TaskController as SupervisorTaskController;
use App\Http\Controllers\Student\LogbookController as StudentLogbookController;
use App\Http\Controllers\Supervisor\LogbookController as SupervisorLogbookController;

use App\Http\Controllers\Student\ProfileController as StudentProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Grup rute yang HANYA bisa diakses oleh ADMIN
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/plotting', [AdminController::class, 'plotting'])->name('plotting');
    Route::post('/plotting/assign', [AdminController::class, 'assign'])->name('plotting.assign');
    Route::post('/users/{user}/resend-activation', [UserController::class, 'resendActivation'])->name('users.resend_activation');

    Route::resource('users', UserController::class);
});

// Grup Supervisor
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    // Rute utama
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

    // Rute untuk menampilkan daftar mahasiswa bimbingan
    Route::get('/students/list', [SupervisorController::class, 'index'])->name('students.list.index');

    // Rute untuk tugas (CRUD)
    Route::resource('tasks', SupervisorTaskController::class);

    // Rute untuk laporan harian (hanya melihat)
    Route::get('/logbooks', [SupervisorLogbookController::class, 'index'])->name('logbooks.index');
    Route::get('/logbooks/{logbook}', [SupervisorLogbookController::class, 'show'])->name('logbooks.show');

    // Rute untuk penilaian akhir
    Route::get('/students/{student}/assessment', [FinalAssessmentController::class, 'create'])->name('students.assessment.create');
    Route::post('/students/{student}/assessment', [FinalAssessmentController::class, 'store'])->name('students.assessment.store');
    Route::get('/students/{student}/assessment/edit', [FinalAssessmentController::class, 'edit'])->name('students.assessment.edit');
    Route::patch('/students/{student}/assessment', [FinalAssessmentController::class, 'update'])->name('students.assessment.update');

    // Rute untuk memberi nilai pada submission tugas
    Route::post('submissions/{submission}/grade', [SupervisorTaskController::class, 'grade'])->name('submissions.grade');

    Route::prefix('pdf')->name('pdf.')->group(function () {
        Route::get('/grades/{student}', [SupervisorController::class, 'generateGrade'])->name('grade');
    });

    Route::get('certificate/{student}/generate', [FinalAssessmentController::class, 'generateCertificate'])->name('pdf.certificate.generate');
    Route::get('certificate/{student}/download', [FinalAssessmentController::class, 'downloadCertificate'])->name('pdf.certificate.download');

    Route::get('/students/{student}/documents', [SupervisorController::class, 'showDocuments'])->name('students.documents');
});

// Grup Student
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::post('tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('tasks.submit');

    Route::resource('logbooks', StudentLogbookController::class);
    Route::resource('tasks', StudentTaskController::class)->only(['index', 'show']);

    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');

    Route::get('/certificate/download', [FinalAssessmentController::class, 'studentDownload'])->name('pdf.certificate.download');

    Route::resource('documents', DocumentController::class);
});


Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [LoginController::class, 'actionlogout'])->name('logout');
});

Route::get('/activate/{token}', [ActivationController::class, 'showActivationForm'])->name('activation.form');
Route::post('/activate', [ActivationController::class, 'activateAccount'])->name('activation.activate');

require __DIR__ . '/auth.php';



/* // Rute untuk Tamu (halaman login)
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/', [LoginController::class, 'actionlogin'])->name('actionlogin'); */

// Rute untuk semua pengguna yang sudah login

/* 
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
 */

 // Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
