<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderNotificationService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $cart = collect($request->session()->get('cart', []));
        $products = Product::active()->with('images')->whereIn('id', $cart->keys())->get();

        return view('checkout.create', compact('cart', 'products'));
    }

    public function store(
        CheckoutRequest $request,
        OrderService $orders,
        OrderNotificationService $notifications,
    )
    {
        $data = $request->validated();
        $user = $request->user();

        [$order, $createdUser] = DB::transaction(function () use ($request, $orders, $data, $user): array {
            $createdUser = null;
            if (! $user && $request->boolean('create_account')) {
                $createdUser = User::create([
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'phone' => $data['customer_phone'],
                    'password' => Hash::make($data['password']),
                    'role' => 'customer',
                ]);
                $user = $createdUser;
            }

            $order = $orders->place($data, $user);

            if ($user && ($createdUser || $request->boolean('save_address'))) {
                Address::updateOrCreate([
                    'user_id' => $user->id,
                    'address' => $data['shipping_address'],
                ], [
                    'label' => 'Home',
                    'recipient_name' => $data['customer_name'],
                    'phone' => $data['customer_phone'],
                    'city' => $data['city'] ?? null,
                    'is_default' => ! $user->addresses()->exists(),
                ]);
            }

            return [$order, $createdUser];
        });

        if ($createdUser) {
            Auth::login($createdUser);
            $request->session()->regenerate();
        }

        $request->session()->forget('cart');
        $request->session()->put('recent_order_id', $order->id);
        $notifications->orderPlaced($order);

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where(function ($query) use ($request): void {
                $query->whereKey($request->session()->get('recent_order_id', 0));
                if ($request->user()) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            })
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
