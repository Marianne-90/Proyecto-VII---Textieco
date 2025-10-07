<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // --- Inputs con defaults y saneados ---
        $size = (int) $request->integer('size', 12);
        if ($size <= 0 || $size > 100) $size = 12;

        $order = (int) $request->integer('order', -1);

        // Normaliza "1,2,3" -> [1,2,3] y quita vacíos/no numéricos
        $brandsParam = (string) $request->query('brands', '');
        $categoriesParam = (string) $request->query('categories', '');

        $brandIds = array_values(array_filter(array_map(
            fn ($v) => is_numeric($v) ? (int) $v : null,
            array_map('trim', $brandsParam === '' ? [] : explode(',', $brandsParam))
        ), fn ($v) => $v !== null));

        $categoryIds = array_values(array_filter(array_map(
            fn ($v) => is_numeric($v) ? (int) $v : null,
            array_map('trim', $categoriesParam === '' ? [] : explode(',', $categoriesParam))
        ), fn ($v) => $v !== null));

        $min_price = (float) $request->query('min', 1);
        $max_price = (float) $request->query('max', 2000);
        if ($min_price < 0) $min_price = 0;
        if ($max_price < $min_price) $max_price = $min_price;

        // --- Orden seguro (whitelist) ---
        // 1: created_at asc, 2: created_at desc, 3: regular_price asc, 4: regular_price desc, default: id desc
        [$o_column, $o_order] = match ($order) {
            1 => ['created_at', 'asc'],
            2 => ['created_at', 'desc'],
            3 => ['regular_price', 'asc'],
            4 => ['regular_price', 'desc'],
            default => ['id', 'desc'],
        };

        // --- Query sin SQL crudo, portable a PostgreSQL ---
        $products = Product::query()
            // Aplica marcas sólo si hay IDs
            ->when(!empty($brandIds), fn ($q) => $q->whereIn('brand_id', $brandIds))
            // Aplica categorías sólo si hay IDs
            ->when(!empty($categoryIds), fn ($q) => $q->whereIn('category_id', $categoryIds))
            // Rango de precio: entra si regular_price o sale_price caen dentro del rango
            ->where(function ($q) use ($min_price, $max_price) {
                $q->whereBetween('regular_price', [$min_price, $max_price])
                  ->orWhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        // Pasa los valores originales para mantener filtros en la vista
        return view('shop', [
            'products'      => $products,
            'categories'    => $categories,
            'brands'        => $brands,
            'size'          => $size,
            'order'         => $order,
            'f_brands'      => $brandsParam,
            'f_categories'  => $categoriesParam,
            'min_price'     => $min_price,
            'max_price'     => $max_price,
        ]);
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();

        $rproducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('details', compact('product', 'rproducts'));
    }
}
