<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductTestimonial;

class FrontService
{
    public function getFrontPageData()
{
    $popularProducts = Product::where('is_popular', 1)->latest()->get();
    $newProducts = Product::latest()->get();
    $productTestimonials = ProductTestimonial::where('is_publish', true)->latest()->take(20)->with('product')->get();

    return compact('popularProducts', 'newProducts', 'productTestimonials');
}
}
