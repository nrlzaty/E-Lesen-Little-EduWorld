<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ActionListController;
use App\Http\Controllers\PengalamanPenyeliaController;
use App\Http\Controllers\PengalamanPengurusController;
use App\Http\Controllers\MaklumatPekerjaController;
use App\Http\Controllers\ButiranPenyeliaController;
use App\Http\Controllers\ButiranTaskaController;
use App\Http\Controllers\ButiranPengurusController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ButiranPengusahaController;
use App\Http\Controllers\Configuration\CategoryController as CategoryConfigurationController;
use App\Http\Controllers\Configuration\UsersConfigurationController;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Configuration\ConfigCodeController as CodeConfigurationController;
use App\Models\MaklumatPekerja;
use App\Models\PengalamanPengurus;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RenewLicenseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role === 'Kerani') {
            return redirect()->route('applicant.index'); // Redirect to the list pemohonan baru page
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('/tetapan/Pengguna', UsersConfigurationController::class)
    ->names([
        'index' => 'setting.user.index',
        'create' => 'setting.user.create',
        'store' => 'setting.user.store',
        'edit' => 'setting.user.edit',
        'update' => 'setting.user.update',
        'show' => 'setting.user.show',
        'destroy' => 'setting.user.destroy',
    ]);
    Route::get('users',[UsersConfigurationController::class,'index'])->name('users.index');
    Route::post('users',[UsersConfigurationController::class,'store']);

    Route::resource('/tetapan/Peranan', RoleController::class)
        ->names([
            'index' => 'setting.role.index',
            'create' => 'setting.role.create',
            'store' => 'setting.role.store',
            'edit' => 'setting.role.edit',
            'update' => 'setting.role.update',
            'show' => 'setting.role.show',
            'destroy' => 'setting.role.destroy',
        ]);
    #Tetapan\Kategori
    Route::controller(CategoryConfigurationController::class)->group(function() {
        Route::get('/tetapan/konfigurasi-sistem/kategori', 'index')->name('setting.category.index');
        Route::get('/tetapan/konfigurasi-sistem/kategori/{id}/edit', 'edit')->name('setting.category.edit');
        Route::put('/tetapan/konfigurasi-sistem/kategori/{id}/update', 'update')->name('setting.category.update');
        Route::delete('/tetapan/konfigurasi-sistem/kategori/{id}/destroy', 'destroy')->name('setting.category.destroy');
    });

    #Tetapan\Kod
    Route::controller(CodeConfigurationController::class)->group(function() {
        Route::get('/tetapan/konfigurasi-sistem/kod/{id}', 'index')->name('setting.code.index');
        Route::get('/tetapan/konfigurasi-sistem/kod/{category}/create', 'create')->name('setting.code.create');
        Route::post('/tetapan/konfigurasi-sistem/kod', 'store')->name('setting.code.store');
        Route::get('/tetapan/konfigurasi-sistem/kod/{id}/show', 'show')->name('setting.code.show');
        Route::get('/tetapan/konfigurasi-sistem/kod/{id}/edit', 'edit')->name('setting.code.edit');
        Route::put('/tetapan/konfigurasi-sistem/kod/{id}/update', 'update')->name('setting.code.update');
        Route::delete('/tetapan/konfigurasi-sistem/kod/{id}/destroy', 'destroy')->name('setting.code.destroy');
        Route::get('/tetapan/konfigurasi-sistem/kod/{id}/papar', 'display')->name('setting.code.display');
    });

    Route::controller(ApplicantController::class)->group(function() {
        Route::get('/applicant', 'index')->name('applicant.index');
        Route::get('/applicant/create', 'create')->name('applicant.create');
        Route::put('/applicant/{id}/update', 'update')->name('applicant.update');
        Route::delete('/applicant/{id}/destroy', 'destroy')->name('applicant.destroy');
        Route::get('/applicant/{id}/display', 'display')->name('applicant.display');
        Route::get('/applicant/{id}/edit', 'edit')->name('applicant.edit');
        Route::post('/applicant/store', 'store')->name('applicant.store');
        Route::get('/applicant/{id}/show', 'show')->name('applicant.show');
      
    });

    Route::controller(ButiranPengusahaController::class)->group(function() {
        Route::get('/pengusaha', 'index')->name('pengusaha.index');
        Route::get('/pengusaha/{applicant_id}/ create', 'create')->name('pengusaha.create');
        Route::put('/pengusaha/{id}/update', 'update')->name('pengusaha.update');
        Route::delete('/pengusaha/{id}/destroy', 'destroy')->name('pengusaha.destroy');
        Route::get('/pengusaha/{id}/display', 'display')->name('pengusaha.display');
        Route::get('/pengusaha/{id}/edit', 'edit')->name('pengusaha.edit');
        Route::post('/pengusaha/store', 'store')->name('pengusaha.store');
        Route::get('/pengusaha/{id}/show', 'show')->name('pengusaha.show');
    });

    Route::controller(ButiranTaskaController::class)->group(function() {
        Route::get('/butiran-taska', 'index')->name('butirantaska.index');
        Route::get('/butiran-taska/{applicant_id}/create', 'create')->name('butirantaska.create');
        Route::put('/butiran-taska/{id}/update', 'update')->name('butirantaska.update');
        Route::delete('/butiran-taska/{id}/destroy', 'destroy')->name('butirantaska.destroy');
        Route::get('/butiran-taska/{id}/display', 'display')->name('butirantaska.display');       
        Route::get('/butiran-taska/{id}/edit', 'edit')->name('butirantaska.edit');
        Route::post('/butiran-taska/store', 'store')->name('butirantaska.store');
        Route::get('/butiran-taska/{id}/show', 'show')->name('butirantaska.show');
    });

    Route::controller(ButiranPengurusController::class)->group(function() {
        Route::get('/butiran-pengurus', 'index')->name('butiranpengurus.index');
        Route::get('/butiran-pengurus/{applicant_id}/create', 'create')->name('butiranpengurus.create');
        Route::put('/butiran-pengurus/{id}/update', 'update')->name('butiranpengurus.update');
        Route::delete('/butiran-pengurus/{id}/destroy', 'destroy')->name('butiranpengurus.destroy');
        Route::get('/butiran-pengurus/{id}/display', 'display')->name('butiranpengurus.display');
        Route::get('/butiran-pengurus/{id}/edit', 'edit')->name('butiranpengurus.edit');
        Route::post('/butiran-pengurus/store', 'store')->name('butiranpengurus.store');
        Route::get('/butiran-pengurus/{id}/show', 'show')->name('butiranpengurus.show');
    });

    Route::controller(ButiranPenyeliaController::class)->group(function() {
        Route::get('/butiran-penyelia', 'index')->name('butiranpenyelia.index');
        Route::get('/butiran-penyelia/{applicant_id}/create', 'create')->name('butiranpenyelia.create');
        Route::put('/butiran-penyelia/{id}/update', 'update')->name('butiranpenyelia.update');
        Route::delete('/butiran-penyelia/{id}/destroy', 'destroy')->name('butiranpenyelia.destroy');
        Route::get('/butiran-penyelia/{id}/display', 'display')->name('butiranpenyelia.display');
        Route::get('/butiran-penyelia/{id}/edit', 'edit')->name('butiranpenyelia.edit');
        Route::post('/butiran-penyelia/store', 'store')->name('butiranpenyelia.store');
        Route::get('/butiran-penyelia/{id}/show', 'show')->name('butiranpenyelia.show');
    });

    Route::controller(MaklumatPekerjaController::class)->group(function() {
        Route::get('/maklumat-pekerja', 'index')->name('maklumatpekerja.index');
        Route::get('/maklumat-pekerja/{applicant_id}/create', 'create')->name('maklumatpekerja.create');
        Route::put('/maklumat-pekerja/{id}/update', 'update')->name('maklumatpekerja.update');
        Route::delete('/maklumat-pekerja/{id}/destroy', 'destroy')->name('maklumatpekerja.destroy');
        Route::get('/maklumat-pekerja/{id}/display', 'display')->name('maklumatpekerja.display');
        Route::get('/maklumat-pekerja/{id}/edit', 'edit')->name('maklumatpekerja.edit');
        Route::post('/maklumat-pekerja/store', 'store')->name('maklumatpekerja.store');
        Route::get('/maklumat-pekerja/{id}/show', 'show')->name('maklumatpekerja.show');
    });

    Route::controller(PengalamanPengurusController::class)->group(function() {
        Route::get('/pengalaman-pengurus', 'index')->name('pengalamanpengurus.index');
        Route::get('/pengalaman-pengurus/{applicant_id}/create', 'create')->name('pengalamanpengurus.create');
        Route::put('/pengalaman-pengurus/{id}/update', 'update')->name('pengalamanpengurus.update');
        Route::delete('/pengalaman-pengurus/{id}/destroy', 'destroy')->name('pengalamanpengurus.destroy');
        Route::get('/pengalaman-pengurus/{id}/display', 'display')->name('pengalamanpengurus.display');
        Route::get('/pengalaman-pengurus/{id}/edit', 'edit')->name('pengalamanpengurus.edit');
        Route::post('/pengalaman-pengurus/store', 'store')->name('pengalamanpengurus.store');
        Route::get('/pengalaman-pengurus/{id}/show', 'show')->name('pengalamanpengurus.show');
    });

    Route::controller(PengalamanPenyeliaController::class)->group(function() {
        Route::get('/pengalaman-penyelia', 'index')->name('pengalamanpenyelia.index');
        Route::get('/pengalaman-penyelia/{applicant_id}/create', 'create')->name('pengalamanpenyelia.create');
        Route::put('/pengalaman-penyelia/{id}/update', 'update')->name('pengalamanpenyelia.update');
        Route::delete('/pengalaman-penyelia/{id}/destroy', 'destroy')->name('pengalamanpenyelia.destroy');
        Route::get('/pengalaman-penyelia/{id}/display', 'display')->name('pengalamanpenyelia.display');
        Route::get('/pengalaman-penyelia/{id}/edit', 'edit')->name('pengalamanpenyelia.edit');
        Route::post('/pengalaman-penyelia/store', 'store')->name('pengalamanpenyelia.store');
        Route::get('/pengalaman-penyelia/{id}/show', 'show')->name('pengalamanpenyelia.show');
    });

    Route::controller(ActionListController::class)->group(function() {
        Route::get('/status', 'index')->name('status.index'); // Ensure this is correctly defined
        Route::get('/status/{applicant_id}/create', 'create')->name('status.create');
        Route::put('/status/{id}/update', 'update')->name('status.update');
        Route::delete('/status/{id}/destroy', 'destroy')->name('status.destroy');
        Route::get('/status/{id}/display', 'display')->name('status.display');
        Route::get('/status/{id}/edit', 'edit')->name('status.edit');
        Route::post('/status/store', 'store')->name('status.store');
        Route::get('/status/{id}/show', 'show')->name('status.show');
        Route::get('/status/dalam-semakan', [ActionListController::class, 'dalamSemakan'])->name('status.dalam-semakan');
        Route::get('/status/telah-disemak', [ActionListController::class, 'telahDisemak'])->name('status.telah-disemak');
        Route::get('/status/diluluskan', [ActionListController::class, 'diluluskan'])->name('status.diluluskan');
        Route::get('/status/tidak-diluluskan', [ActionListController::class, 'tidakDiluluskan'])->name('status.tidak-diluluskan');
        Route::get('/status/pemohonan-baru', [ActionListController::class, 'pemohonanBaru'])->name('status.pemohonan-baru');
        Route::get('/status/borang-tidak-lengkap', [ActionListController::class, 'borangTidakLengkap'])->name('status.borang-tidak-lengkap');
        Route::get('/status/renew-review/{id}', 'renewReview')->name('status.renew-review');
        Route::get('/status/perbaharui-lesen', 'perbaharuiLesenList')->name('status.perbaharui-lesen');
        Route::get('/status/perbaharui-lesen-telah-disemak', 'perbaharuiLesenTelahDisemak')->name('status.perbaharui-lesen-telah-disemak');
    });

    // Kerani notification API
    Route::get('/kerani-notifications', function () {
        $user = Auth::user();
        $notifications = [];
        if ($user->role === 'kerani') {
            $notifications = \App\Models\Applicant::where('user_id', $user->id)
                ->where('notification_status', 'alert_kerani')
                ->whereIn('status', ['Borang Tidak Lengkap', 'Diluluskan', 'Tidak Diluluskan'])
                ->get(['id', 'status', 'komen']);
        }
        return response()->json(['notifications' => $notifications]);
    });

    Route::get('/dashboard-data', [ActionListController::class, 'dashboardData']);
    Route::get('/user-role', function () {
        return response()->json(['role' => Auth::user()->role]);
    });

    Route::controller(PDFController::class)->group(function() {
        Route::post('/generate-pdf', 'generatePDF')->name('generate.pdf');
        Route::get('/generate-pdf-admin/{id}', 'generateAdminPDF')->name('generate-pdf-admin');
    });   
    
    Route::controller(DocumentController::class)->group(function() {
        Route::get('/dokumen', 'index')->name('dokumen.index');
        Route::get('/dokumen/create/{applicant_id}', 'create')->name('dokumen.create'); // Ensure applicant_id is included
        Route::post('/dokumen/store', 'store')->name('document.store'); // Ensure this matches the name used in Vue
        Route::get('/dokumen/{applicant_id}/display', 'display')->name('dokumen.display');
        Route::get('/dokumen/{applicant_id}/show', 'show')->name('dokumen.show');
        Route::get('/dokumen/{id}/edit', 'edit')->name('dokumen.edit');
        Route::post('/dokumen/{id}/update', 'update')->name('dokumen.update');
    });

    Route::controller(RenewLicenseController::class)->group(function () {
        Route::get('/renew-license', 'index')->name('renew-license.index');
        Route::post('/renew-license/{id}', 'renew')->name('renew-license.renew');
    });

    // Add this line for Pegawai Penyemakan renew notifications
    Route::get('/penyemak-renew-notifications', [\App\Http\Controllers\ActionListController::class, 'penyemakRenewNotifications']);
    Route::get('/penyemak-renew-dalam-semak-notifications', [\App\Http\Controllers\ActionListController::class, 'penyemakRenewDalamSemakNotifications']);
    Route::get('/perlulus-telah-disemak-notifications', [\App\Http\Controllers\ActionListController::class, 'perlulusTelahDisemakNotifications']);
    Route::get('/perlulus-renew-telah-disemak-notifications', [\App\Http\Controllers\ActionListController::class, 'perlulusRenewTelahDisemakNotifications']);
    Route::post('/renew/{id}/complete', [\App\Http\Controllers\ApplicantController::class, 'completeRenewal'])->name('renew.complete');
});

