<?php

namespace Database\Seeders;

use App\Models\AcademicArea;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApunteShareCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Ingeniería y Tecnología', 'Ciencias Sociales', 'Medicina y Salud', 'Derecho y Leyes', 'Economía y Negocios', 'Arquitectura'] as $name) {
            AcademicArea::firstOrCreate(['name' => $name], ['slug' => Str::slug($name), 'is_active' => true]);
        }
        foreach (['Apuntes', 'Guía', 'Resumen', 'Ensayo', 'Presentación'] as $name) {
            DocumentType::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
