<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('customer.dashboard', [
            'recentOrders' => $user->orders()->with('items')->latest('placed_at')->limit(5)->get(),
            'orderCounts' => $user->orders()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'unreadNotifications' => $user->unreadNotifications()->limit(6)->get(),
        ]);
    }

    public function profile(Request $request)
    {
        return view('customer.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);
        $data['email_notifications'] = $request->boolean('email_notifications');
        $user->update($data);

        return back()->with('success', __('Profile updated.'));
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', __('Password changed securely.'));
    }

    public function orders(Request $request)
    {
        return view('customer.orders.index', [
            'orders' => $request->user()->orders()->withCount('items')->latest('placed_at')->paginate(12),
        ]);
    }

    public function order(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('customer.orders.show', [
            'order' => $order->load(['items.product.images', 'statusEvents.changedBy']),
        ]);
    }

    public function notifications(Request $request)
    {
        return view('customer.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function readNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', __('Notifications marked as read.'));
    }
}
