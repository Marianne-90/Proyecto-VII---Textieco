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
        $images = range('a', 'u');
        $images = array_map(fn($ch) => $ch . '.png', $images);
        shuffle($images);

        $tiposTela = [
            'Algodón','Lino','Seda','Lana','Poliéster','Nylon','Rayón','Denim','Franela','Terciopelo',
            'Loneta','Jacquard','Chiffón','Organza','Crepé','Acrílico','Spandex','Softshell','Brocado','Damasco'
        ];

        $adjetivos = ['Premium','Deluxe','Suave','Ligera','Eco','Plus','Pro','Clásica','Compact','Ultra'];

        // ===== Bloques para descripciones aleatorias =====
        $origenes = ['Perú','España','Italia','India','Japón','México','Turquía','Marruecos','Colombia','Portugal','Vietnam','Tailandia','China','Francia','Brasil'];
        $usos = ['blusas','vestidos','camisas','trajes','tapicería','cojines','cortinas','manualidades','uniformes','ropa infantil','lencería','chaquetas','pijamas','faldas','mantelería'];
        $cualidades = ['increíblemente suave','ligera','transpirable','muy resistente','elástica','con caída fluida','térmica','antipilling','hipoalergénica','de secado rápido','con tacto sedoso','mate','con brillo sutil'];
        $acabados = ['acabado satinado','textura peinada','tejido cerrado','tejido abierto','microfibra','tejido twill','efecto arrugado','acabado stone-wash','tejido acanalado','acabado mercerizado'];
        $cuidados = [
            'lavar a máquina en frío', 'lavar a mano', 'no usar lejía',
            'planchar a baja temperatura', 'no secar en secadora', 'secar a la sombra'
        ];
        $promos = [
            'Edición limitada.',
            'Producción responsable.',
            'Compra mínima de 0,5 m.',
            'Stock sujeto a disponibilidad.',
            'Hecha sin crueldad animal.'
        ];

        // Composiciones probables según tipo
        $composiciones = [
            'Algodón'    => ['100% algodón','98% algodón · 2% spandex','60% algodón · 40% poliéster'],
            'Lino'       => ['100% lino','55% lino · 45% algodón'],
            'Seda'       => ['100% seda','70% seda · 30% algodón'],
            'Lana'       => ['100% lana','80% lana · 20% nylon'],
            'Poliéster'  => ['100% poliéster','95% poliéster · 5% spandex'],
            'Nylon'      => ['100% nylon','92% nylon · 8% spandex'],
            'Rayón'      => ['100% rayón','70% rayón · 30% poliéster'],
            'Denim'      => ['98% algodón · 2% elastano','100% algodón'],
            'Franela'    => ['100% algodón','65% poliéster · 35% algodón'],
            'Terciopelo' => ['100% poliéster','80% algodón · 20% poliéster'],
            // Por defecto
            '*'          => ['Combinación de fibras seleccionadas']
        ];

        // Helper: crea short y long description para una tela base
        $makeDescription = function (string $base) use ($faker, $origenes, $usos, $cualidades, $acabados, $cuidados, $promos, $composiciones) {
            $origen   = $faker->randomElement($origenes);
            $c1       = $faker->randomElement($cualidades);
            // evitar repetir la misma cualidad
            $c2       = $faker->randomElement(array_values(array_diff($cualidades, [$c1])));
            $acabado  = $faker->randomElement($acabados);

            // 2-3 usos distintos
            $usosSel  = $faker->randomElements($usos, $faker->numberBetween(2, 3));
            $usoTxt   = implode(', ', array_slice($usosSel, 0, -1)) . (count($usosSel) > 1 ? ' y ' . end($usosSel) : $usosSel[0]);

            // 2 cuidados distintos
            $cuidSel  = $faker->randomElements($cuidados, 2);
            $cuidado1 = $cuidSel[0];
            $cuidado2 = $cuidSel[1];

            // gramaje/ancho realistas
            $gramaje  = $faker->numberBetween(80, 380);  // g/m²
            $ancho    = $faker->numberBetween(135, 160); // cm

            // composición por tipo
            $compList = $composiciones[$base] ?? $composiciones['*'];
            $comp     = $faker->randomElement($compList);

            // short description tipo tagline
            $short = ucfirst($c1) . " · {$base} · Origen: {$origen}";

            // párrafos concatenados
            $sentences = [
                "Esta tela de {$base} es {$c1} y {$c2}.",
                "Originaria de {$origen}, presenta {$acabado} y un look premium.",
                "Ideal para {$usoTxt}.",
                "Composición: {$comp}. Gramaje aprox. {$gramaje} g/m² y ancho {$ancho} cm.",
                "Cuidado: {$cuidado1}; {$cuidado2}.",
            ];

            // 60% de las veces agrega una frase promocional
            if ($faker->boolean(60)) {
                $sentences[] = $faker->randomElement($promos);
            }

            $long = implode(' ', $sentences);

            return [$short, $long];
        };

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

            // Generar descripciones aleatorias coherentes
            [$short, $long] = $makeDescription($base);

            $rows[] = [
                'name'              => $name,
                'slug'              => $slug,
                'short_description' => $short,
                'description'       => $long,
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
