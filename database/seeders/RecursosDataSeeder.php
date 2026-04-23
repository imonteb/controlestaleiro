<?php

namespace Database\Seeders;

use App\Models\Colaborador;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class RecursosDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear algunos colaboradores
        $colaboradores = [
            ['numero_colaborador' => 'CME-001', 'nombre' => 'João', 'apellido' => 'Silva', 'telefono' => '912345678', 'denominacion_cargo' => 'Técnico AT'],
            ['numero_colaborador' => 'CME-002', 'nombre' => 'Manuel', 'apellido' => 'Oliveira', 'telefono' => '912345679', 'denominacion_cargo' => 'Técnico BT'],
            ['numero_colaborador' => 'CME-003', 'nombre' => 'Carlos', 'apellido' => 'Pereira', 'telefono' => '912345680', 'denominacion_cargo' => 'Especialista Fibra'],
            ['numero_colaborador' => 'CME-004', 'nombre' => 'Rui', 'apellido' => 'Santos', 'telefono' => '912345681', 'denominacion_cargo' => 'Jefe de Equipo'],
            ['numero_colaborador' => 'CME-005', 'nombre' => 'André', 'apellido' => 'Costa', 'telefono' => '912345682', 'denominacion_cargo' => 'Auxiliar'],
        ];

        foreach ($colaboradores as $colaborador) {
            Colaborador::firstOrCreate(
                ['numero_colaborador' => $colaborador['numero_colaborador']],
                $colaborador
            );
        }

        // Crear algunos vehículos
        $vehiculos = [
            ['marca' => 'Toyota', 'modelo' => 'Hilux', 'matricula' => 'AB-12-CD'],
            ['marca' => 'Ford', 'modelo' => 'Ranger', 'matricula' => 'EF-34-GH'],
            ['marca' => 'Renault', 'modelo' => 'Transit', 'matricula' => 'IJ-56-KL'],
            ['marca' => 'Peugeot', 'modelo' => 'Kangoo', 'matricula' => 'MN-78-OP'],
        ];

        foreach ($vehiculos as $vehiculo) {
            Vehiculo::firstOrCreate(
                ['matricula' => $vehiculo['matricula']],
                $vehiculo
            );
        }
    }
}
