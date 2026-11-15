<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplyChainTest extends TestCase
{
    use RefreshDatabase;

    private function product(User $supplier, array $attrs = []): Product
    {
        static $n = 0;
        $n++;

        return Product::create(array_merge([
            'user_id' => $supplier->id,
            'name' => "Product $n",
            'sku' => "SKU-$n",
            'category' => 'Electronics',
            'stock_quantity' => 10,
            'price' => 100,
            'shipping_method' => ['Land'],
        ], $attrs));
    }

    // ------------------------------------------------------------ role-based access

    public function test_each_role_lands_on_its_own_dashboard(): void
    {
        foreach (['admin' => 'admin.dashboard', 'supplier' => 'supplier.dashboard', 'customer' => 'customer.dashboard'] as $role => $route) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/dashboard')->assertRedirect(route($route));
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_roles_cannot_access_each_others_areas(): void
    {
        $customer = User::factory()->create();
        $supplier = User::factory()->supplier()->create();

        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('supplier.dashboard'))->assertForbidden();
        $this->actingAs($supplier)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($supplier)->get(route('customer.products'))->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('customer.orders'))->assertRedirect(route('login'));
        $this->get(route('admin.reports'))->assertRedirect(route('login'));
    }

    public function test_nobody_can_self_register_as_admin(): void
    {
        $this->post('/register', [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'Str0ng!Pass',
            'password_confirmation' => 'Str0ng!Pass',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.com']);
    }

    public function test_deactivated_accounts_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    // ------------------------------------------------------------ ordering & inventory

    public function test_placing_an_order_updates_stock_and_sales(): void
    {
        $product = $this->product(User::factory()->supplier()->create(), ['stock_quantity' => 10, 'price' => 250]);
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->post(route('customer.orders.store'), ['product_id' => $product->id, 'quantity' => 3])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 3, 'total_price' => 750, 'status' => 'processing',
        ]);
        $product->refresh();
        $this->assertSame(7, $product->stock_quantity);
        $this->assertSame(3, $product->sold_count);
    }

    public function test_cannot_order_more_than_in_stock(): void
    {
        $product = $this->product(User::factory()->supplier()->create(), ['stock_quantity' => 2]);

        $this->actingAs(User::factory()->create())
            ->post(route('customer.orders.store'), ['product_id' => $product->id, 'quantity' => 5])
            ->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    public function test_cancelling_an_order_returns_stock(): void
    {
        $product = $this->product(User::factory()->supplier()->create(), ['stock_quantity' => 10]);
        $customer = User::factory()->create();
        $this->actingAs($customer)->post(route('customer.orders.store'), ['product_id' => $product->id, 'quantity' => 4]);
        $order = Order::first();

        $this->actingAs($customer)->post(route('customer.orders.cancel', $order->id))->assertSessionHas('success');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertSame(0, $product->fresh()->sold_count);
    }

    public function test_shipped_orders_cannot_be_cancelled(): void
    {
        $product = $this->product(User::factory()->supplier()->create());
        $customer = User::factory()->create();
        $order = Order::create(['user_id' => $customer->id, 'product_id' => $product->id, 'quantity' => 1, 'total_price' => 100, 'status' => 'shipped']);

        $this->actingAs($customer)->post(route('customer.orders.cancel', $order->id))->assertSessionHas('error');
        $this->assertSame('shipped', $order->fresh()->status);
    }

    public function test_customers_cannot_see_or_cancel_other_customers_orders(): void
    {
        $product = $this->product(User::factory()->supplier()->create());
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::create(['user_id' => $owner->id, 'product_id' => $product->id, 'quantity' => 1, 'total_price' => 100, 'status' => 'processing']);

        $this->actingAs($other)->get(route('customer.orders.show', $order->id))->assertNotFound();
        $this->actingAs($other)->post(route('customer.orders.cancel', $order->id))->assertNotFound();
        $this->actingAs($owner)->get(route('customer.orders.show', $order->id))->assertOk()->assertJsonPath('id', $order->id);
    }

    // ------------------------------------------------------------ supplier isolation

    public function test_suppliers_can_only_modify_their_own_products(): void
    {
        $alice = User::factory()->supplier()->create();
        $bob = User::factory()->supplier()->create();
        $product = $this->product($alice, ['name' => 'Original']);

        $payload = ['name' => 'Hacked', 'category' => 'X', 'price' => 1, 'stock_quantity' => 1, 'shipping_method' => ['Air']];
        $this->actingAs($bob)->put(route('supplier.products.update', $product->id), $payload)->assertNotFound();
        $this->actingAs($bob)->delete(route('supplier.products.delete', $product->id))->assertNotFound();

        $this->assertSame('Original', $product->fresh()->name);
        $this->assertNull($product->fresh()->deleted_at);
    }

    public function test_product_update_cannot_change_owner_or_sales_figures(): void
    {
        $supplier = User::factory()->supplier()->create();
        $product = $this->product($supplier);

        $this->actingAs($supplier)->put(route('supplier.products.update', $product->id), [
            'name' => 'Renamed', 'category' => 'Furniture', 'price' => 50, 'stock_quantity' => 5, 'shipping_method' => ['Sea'],
            'user_id' => 999, 'sold_count' => 1000000,
        ])->assertSessionHas('success');

        $product->refresh();
        $this->assertSame('Renamed', $product->name);
        $this->assertSame($supplier->id, $product->user_id);
        $this->assertSame(0, $product->sold_count);
    }

    public function test_suppliers_can_only_update_orders_for_their_products(): void
    {
        $alice = User::factory()->supplier()->create();
        $bob = User::factory()->supplier()->create();
        $order = Order::create([
            'user_id' => User::factory()->create()->id, 'product_id' => $this->product($alice)->id,
            'quantity' => 1, 'total_price' => 100, 'status' => 'processing',
        ]);

        $this->actingAs($bob)->postJson(route('supplier.orders.update', $order->id), ['status' => 'delivered'])->assertNotFound();
        $this->assertSame('processing', $order->fresh()->status);

        $this->actingAs($alice)->postJson(route('supplier.orders.update', $order->id), ['status' => 'shipped'])
            ->assertOk()
            ->assertJsonPath('counts.shipped', 1);
        $this->assertSame('shipped', $order->fresh()->status);
    }

    // ------------------------------------------------------------ admin

    public function test_admin_can_create_users_and_export_reports(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Supplier', 'email' => 'new@scm.com', 'password' => 'Str0ng!Pass',
            'password_confirmation' => 'Str0ng!Pass', 'role' => 'supplier',
        ])->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'new@scm.com', 'role' => 'supplier']);

        $this->actingAs($admin)->get(route('admin.reports'))->assertOk();
        $response = $this->actingAs($admin)->get(route('admin.reports.export'));
        $response->assertOk();
        $this->assertStringContainsString('"Order ID","Product Name"', $response->streamedContent());
    }

    public function test_seeded_demo_data_renders_every_page(): void
    {
        $this->seed();

        foreach (['admin@scm.com' => ['admin.dashboard', 'admin.users.index', 'admin.reports'],
                  'supplier@scm.com' => ['supplier.dashboard', 'supplier.products', 'supplier.orders'],
                  'customer@scm.com' => ['customer.dashboard', 'customer.products', 'customer.orders']] as $email => $routes) {
            $user = User::where('email', $email)->first();
            foreach ($routes as $route) {
                $this->actingAs($user)->get(route($route))->assertOk();
            }
        }
    }
}
