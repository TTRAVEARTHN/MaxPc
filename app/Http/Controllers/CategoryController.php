<?php
//
//namespace App\Http\Controllers;
//
//use Illuminate\Http\Request;
//use App\Models\Category;
//
//class CategoryController extends Controller
//{
//    /**
//     * Display list of categories (ADMIN)
//     */
//    public function index()
//    {
//        $categories = Category::orderBy('name')->paginate(20);
//
//        return view('admin.categories.index', compact('categories'));
//    }
//
//    /**
//     * Show create form (ADMIN)
//     */
//    public function create()
//    {
//        return view('admin.categories.create');
//    }
//
//    /**
//     * Store new category (ADMIN)
//     */
//    public function store(Request $request)
//    {
//        $validated = $request->validate([
//            'name'        => 'required|string|max:100',
//            'description' => 'nullable|string',
//        ]);
//
//        Category::create($validated);
//
//        return redirect()
//            ->route('admin.categories.index')
//            ->with('success', 'Category created successfully.');
//    }
//
//    /**
//     * Show a single category (not necessary, but included)
//     */
//    public function show(Category $category)
//    {
//        return view('admin.categories.show', compact('category'));
//    }
//
//    /**
//     * Show edit form (ADMIN)
//     */
//    public function edit(Category $category)
//    {
//        return view('admin.categories.edit', compact('category'));
//    }
//
//    /**
//     * Update existing category (ADMIN)
//     */
//    public function update(Request $request, Category $category)
//    {
//        $validated = $request->validate([
//            'name'        => 'required|string|max:100',
//            'description' => 'nullable|string',
//        ]);
//
//        $category->update($validated);
//
//        return redirect()
//            ->route('admin.categories.index')
//            ->with('success', 'Category updated successfully.');
//    }
//
//    /**
//     * Delete category (ADMIN)
//     */
//    public function destroy(Category $category)
//    {
//        $category->delete();
//
//        return redirect()
//            ->route('admin.categories.index')
//            ->with('success', 'Category deleted successfully.');
//    }
//}
