<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Product;
//use App\Models\ProductImage;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Storage;
//
//class ProductImageController extends Controller
//{
//
//    public function index($productId)
//    {
//        $product = Product::with('images')->findOrFail($productId);
//
//        return view('product.images', compact('product'));
//    }
//
//    /**
//     * Upload and attach image to product
//     */
//    public function store(Request $request, $productId)
//    {
//        $product = Product::findOrFail($productId);
//
//        // Validation
//        $request->validate([
//            'image' => 'required|image|max:4096', // 4MB limit
//        ]);
//
//        // store file
//        $path = $request->file('image')->store('products', 'public');
//
//        // save database record
//        ProductImage::create([
//            'product_id' => $product->id,
//            'image_path' => '/storage/' . $path,
//        ]);
//
//        return back()->with('success', 'Image uploaded!');
//    }
//
//    /**
//     * Delete product image
//     */
//    public function destroy($imageId)
//    {
//        $image = ProductImage::findOrFail($imageId);
//
//        // delete physical file
//        if (Storage::disk('public')->exists(str_replace('/storage/', '', $image->image_path))) {
//            Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_path));
//        }
//
//        // delete DB row
//        $image->delete();
//
//        return back()->with('success', 'Image removed.');
//    }
//}
//
//
