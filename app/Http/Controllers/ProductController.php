<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::latest();

        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        $limit = $request->get('limit', 8);
        $products = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => "Product fetched successfully",
            'data'    => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'required|string|max:2000',
            'price'          => 'required|numeric|min:0',
            'category'       => 'required|string',
            'brand'          => 'required|string|max:255',
            'size'           => 'required|string',
            'color'          => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

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
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data'    => null,
            ], 404);
        }

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'description'    => 'sometimes|required|string|max:2000',
            'price'          => 'sometimes|required|numeric|min:0',
            'category'       => 'sometimes|required|string',
            'brand'          => 'sometimes|required|string|max:255',
            'size'           => 'sometimes|required|string',
            'color'          => 'sometimes|required|string|max:255',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            // No new file picked -> don't touch the existing image
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect('/inventory');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'data'    => null,
            ], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'data'    => null,
        ]);
    }

    /**
     * Admin: Inventory listing page
     */
    public function inventoryPage()
    {
        $products = Product::latest()->paginate(10);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products
        ]);
    }

    /**
     * Admin: Add product form
     */
    public function create()
    {
        return Inertia::render('Admin/Products/Form');
    }

    /**
     * Admin: Edit product form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
        ]);
    }

    /**
     * Store front listing page
     */
    public function storePage()
    {
        return Inertia::render('Store');
    }

    /**
     * Home page
     */
    public function homePage()
    {
        return Inertia::render('Home');
    }

    /**
     * Product detail page (store front)
     */
    public function productDetailPage($id)
    {
        $product = Product::findOrFail($id);

        return Inertia::render('ProductDetail', [
            'product' => $product
        ]);
    }
}