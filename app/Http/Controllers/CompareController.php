<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{

    public function index()
    {
        // zoberieme ID produktov z session
        $ids = session('compare', []);

        // nacitame produkty podla ID zo session
        $products = Product::with('category')
            ->whereIn('id', $ids)
            ->get();

        return view('compare.index', compact('products'));
    }

    public function add(Request $request, Product $product)
    {
        // aktualny stav porovnania zo session
        $compare = session('compare', []);

        // uz je v porovnani
        if (in_array($product->id, $compare)) {
            $message = 'Product is already in compare list.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'count'   => count($compare),
                ]);
            }

            return back()->with('info', $message);
        }

        // maximum 4 produkty
        if (count($compare) >= 4) {
            $message = 'You can compare up to 4 products.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'count'   => count($compare),
                ]);
            }

            return back()->with('error', $message);
        }

        // povolene len PC kategorie
        $categoryName        = optional($product->category)->name;
        $allowedPcCategories = ['Gaming PCs', 'Workstations', 'Office PCs'];

        if (!in_array($categoryName, $allowedPcCategories)) {
            $message = 'Only desktop PCs can be compared.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'count'   => count($compare),
                ]);
            }

            return back()->with('error', $message);
        }

        // ak uz nieco v porovnani je, skontrolujeme prvy produkt
        if (!empty($compare)) {
            $firstProduct      = Product::with('category')->find(reset($compare));
            $firstCategoryName = optional($firstProduct->category)->name;

            if (!in_array($firstCategoryName, $allowedPcCategories)) {
                $message = 'Compare list already contains non-PC products.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'count'   => count($compare),
                    ]);
                }

                return back()->with('error', $message);
            }
        }

        // vsetko ok, pridame ID produktu do session
        $compare[] = $product->id;
        session(['compare' => $compare]);

        $message = 'Product added to compare.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count'   => count($compare),
            ]);
        }

        return back()->with('success', $message);
    }

    public function remove(Request $request, Product $product)
    {
        $compare = session('compare', []);

        // odfiltrujeme ID odstraneneho produktu
        $compare = array_values(
            array_filter($compare, fn($id) => (int)$id !== (int)$product->id)
        );

        session(['compare' => $compare]);

        $message = 'Product removed from compare.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count'   => count($compare),
            ]);
        }

        return back()->with('success', $message);
    }

    public function clear(Request $request)
    {
        // uplne vymazeme kluc compare zo session
        session()->forget('compare');

        $message = 'Compare list cleared.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count'   => 0,
            ]);
        }

        return back()->with('success', $message);
    }


    public function count()
    {
        $ids = session('compare', []);

        return response()->json([
            'count' => is_array($ids) ? count($ids) : 0,
        ]);
    }
}
