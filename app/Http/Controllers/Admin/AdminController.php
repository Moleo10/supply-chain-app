<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    //  AdminController.php

    public function index()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('status', '!=', 'cancelled')->sum('total_price'),

            'recentUsers' => User::latest()->take(5)->get(),
            'recentOrders' => Order::latest()->take(5)->get(),
            'lowStockProducts' => Product::where('stock_quantity', '<', 100)->take(5)->get(),
            'shippedOrders' => Order::where('status', 'shipped')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get(),
        ];

        $allActivities = collect()
            ->concat($data['recentUsers'])
            ->concat($data['recentOrders'])
            ->concat($data['lowStockProducts'])
            ->concat($data['shippedOrders']) //  
            ->sortByDesc(function ($item) {
                return $item->updated_at ?? $item->created_at;
            })
            ->take(10);


        // Orders per status (cancelled orders are excluded from revenue).
        $statusCounts = collect(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
            ->mapWithKeys(fn ($status) => [$status => Order::where('status', $status)->count()])
            ->all();

        // Revenue for each of the last 6 months, computed from real orders.
        $monthlyRevenue = collect(range(5, 0))->mapWithKeys(function ($monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [$month->format('M Y') => (float) Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$month, $month->copy()->endOfMonth()])
                ->sum('total_price')];
        })->all();

        return view('admin.dashboard', array_merge($data, compact('allActivities', 'statusCounts', 'monthlyRevenue')));
    }
    public function users(Request $request)
    {
        $query = User::query();

        //  
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->latest()->paginate(10)->appends($request->query());

        $totalCustomers = User::where('role', 'customer')->count();
        $totalSuppliers = User::where('role', 'supplier')->count();
        $totalAdmins    = User::where('role', 'admin')->count();

        return view('admin.user', compact('users', 'totalCustomers', 'totalSuppliers', 'totalAdmins'));
    }

    /**
     * د
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:admin,customer,supplier'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => 'active', // ا
        ]);

        return back()->with('success', 'User created successfully!');
    }

    /**
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,customer,supplier',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return back()->with('success', 'User updated successfully');
    }

    /**
     */
    public function toggleStatus(User $user)
    {
        $user->status = ($user->status == 'active') ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status changed');
    }

    /**
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }
}
