<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index', ['orders' => Order::latest()->paginate(30)]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['items.product', 'user'])]);
    }

    public function update(Request $request, Order $order, OrderNotificationService $notifications)
    {
        $oldStatus = $order->status;
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,completed,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
            'status_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $order->update([
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
        ]);

        if ($oldStatus !== $order->status) {
            $order->statusEvents()->create([
                'changed_by' => $request->user()->id,
                'status' => $order->status,
                'note' => $data['status_note'] ?? null,
                'occurred_at' => now(),
            ]);
            $notifications->statusChanged($order->fresh('items'));
        }

        return back()->with('success', __('Order updated.'));
    }
}
