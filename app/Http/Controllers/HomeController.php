<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */ /**
       * Show the application dashboard.
       *
       * @return \Illuminate\Contracts\Support\Renderable
       */
    public function index()
    {
        $slides = Slide::where('status', 1)->get()->take(3);
        $sProducts = Product::whereNotNull('sale_price')
            ->where('sale_price', '<>', 0)
            ->whereColumn('sale_price', '<>', 'regular_price')
            ->inRandomOrder()
            ->get()
            ->take(8);
        return view('index', compact('slides', 'sProducts'));
    }
}
