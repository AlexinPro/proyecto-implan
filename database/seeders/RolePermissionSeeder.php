<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        | No se asignan permisos explícitos.
        | Tiene acceso total vía Gate::before
        */
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);

        $superAdmin->syncPermissions([
            //legalidad
            'legalidad.ver',
            'legalidad.validar_reeleccion',
            'legalidad.rechazar_reeleccion',
        ]);


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        $admin = Role::where('name', 'admin')->first();

        $admin->syncPermissions([
            // Lectura
            'consejos.ver',
            'asistencias.ver',
            'legalidad.ver',
            'reportes.ver',

            // Acciones administrativas
            'usuarios.crear',
            'usuarios.editar',
            'convocatorias.crear',
            'asistencias.crear',
            

            // Documentos
            'documentos.subir',
        ]);

        /*
        |--------------------------------------------------------------------------
        | INTEGRANTE
        |--------------------------------------------------------------------------
        */
        $integrante = Role::where('name', 'integrante')->first();

        $integrante->syncPermissions([
            // Lectura
            'consejos.ver',
            'asistencias.ver',
            'legalidad.ver',
            'reportes.ver',

            // Documentos
            'documentos.subir',
        ]);
    }
}
