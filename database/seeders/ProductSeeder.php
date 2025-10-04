<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        // 1) Obtener IDs existentes
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $brandIds    = DB::table('brands')->pluck('id')->all();

        if (empty($categoryIds) || empty($brandIds)) {
            $this->command->warn('No hay categorías o marcas sembradas. Ejecuta BrandSeeder y CategorySeeder primero.');
            return;
        }

        // Lista de nombres de imágenes locales (a.png hasta u.png)
        $images = range('a', 'u'); // letras de a a u
        $images = array_map(fn($ch) => $ch . '.png', $images);
        shuffle($images); // las mezclamos para que sean aleatorias

        $tiposTela = [
            'Algodón','Lino','Seda','Lana','Poliéster','Nylon','Rayón','Denim','Franela','Terciopelo',
            'Loneta','Jacquard','Chiffón','Organza','Crepé','Acrílico','Spandex','Softshell','Brocado','Damasco'
        ];

        $adjetivos = ['Premium','Deluxe','Suave','Ligera','Eco','Plus','Pro','Clásica','Compact','Ultra'];

        $rows = [];
        $now = now();

        for ($i = 0; $i < 20; $i++) {
            $base = $faker->randomElement($tiposTela);
            $adj  = $faker->randomElement($adjetivos);

            $name = "{$base} {$adj}";
            $slug = Str::slug($name) . '-' . Str::lower(Str::random(6));

            // Precio regular
            $regular = $faker->randomFloat(2, 20, 100);

            // Oferta opcional (10% a 50%)
            $hasSale  = $faker->boolean(35);
            $discount = $faker->numberBetween(10, 50);
            $sale     = $hasSale ? round($regular * (1 - $discount / 100), 2) : $regular;

            // Stock
            $qty   = $faker->numberBetween(0, 200);
            $stock = $qty > 0 ? 'instock' : 'outofstock';

            // SKU único
            $sku = strtoupper(Str::slug(substr($base, 0, 3), '')) . '-' . strtoupper(Str::random(8));

            // Relación con categorías y marcas
            $categoryId = $faker->randomElement($categoryIds);
            $brandId    = $faker->randomElement($brandIds);

            // Asignar imagen única
            $imageMain = $images[$i];

            $rows[] = [
                'name'              => $name,
                'slug'              => $slug,
                'short_description' => $faker->sentence(10),
                'description'       => $faker->paragraphs(2, true),
                'regular_price'     => $regular,
                'sale_price'        => $sale,
                'SKU'               => $sku,
                'stock_status'      => $stock,
                'featured'          => $faker->boolean(10),
                'quantity'          => $qty,
                'image'             => $imageMain, // solo esta
                'images'            => null,       // siempre null
                'category_id'       => $categoryId,
                'brand_id'          => $brandId,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        DB::table('products')->insert($rows);
    }
}
