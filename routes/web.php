<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\DocuController;
use App\Http\Controllers\IntegranteController;
use App\Http\Controllers\AsistenciaController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard'); // Página principal
});

// Dashboard y rutas protegidas por autenticación y verificación
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Usuarios - solo listado
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    // Asistencia - control total por AsistenciaController
    Route::get('/consejos/{consejo}/asistencia', [AsistenciaController::class, 'index'])->name('consejos.asistencia');
    Route::post('/consejos/{consejo}/asistencia', [AsistenciaController::class, 'store'])->name('consejos.asistencia.store');

    // Consejos
    Route::get('/consejos', [ConsejoController::class, 'index'])->name('consejos.index');

    // Convocatorias
    Route::get('/convocatorias/{consejo}', [ConvocatoriaController::class, 'index'])->name('convocatorias.index');
    Route::get('/consejos/convocatorias', [ConsejoController::class, 'index'])->name('consejos.convocatorias');

    // Integrantes (CRUD)
    Route::resource('integrantes', IntegranteController::class);
    Route::get('/consejos/{consejo}/integrantes', [IntegranteController::class, 'index'])->name('consejos.integrantes');

    // Documentación
    Route::get('/docu/{integrante}', [DocuController::class, 'index'])->name('docu.index');
    Route::post('/docu/{integrante}', [DocuController::class, 'store'])->name('docu.store');
    Route::get('/docu/download/{documento}', [DocuController::class, 'download'])->name('docu.download');
    Route::post('/docu/{integrante}/enviar', [DocuController::class, 'enviarDocumentos'])->name('docu.enviar');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/about', function () {
        return Inertia::render('About');
    })->name('about');

    // Aquí la ruta corregida para que apunte al método asistencia en UserController
    Route::get('/usuarios/asistencia/{consejo}', [UserController::class, 'asistencia'])
        ->name('usuarios.asistencia.resumen');

});

require __DIR__ . '/auth.php';
