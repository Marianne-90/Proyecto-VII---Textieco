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
<<<<<<< HEAD
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
=======
        $size = $request->query('size', 12); // Default to 12 if 'size' is not provided
        $o_column = "";
        $o_order = "";
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min', 1);
        $max_price = $request->query('max', 2000);
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'asc';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'desc';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order = 'asc';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order = 'desc';
                break;
            default:
                $o_column = 'id';
                $o_order = 'desc';
                break;
        }

        $products = Product::
        where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "'=''");
        })
        ->where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "'=''");
        })
        ->where(function ($query) use ($min_price, $max_price) {
            $query->whereBetween('regular_price', [$min_price, $max_price])
            ->orWhereBetween('sale_price', [$min_price, $max_price]);
        })
        ->orderBy($o_column, $o_order)->paginate($size);
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        return view('shop', compact('products', 'categories', 'brands', 'size', 'order', 'f_brands', 'f_categories', 'min_price', 'max_price'));
>>>>>>> origin/main
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
