<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Textiles Aurora',    'image' => 'textiles-aurora.png'],//
            ['name' => 'Hilaturas del Sol',  'image' => 'hilaturas-sol.png'], //
            ['name' => 'Fibras Andinas',     'image' => 'fibras-andinas.png'],//
            ['name' => 'TelaMundo',          'image' => 'telamundo.png'],//
            ['name' => 'Casa del Algodón',   'image' => 'casa-algodon.png'], //
            ['name' => 'Lino Fino',          'image' => 'lino-fino.png'],//
            ['name' => 'Seda Imperial',      'image' => 'seda-imperial.png'],
            ['name' => 'PoliTex',            'image' => 'politex.png'],
            ['name' => 'VelvetHouse',        'image' => 'velvethouse.png'],
            ['name' => 'EcoFibras',          'image' => 'ecofibras.png'],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name'       => $brand['name'],
                'slug'       => Str::slug($brand['name']),
                'image'      => $brand['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
