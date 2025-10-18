<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\DocuController;
use App\Http\Controllers\IntegranteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard'); // o Welcome si quieres
});

// Dashboard protegido
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por auth
Route::middleware('auth')->group(function () {
    // Archvivo digital
    // Usuarios
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    // Consejos 
    Route::get('/consejos', [ConsejoController::class, 'index'])->name('consejos.index');

    // Convocatorias
    // Muestra los consejos con el origen "convocatorias"
    Route::get('/consejos/convocatorias', [ConsejoController::class, 'index'])
    ->name('consejos.convocatorias');
    // Muestra las convocatorias de un consejo específico
    Route::get('/convocatorias/{consejo}', [ConvocatoriaController::class, 'index'])
    ->name('convocatorias.index');
    // Guarda una nueva convocatoria
    Route::post('/convocatorias/{consejo}', [ConvocatoriaController::class, 'store'])
    ->name('convocatorias.store');
    // Cambia el estado (palomita / tache)
    Route::patch('/convocatorias/{convocatoria}/estado', [ConvocatoriaController::class, 'toogleEstado'])
    ->name('convocatorias.toogleEstado');
    Route::post('/convocatorias/subir', [ConvocatoriaController::class, 'subir'])
    ->name('convocatorias.subir');
    /*Route::get('/convocatorias/{consejo}', [ConvocatoriaController::class, 'index'])
    ->name('convocatorias.index');
    //convocatorias muestra los mismos consejos pero con la ruta para concovatorias
     Route::get('/consejos/convocatorias', [ConsejoController::class, 'index'])
    ->name('consejos.convocatorias');*/
    
    // Integrantes (CRUD completo con resource)
    Route::resource('integrantes', IntegranteController::class);
    // Ruta para ver integrantes de un consejo específico
    Route::get('/consejos/{consejo}/integrantes', [IntegranteController::class, 'index'])
        ->name('consejos.integrantes');

    // Documentacion
    Route::get('/documentos/{integrante}', [DocuController::class, 'index'])->name('docu.index');
    Route::post('/documentos/{integrante}', [DocuController::class, 'store'])->name('docu.store');
    Route::get('/documento/descargar/{id}', [DocuController::class, 'download'])->name('docu.download');
    Route::get('/documento/ver/{id}', [DocuController::class, 'show'])->name('docu.show');
});

    // Asistencia y participacion
    Route::get('/about', fn () => Inertia::render('About'))->name('about');
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

require __DIR__ . '/auth.php';
