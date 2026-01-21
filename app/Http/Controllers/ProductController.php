<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // hlavna stranka katalogu pre uzivatelov
    public function index(Request $request)
    {
        // pripraveny query builder s loadingom kategorie
        $query = Product::with('category');

        if ($request->has('category') && $request->category !== null) {
            // filtrovanie produktov podla zvolenej kategorie
            $query->where('category_id', $request->category);
        }


        // triedenie podla parametra sort z requestu
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc');
        }

        // strankovanie po 12 produktov
        $products = $query->paginate(12);
        // zoznam vsetkych kategorii pre filter
        $categories = Category::all();


        // odpoved pre AJAX nacitavanie katalogu
        if ($request->boolean('ajax')) {
            $html = view('partials.catalog_grid', compact('products'))->render();

            return response()->json([
                'html' => $html,
                'total' => $products->total(),
            ]);
        }

        return view('catalog', compact('products', 'categories'));

    }

    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }
}


//    public function create()
//    {
//        return view('admin.products.create', [
//            'categories' => Category::all()
//        ]);
//    }
//
//    public function store(Request $request)
//    {
//        $data = $request->validate([
//            'category_id' => 'nullable|exists:categories,id',
//            'name'        => 'required|string|max:150',
//            'price'       => 'required|numeric|min:0',
//            'description' => 'nullable|string',
//            'specs'       => 'nullable|array',
//        ]);
//
//        Product::create($data);
//
//        return redirect()->route('admin.products.index');
//    }
//
//    public function edit(Product $product)
//    {
//        return view('admin.products.edit', [
//            'product'    => $product,
//            'categories' => Category::all(),
//        ]);
//    }
//
//    public function update(Request $request, Product $product)
//    {
//        $data = $request->validate([
//            'category_id' => 'nullable|exists:categories,id',
//            'name'        => 'required|string|max:150',
//            'price'       => 'required|numeric|min:0',
//            'description' => 'nullable|string',
//            'specs'       => 'nullable|array',
//        ]);
//
//        $product->update($data);
//
//        return redirect()->route('admin.products.index');
//    }
//
//    public function destroy(Product $product)
//    {
//        $product->delete();
//
//        return redirect()->route('admin.products.index');
//    }
//}
