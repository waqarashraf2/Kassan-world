<?php

namespace App\Services;

use App\Mail\NewOrderAdminMail;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Notifications\OrderActivityNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    public function orderPlaced(Order $order): void
    {
        $order->loadMissing('items');
        $message = 'Your order has been received and is waiting for confirmation.';

        $this->notifyCustomer($order, 'Order received', $message);

        try {
            $emails = User::where('role', 'admin')->pluck('email')
                ->push(WebsiteSetting::where('key', 'site_email')->value('value'))
                ->push(config('mail.admin_address'))
                ->filter()
                ->unique();

            foreach ($emails as $email) {
                Mail::to($email)->send(new NewOrderAdminMail($order));
            }
        } catch (\Throwable $exception) {
            Log::error('Admin order email could not be sent.', ['order' => $order->id, 'error' => $exception->getMessage()]);
        }
    }

    public function statusChanged(Order $order): void
    {
        [$headline, $message] = match ($order->status) {
            'confirmed' => ['Order confirmed', 'Your order has been confirmed and will move to processing shortly.'],
            'processing' => ['Order processing', 'Our team is preparing your KISANWORLD order.'],
            'shipped' => ['Order shipped', 'Your order has left our facility and is on its way.'],
            'completed' => ['Order delivered', 'Your order has been marked as delivered. Thank you for choosing KISANWORLD.'],
            'cancelled' => ['Order cancelled', 'Your order has been cancelled. Contact support if you need assistance.'],
            default => ['Order update', 'The status of your order has been updated.'],
        };

        $this->notifyCustomer($order, $headline, $message);
    }

    private function notifyCustomer(Order $order, string $headline, string $message): void
    {
        try {
            $order->user?->notify(new OrderActivityNotification($order, $message));

            if ($order->customer_email && ($order->user?->email_notifications ?? true)) {
                Mail::to($order->customer_email)->send(new OrderStatusMail($order, $headline, $message));
            }
        } catch (\Throwable $exception) {
            Log::error('Customer order notification could not be sent.', ['order' => $order->id, 'error' => $exception->getMessage()]);
        }
    }
}
