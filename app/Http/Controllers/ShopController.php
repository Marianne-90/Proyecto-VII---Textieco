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
        $size = $request->query('size', 12); // Default to 12 if 'size' is not provided
        $o_column = "";
        $o_order = "";
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands');

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

        $products = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "'=''");
        })->orderBy($o_column, $o_order)->paginate($size);
        $categories = Category::orderBy('id', 'DESC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        return view('shop', compact('products', 'categories', 'brands', 'size', 'order', 'f_brands'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->get()->take(4);
        return view('details', compact('product', 'rproducts'));
    }

}
