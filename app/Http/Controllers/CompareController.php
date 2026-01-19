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
     * Удалить один товар из сравнения.
     */
    public function remove(Product $product)
    {
        $compare = session('compare', []);

        $compare = array_values(array_filter($compare, fn($id) => $id !== $product->id));

        session(['compare' => $compare]);

        return back()->with('success', 'Product removed from compare.');
    }



    /**
     * Очистить список сравнения.
     */
    public function clear()
    {
        session()->forget('compare');

        return back()->with('success', 'Compare list cleared.');
    }

    public function add(Product $product)
    {
        $compare = session('compare', []);


        // уже есть в сравнении
        if (in_array($product->id, $compare)) {
            return back()->with('info', 'Product is already in compare list.');
        }

        // максимум 4 товара (можешь поменять число)
        if (count($compare) >= 4) {
            return back()->with('error', 'You can compare up to 4 products.');
        }



        $categoryName = optional($product->category)->name;
        $allowedPcCategories = ['Gaming PCs', 'Workstations', 'Office PCs'];

        if (!in_array($categoryName, $allowedPcCategories)) {
            return back()->with('error', 'Only desktop PCs can be compared.');
        }

        // если в списке уже есть товары — проверяем, что все они тоже из allowedPcCategories
        if (!empty($compare)) {
            $firstProduct = Product::with('category')->find(reset($compare));
            $firstCategoryName = optional($firstProduct->category)->name;

            if (!in_array($firstCategoryName, $allowedPcCategories)) {
                return back()->with('error', 'Compare list already contains non-PC products.');
            }
        }

        $compare[] = $product->id;
        session(['compare' => $compare]);

        return back()->with('success', 'Product added to compare.');
    }
}
