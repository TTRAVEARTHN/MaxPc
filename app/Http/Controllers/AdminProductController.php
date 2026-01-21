<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{

    public function index(Request $request)
    {
        // jednoduche fulltext vyhladavanie podla nazvu
        $search = $request->query('search');

        $products = Product::with('category')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            // strankovanie po 20 kuskov
            ->paginate(5)
            ->withQueryString();

        // posielame produkty a aktualny search text do admin view
        return view('admin.products.products', compact('products', 'search'));
    }


    public function create()
    {
        // nacitame kategorie do selectu
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // kontrola ze kategoria existuje v tabulke categories
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'main_image'  => 'nullable|image|max:2048',

            // specs: массив строк "key/value"
            'specs'           => 'nullable|array',
            'specs.*.key'     => 'nullable|string|max:255',
            'specs.*.value'   => 'nullable|string|max:255',
        ]);

        // preklopenie specs z pola key/value na asociativne pole
        $specs = [];
        if (!empty($data['specs']) && is_array($data['specs'])) {
            foreach ($data['specs'] as $row) {
                $key   = isset($row['key'])   ? trim($row['key'])   : '';
                $value = isset($row['value']) ? trim($row['value']) : '';

                if ($key !== '' && $value !== '') {
                    $specs[$key] = $value;
                }
            }
        }
        // ak nie su specs, ulozime null aby to nebol prazdny array
        $data['specs'] = $specs ?: null;
        // upload hlavneho obrazku
        if ($request->hasFile('main_image')) {
            // ulozenie do storage/app/public/products
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        // vytvorenie noveho produktu z validovanych dat
        Product::create($data);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // kategorie do selectu pri editacii
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'main_image'  => 'nullable|image|max:2048',

            'specs'           => 'nullable|array',
            'specs.*.key'     => 'nullable|string|max:255',
            'specs.*.value'   => 'nullable|string|max:255',
        ]);

        // rovnaka logika spracovania specs ako pri create
        $specs = [];
        if (!empty($data['specs']) && is_array($data['specs'])) {
            foreach ($data['specs'] as $row) {
                $key   = isset($row['key'])   ? trim($row['key'])   : '';
                $value = isset($row['value']) ? trim($row['value']) : '';

                if ($key !== '' && $value !== '') {
                    $specs[$key] = $value;
                }
            }
        }
        $data['specs'] = $specs ?: null;

        if ($request->hasFile('main_image')) {
            // pri update prepiseme cestu k obrazku na novu
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        // hromadny update existujuceho produktu
        $product->update($data);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product updated successfully.');
    }


    public function delete(Product $product)
    {
        // jednoduche delete, bez riesenia suboru z disku
        $product->delete();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product deleted.');
    }
}
