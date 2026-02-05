<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            //Asistencias
            'asistencias.ver',
            'asistencias.crear',

            // Usuarios
            'usuarios.crear',
            'usuarios.editar',

            //consejos
            'consejos.ver',

            //legalidad
            'legalidad.ver',
            'legalidad.solicitar_reeleccion',
            'legalidad.validar_reeleccion',
            'legalidad.rechazar_reeleccion',

            //reportes
            'reportes.ver',

            // Convocatorias
            'convocatorias.crear',

            // Documentos
            'documentos.subir',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
