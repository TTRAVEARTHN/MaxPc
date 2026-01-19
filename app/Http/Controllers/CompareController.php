<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Список товаров для сравнения.
     */
    public function index()
    {
        $ids = session('compare', []);

        $products = Product::with('category')
            ->whereIn('id', $ids)
            ->get();

        return view('compare.index', compact('products'));
    }

    /**
     * Добавить товар в сравнение.
     */
    public function add(Request $request, Product $product)
    {
        $compare = session('compare', []);

        // уже есть в сравнении
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

        // максимум 4 товара
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

        // разрешаем только ПК-категории
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

        // если в списке уже есть товары — проверяем, что первый тоже из списка разрешённых
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

        // всё ок — добавляем

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

    /**
     * Удалить один товар из сравнения.
     */
    public function remove(Request $request, Product $product)
    {
        $compare = session('compare', []);

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

    /**
     * Очистить список сравнения.
     */
    public function clear(Request $request)
    {
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

    /**
     * Вернуть только количество товаров в сравнении (для бейджика и pageshow).
     */
    public function count()
    {
        $ids = session('compare', []);

        return response()->json([
            'count' => is_array($ids) ? count($ids) : 0,
        ]);
    }
}
