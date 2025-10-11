<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlidesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('slides')->insert([
            [
                'tagline' => 'Moda',
                'title' => 'A conciencia',
                'subtitle' => 'Descubre telas y comercio justo',
                'link' => 'https://proyecto-vii-textieco.onrender.com/shop?page=1&size=12&order=-1&brands=10&categories=&min=1&max=2000',
                'image' => 'ecofibra.png',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tagline' => 'Nuevo',
                'title' => 'Elegancia natural',
                'subtitle' => 'Fibras orgánicas',
                'link' => 'https://proyecto-vii-textieco.onrender.com/shop?page=1&size=12&order=-1&brands=&categories=1&min=1&max=2000',
                'image' => 'telas.png',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
