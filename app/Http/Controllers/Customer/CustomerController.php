<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => \App\Models\Order::where('user_id', $user->id)->count(),
            'delivered'    => \App\Models\Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'processing'   => \App\Models\Order::where('user_id', $user->id)->where('status', 'processing')->count(),
            'wishlist'     => 0, 
        ];

        $recentOrders = \App\Models\Order::where('user_id', $user->id)
                        ->latest()
                        ->take(5)
                        ->get();

        $recommendedProducts = \App\Models\Product::latest()->take(4)->get();

        return view('customer.dashboard', compact('stats', 'recentOrders', 'recommendedProducts'));
    }

    public function showOrder($id)
    {
        // Customers can only view their own orders.
        $order = Order::with('product')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($order);
    }

    public function orders(Request $request)
    {
        $status = $request->query('status'); 
        $user = Auth::user();

        $orders = \App\Models\Order::where('user_id', $user->id)
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->with('product') 
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    public function products(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('shipping_method')) {
            $query->whereJsonContains('shipping_method', $request->shipping_method);
        }

        if ($request->filled('supplier_id')) {
            $query->where('user_id', $request->supplier_id);
        }

        if ($request->sort == 'low_high') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'high_low') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->where('stock_quantity', '>', 0)->paginate(12);

        return view('customer.products', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Lock the product row so two customers can't buy the last units at the same time.
        $placed = DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            if ($product->stock_quantity < $request->quantity) {
                return false;
            }

            Order::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'total_price' => $product->price * $request->quantity,
                'status' => 'processing',
            ]);

            $product->decrement('stock_quantity', $request->quantity);
            $product->increment('sold_count', $request->quantity); // feeds the "top products" report

            return true;
        });

        if (! $placed) {
            return back()->with('error', 'Sorry, this quantity is not available.');
        }

        return back()->with('success', 'Product added to your orders successfully!');
    }

    /**
     * إلغاء الطلب من قبل العميل
     */
    public function cancelOrder($id)
    {
        // البحث عن الطلب والتأكد من أنه يخص المستخدم الحالي
        $order = Order::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        // يسمح بالإلغاء فقط إذا كان الطلب في حالة الانتظار أو المعالجة
        if ($order->status == 'pending' || $order->status == 'processing') {
            
            // إعادة الكمية للمخزن عند الإلغاء
            $product = Product::find($order->product_id);
            if ($product) {
                $product->increment('stock_quantity', $order->quantity);
                $product->decrement('sold_count', min($order->quantity, $product->sold_count));
            }

            // تحديث حالة الطلب
            $order->update(['status' => 'cancelled']);

            return back()->with('success', 'Order #ORD-' . str_pad($order->id, 3, '0', STR_PAD_LEFT) . ' has been cancelled.');
        }

        return back()->with('error', 'This order cannot be cancelled as it is already being shipped or delivered.');
    }
}