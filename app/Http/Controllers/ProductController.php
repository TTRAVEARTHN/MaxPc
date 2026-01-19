<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Главная страница каталога (для пользователей)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category') && $request->category !== null) {
            $query->where('category_id', $request->category);
        }


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

        $products   = $query->paginate(12);
        $categories = Category::all();


        if ($request->boolean('ajax')) {
            $html = view('partials.catalog_grid', compact('products'))->render();

            return response()->json([
                'html'  => $html,
                'total' => $products->total(),
            ]);
        }

        // Обычное HTML при прямом заходе /catalog?category=4
        return view('catalog', compact('products', 'categories'));

    }


    /**
     * Страница одного товара
     */
    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }


    /* ===========================
       А Д М И Н К А
       =========================== */

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'specs'       => 'nullable|array',
        ]);

        Product::create($data);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product'    => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'specs'       => 'nullable|array',
        ]);

        $product->update($data);

        return redirect()->route('admin.products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index');
    }
}
