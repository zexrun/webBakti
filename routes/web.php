<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Student\StudentController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DirectorateController;

use App\Http\Controllers\Admin\SettingController;


use App\Http\Controllers\Supervisor\FinalAssessmentController;

use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Supervisor\TaskController as SupervisorTaskController;
use App\Http\Controllers\Student\LogbookController as StudentLogbookController;
use App\Http\Controllers\Supervisor\LogbookController as SupervisorLogbookController;

use App\Http\Controllers\Student\ProfileController as StudentProfileController;

use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Grup rute yang HANYA bisa diakses oleh ADMIN
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/plotting', [AdminController::class, 'plotting'])->name('plotting');
    Route::post('/plotting/assign', [AdminController::class, 'assign'])->name('plotting.assign');
    Route::post('/users/{user}/resend-activation', [UserController::class, 'resendActivation'])->name('users.resend_activation');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/student/{student}', [MonitoringController::class, 'showStudent'])->name('monitoring.student.show');
    Route::get('/monitoring/supervisor/{supervisor}', [MonitoringController::class, 'showSupervisor'])->name('monitoring.supervisor.show');
    Route::get('/monitoring/student/{student}', [MonitoringController::class, 'showStudent'])->name('monitoring.student.show');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/directorates', [SettingController::class, 'storeDirectorate'])->name('settings.storeDirectorate');
    Route::put('/settings/directorates/{id}', [SettingController::class, 'updateDirectorate'])->name('settings.updateDirectorate');
    Route::delete('/settings/directorates/{id}', [SettingController::class, 'deleteDirectorate'])->name('settings.deleteDirectorate');

    // Jabatan
    Route::post('/settings/positions', [SettingController::class, 'storePosition'])->name('settings.storePosition');
    Route::put('/settings/positions/{id}', [SettingController::class, 'updatePosition'])->name('settings.updatePosition');
    Route::delete('/settings/positions/{id}', [SettingController::class, 'deletePosition'])->name('settings.deletePosition');

    Route::post('/settings/universities', [SettingController::class, 'storeUniversity'])->name('settings.storeUniversity');
    Route::put('/settings/universities/{id}', [SettingController::class, 'updateUniversity'])->name('settings.updateUniversity');
    Route::delete('/settings/universities/{id}', [SettingController::class, 'deleteUniversity'])->name('settings.deleteUniversity');
    
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

    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::post('tasks/{task}/submit', [StudentTaskController::class, 'submit'])->name('tasks.submit');

    Route::resource('logbooks', StudentLogbookController::class);
    Route::resource('tasks', StudentTaskController::class)->only(['index', 'show']);

    Route::get('/info', [StudentProfileController::class, 'edit'])->name('info.edit');
    Route::patch('/info', [StudentProfileController::class, 'update'])->name('info.update');

    Route::get('/certificate/download', [FinalAssessmentController::class, 'studentDownload'])->name('pdf.certificate.download');

    Route::resource('documents', DocumentController::class);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'actionlogout'])->name('logout');
});

Route::get('/activate/{token}', [ActivationController::class, 'showActivationForm'])->name('activation.form');
Route::post('/activate', [ActivationController::class, 'activateAccount'])->name('activation.activate');

require __DIR__ . '/auth.php';
