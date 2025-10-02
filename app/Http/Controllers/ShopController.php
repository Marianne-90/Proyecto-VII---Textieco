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
        // Sanitiza entradas
        $size = (int) $request->query('size', 12);

        // Mapea order a columnas/dir válidos (whitelist)
        $orderMap = [
            1 => ['created_at', 'asc'],
            2 => ['created_at', 'desc'],
            3 => ['regular_price', 'asc'],
            4 => ['regular_price', 'desc'],
        ];
        [$o_column, $o_order] = $orderMap[(int)$request->query('order', -1)] ?? ['id', 'desc'];

        // Normaliza filtros: string "1,2,3" -> [1,2,3]; elimina vacíos; castea a int
        $f_brands = collect(explode(',', (string) $request->query('brands', '')))
            ->filter(fn ($v) => $v !== '' && $v !== null)
            ->map(fn ($v) => (int) $v)
            ->values();

        $f_categories = collect(explode(',', (string) $request->query('categories', '')))
            ->filter(fn ($v) => $v !== '' && $v !== null)
            ->map(fn ($v) => (int) $v)
            ->values();

        $products = Product::query()
            ->when($f_brands->isNotEmpty(), fn ($q) => $q->whereIn('brand_id', $f_brands))
            ->when($f_categories->isNotEmpty(), fn ($q) => $q->whereIn('category_id', $f_categories))
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands     = Brand::orderBy('name', 'ASC')->get();

        // Para que el front recuerde qué filtros usó:
        $order = (int)$request->query('order', -1);
        return view('shop', [
            'products'      => $products,
            'categories'    => $categories,
            'brands'        => $brands,
            'size'          => $size,
            'order'         => $order,
            'f_brands'      => $f_brands->implode(','),     // o pasa el array si tu vista lo espera así
            'f_categories'  => $f_categories->implode(','),
        ]);
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();

        $rproducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4) // evita traer todo y luego ->take(4)
            ->get();

        return view('details', compact('product', 'rproducts'));
    }
}
