<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConsejoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\DocuController;
use App\Http\Controllers\IntegranteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

Route::get('/', function () {
    return Inertia::render('Dashboard'); // o Welcome si quieres
});

// Dashboard protegido
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por auth
Route::middleware('auth')->group(function () {
    //Asistencia y participacion
    // Muestra los consejos con el origen "asistencias"
    Route::get('/consejos/asistencias', [ConsejoController::class, 'index'])
    ->name('consejos.asistencias');
    // Muestra las asistencias de un consejo específico
    Route::get('/asistencias/{consejo}', [AsistenciaController::class, 'index'])
    ->name('asistencias.index');
    Route::post('/asistencias/{consejo}', [AsistenciaController::class, 'store'])
    ->name('asistencias.store');
    Route::get('/asistencia/{consejo}', [AsistenciaController::class, 'show'])
    ->name('asistencia.show');
    Route::get('/consejos/{consejo}/calendar', [AsistenciaController::class, 'calendar'])
    ->name('asistencia.calendar');

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

    //reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    // Mostrar los consejos para seleccionar el reporte
    Route::get('/consejos/reportes', [ConsejoController::class, 'index'])->name('consejos.reportes');
    // Mostrar los reportes del consejo seleccionado
    Route::get('/consejos/{consejo}/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Usuarios
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    //grtaficas
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
});

    // Asistencia y participacion
    Route::get('/about', fn () => Inertia::render('About'))->name('about');
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

require __DIR__ . '/auth.php';
