<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Telas Naturales',   'image' => 'telas-naturales.png'],
            ['name' => 'Telas Sintéticas',  'image' => 'telas-sinteticas.png'], //
            ['name' => 'Tapicería y Hogar', 'image' => 'tapiceria-hogar.png'], //
            ['name' => 'Moda y Confección', 'image' => 'moda-confeccion.png'], //
            ['name' => 'Telas Especiales',  'image' => 'telas-especiales.png'],//
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name'       => $category['name'],
                'slug'       => Str::slug($category['name']),
                'image'      => $category['image'],
                'parent_id'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
