<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller
{
    public function dashboard()
    {
        $supplierId = Auth::id();

        // 1. إحصائيات الكروت
        $totalProducts = Product::where('user_id', $supplierId)->count();
        
        $pendingOrders = Order::whereHas('product', function($q) use ($supplierId) {
            $q->where('user_id', $supplierId);
        })->where('status', 'pending')->count();

        $completedOrders = Order::whereHas('product', function($q) use ($supplierId) {
            $q->where('user_id', $supplierId);
        })->where('status', 'delivered')->count();

        $lowStockCount = Product::where('user_id', $supplierId)
            ->where('stock_quantity', '<', 10)->count();

        // 2. جدول آخر الطلبات
        $recentOrders = Order::whereHas('product', function($q) use ($supplierId) {
            $q->where('user_id', $supplierId);
        })->with('product')->latest()->take(5)->get();

        // 3. تنبيهات نقص المخزون
        $lowStockProducts = Product::where('user_id', $supplierId)
            ->where('stock_quantity', '<', 10)->take(5)->get();

        return view('supplier.dashboard', compact(
            'totalProducts', 'pendingOrders', 'completedOrders', 
            'lowStockCount', 'recentOrders', 'lowStockProducts'
        ));
    }

    // Product Management

    public function manageProducts()
    {
        $products = Product::where('user_id', Auth::id())->get();
        return view('supplier.manage-products', compact('products'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'shipping_method' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // تعديل: الحفظ المباشر في مجلد public لضمان الظهور في المشروع التعليمي
            $image->move(public_path('uploads/products'), $imageName);
            $imagePath = 'uploads/products/' . $imageName;
        }

        Product::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'category' => $request->category,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'shipping_method' => $request->shipping_method,
            'image_url' => $imagePath, 
        ]);

        return redirect()->back()->with('success', 'Product Added Successfully!');
    }

    public function updateProduct(Request $request, $id)
    {
        // Suppliers can only edit their own products.
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        // Only validated fields are saved, so user_id / sold_count can't be overwritten from the form.
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'shipping_method' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        unset($data['image']);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($product->image_url && File::exists(public_path($product->image_url))) {
                File::delete(public_path($product->image_url));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image_url'] = 'uploads/products/' . $imageName;
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function deleteProduct($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        // حذف ملف الصورة من المجلد قبل حذف السجل
        if ($product->image_url && File::exists(public_path($product->image_url))) {
            File::delete(public_path($product->image_url));
        }

        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    // Incoming Orders
    public function incomingOrders()
    {
        $supplierId = Auth::id();

        $orders = Order::whereHas('product', function($q) use ($supplierId) {
            $q->where('user_id', $supplierId);
        })->with(['product', 'user'])->latest()->get();

        return view('supplier.incoming-orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $supplierId = Auth::id();
        $ownOrders = fn () => Order::whereHas('product', fn ($q) => $q->where('user_id', $supplierId));

        // Suppliers can only update orders for their own products.
        $order = $ownOrders()->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered'
        ]);

        $order->status = $request->status;
        $order->save();

        // Counters shown on the supplier's page, limited to their own orders.
        return response()->json([
            'success' => true,
            'counts' => collect(['pending', 'processing', 'shipped', 'delivered'])
                ->mapWithKeys(fn ($status) => [$status => $ownOrders()->where('status', $status)->count()]),
        ]);
    }
}