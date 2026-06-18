<?php

namespace App\Http\Controllers;
use Inertia\Inertia;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product=Product::latest()->paginate(10);
        return response()->json([
            'success'=>true,
            'message'=> "Product fetched successfully",
            'data'=>$product
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'brand' => 'required|string|max:255',
            'size' => 'required|string',
            'color' => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Product created successfully',
    //     'data'    => $product
    // ], 201);
        return redirect('/inventory'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function inventoryPage()
    {
        $products = Product::latest()->paginate(10);
        return Inertia::render('inventoryPage', [
            'products' => $products
        ]);
    }
    public function create()
    {
        return Inertia::render('AddProduct');
    }
}
