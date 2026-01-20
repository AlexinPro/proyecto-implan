<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocuController;
use App\Http\Controllers\IntegranteController;
use App\Http\Controllers\IntegranteBajaController;
use App\Http\Controllers\LegalidadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::get('/about', fn () => Inertia::render('About'))->name('about');

/*
|--------------------------------------------------------------------------
| Dashboard protegido
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Asistencias y participación
    |--------------------------------------------------------------------------
    */
    Route::get('/consejos/asistencias', [ConsejoController::class, 'index'])
        ->name('consejos.asistencias');

    Route::get('/asistencias/{consejo}', [AsistenciaController::class, 'index'])
        ->name('asistencias.index');

    Route::post('/asistencias/{consejo}', [AsistenciaController::class, 'store'])
        ->name('asistencias.store');

    Route::get('/asistencia/{consejo}', [AsistenciaController::class, 'show'])
        ->name('asistencia.show');

    Route::get('/consejos/{consejo}/calendar', [AsistenciaController::class, 'calendar'])
        ->name('asistencia.calendar');

    Route::get('/consejos/{consejo}/asistencias/create', [AsistenciaController::class, 'create'])
        ->name('asistencias.create');

    Route::get(
        '/consejos/{consejo}/asistencias/{integrante}/historial',
        [AsistenciaController::class, 'history']
    )->name('asistencia.history');

    Route::post(
        '/consejos/{consejo}/asistencias/sesion',
        [AsistenciaController::class, 'storeSesion']
    )->name('asistencias.sesion.store');

    Route::get('/consejos/{consejo}/evidencias', [AsistenciaController::class, 'evidencias'])
        ->name('asistencias.evidencias');

    /*
    |--------------------------------------------------------------------------
    | Consejos
    |--------------------------------------------------------------------------
    */
    Route::get('/consejos', [ConsejoController::class, 'index'])
        ->name('consejos.index');

    Route::get('/consejos/convocatorias', [ConsejoController::class, 'index'])
        ->name('consejos.convocatorias');

    Route::get('/consejos/legalidad', [ConsejoController::class, 'index'])
        ->name('consejos.legalidad');

    Route::get('/consejos/reportes', [ConsejoController::class, 'index'])
        ->name('consejos.reportes');

    /*
    |--------------------------------------------------------------------------
    | Convocatorias
    |--------------------------------------------------------------------------
    */
    Route::get('/convocatorias/{consejo}', [ConvocatoriaController::class, 'index'])
        ->name('convocatorias.index');

    Route::post('/convocatorias/{consejo}', [ConvocatoriaController::class, 'store'])
        ->name('convocatorias.store');

    Route::patch('/convocatorias/{convocatoria}/estado', [ConvocatoriaController::class, 'toogleEstado'])
        ->name('convocatorias.toogleEstado');

    Route::post('/convocatorias/subir', [ConvocatoriaController::class, 'subir'])
        ->name('convocatorias.subir');

    /*
    |--------------------------------------------------------------------------
    | Documentación
    |--------------------------------------------------------------------------
    */
    Route::get('/documentos/{integrante}', [DocuController::class, 'index'])
        ->name('docu.index');

    Route::post('/documentos/{integrante}', [DocuController::class, 'store'])
        ->name('docu.store');

    Route::get('/documento/descargar/{id}', [DocuController::class, 'download'])
        ->name('docu.download');

    Route::get('/documento/ver/{id}', [DocuController::class, 'show'])
        ->name('docu.show');

    /*
    |--------------------------------------------------------------------------
    | Integrantes
    |--------------------------------------------------------------------------
    */
    Route::resource('integrantes', IntegranteController::class);

    Route::get('/consejos/{consejo}/integrantes', [IntegranteController::class, 'index'])
        ->name('consejos.integrantes');

    Route::post('/integrantes/{integrante}/baja', [IntegranteBajaController::class, 'store'])
        ->name('integrantes.baja');

    /*
    |--------------------------------------------------------------------------
    | Legalidad y control normativo
    |--------------------------------------------------------------------------
    */
    Route::get('/legalidad/{consejo}', [LegalidadController::class, 'index'])
        ->name('legalidad.index');

    Route::post('/legalidad/{consejo}', [LegalidadController::class, 'store'])
        ->name('legalidad.store');

    Route::post('/legalidad/{legalidad}/reeleccion', [LegalidadController::class, 'iniciarReeleccion'])
        ->name('legalidad.reeleccion');

    Route::delete('/legalidad/{legalidad}', [LegalidadController::class, 'destroy'])
        ->name('legalidad.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');

    Route::get('/consejos/{consejo}/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');

    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
Auth routes
*/
require __DIR__ . '/auth.php';
