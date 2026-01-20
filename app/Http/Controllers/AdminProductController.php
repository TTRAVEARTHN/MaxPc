<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    /**
     * List all products
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Product::with('category')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.products.products', compact('products'));
    }

    /**
     * Show form to create product
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store product in DB
     */
    public function store(Request $request)
    {
        $data = $request->validate([
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

        // собрать характеристики в ассоциативный массив
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

        // картинка
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Edit page
     */
    public function edit(Product $product)
    {
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
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete item
     */
    public function delete(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product deleted.');
    }
}
