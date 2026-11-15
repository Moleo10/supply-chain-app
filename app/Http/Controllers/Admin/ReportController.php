<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_price');

        $totalOrders = Order::count();

        $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $topProducts = Product::orderBy('sold_count', 'desc')->take(5)->get();
        
        $lowStockProducts = Product::where('stock_quantity', '<', 100)->get();

        return view('admin.reports', compact('totalRevenue', 'totalOrders', 'avgOrder', 'topProducts', 'lowStockProducts'));
    }

    public function exportReport(Request $request)
    {
        $fileName = 'SCA_Report_' . date('Y-m-d') . '.csv';
        $orders = Order::with('product')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order ID', 'Product Name', 'Quantity', 'Total Price', 'Status', 'Order Date'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->product->name ?? 'N/A',
                    $order->quantity,
                    $order->total_price,
                    $order->status,
                    $order->created_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}