<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()  {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::orderBy('id', 'DESC')->get();
        $brands = Brand::orderBy('id', 'DESC')->get();
        return view('shop', compact('products', 'categories', 'brands'));
    }

public function product_details($product_slug)
{
    $product = Product::where('slug', $product_slug)->first();
    $rproducts = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->get()->take(8);
    return view('details', compact('product', 'rproducts'));
}

}
