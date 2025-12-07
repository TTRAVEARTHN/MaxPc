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
            'specs'       => 'nullable|string', // accept JSON string from form
        ]);

        // decode specs JSON to array
        if (!empty($data['specs'])) {
            $decoded = json_decode($data['specs'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['specs' => 'Invalid JSON in Specifications'])->withInput();
            }
            $data['specs'] = $decoded;
        } else {
            $data['specs'] = null;
        }

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
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
    // File: app/Http/Controllers/AdminProductController.php (update method only)
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'main_image'  => 'nullable|image|max:2048',
            'specs'       => 'nullable|string', // accept JSON string from form
        ]);

        // decode specs JSON to array
        if (!empty($data['specs'])) {
            $decoded = json_decode($data['specs'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['specs' => 'Invalid JSON in Specifications'])->withInput();
            }
            $data['specs'] = $decoded;
        } else {
            $data['specs'] = null;
        }

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products')
            ->with('success', 'Product updated successfully.');
    }


    /**
     * Delete item
     */
    public function delete(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products')
            ->with('success', 'Product deleted.');
    }
}
