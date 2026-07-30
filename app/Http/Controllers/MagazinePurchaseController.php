<?php

namespace App\Http\Controllers;

use App\Mail\NewMagazinePurchaseAdminMail;
use App\Models\Magazine;
use App\Models\MagazinePurchase;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MagazinePurchaseController extends Controller
{
    public function store(Request $request, Magazine $magazine)
    {
        abort_unless($magazine->is_active, 404);
        abort_if($magazine->is_free || (float) $magazine->price === 0.0, 422, 'This magazine is free.');

        $data = $request->validate([
            'payment_method' => ['required', 'in:bank_transfer,online_payment'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_proof' => ['exclude_unless:payment_method,bank_transfer', 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'billing_name' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:120'],
            'billing_email' => ['exclude_unless:payment_method,online_payment', 'required', 'email', 'max:255'],
            'billing_phone' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:30'],
            'billing_city' => ['exclude_unless:payment_method,online_payment', 'nullable', 'string', 'max:120'],
            'billing_address' => ['exclude_unless:payment_method,online_payment', 'required', 'string', 'max:1000'],
            'online_payment_consent' => ['exclude_unless:payment_method,online_payment', 'required', 'accepted'],
            'card_number' => ['prohibited'],
            'card_no' => ['prohibited'],
            'pan' => ['prohibited'],
            'cvc' => ['prohibited'],
            'cvv' => ['prohibited'],
            'expiry' => ['prohibited'],
            'expiry_date' => ['prohibited'],
            'expire_date' => ['prohibited'],
        ]);
        $proof = $data['payment_proof'] ?? null;

        $purchase = MagazinePurchase::updateOrCreate(
            ['user_id' => $request->user()->id, 'magazine_id' => $magazine->id],
            [
                'purchase_number' => 'KWM-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'amount' => $magazine->price,
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'payment_details' => $this->paymentDetails($data),
                'payment_proof_path' => $proof?->store('payment-proofs/magazines', 'local'),
                'payment_proof_original_name' => $proof?->getClientOriginalName(),
                'payment_status' => 'pending',
                'paid_at' => null,
            ]
        );

        $this->notifyAdmins($purchase->load(['user', 'magazine']));

        return redirect()->route('magazines.show', $magazine)
            ->with('success', __('Purchase request :number submitted.', ['number' => $purchase->purchase_number]));
    }

    private function paymentDetails(array $data): ?array
    {
        if (($data['payment_method'] ?? null) !== 'online_payment') {
            return null;
        }

        return [
            'gateway' => 'bank_alfalah',
            'billing_name' => $data['billing_name'] ?? null,
            'billing_email' => $data['billing_email'] ?? null,
            'billing_phone' => $data['billing_phone'] ?? null,
            'billing_city' => $data['billing_city'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'card_collection' => 'redirect_gateway_only',
            'note' => 'No card number, CVV, PIN, OTP or gateway secret is stored by KISANWORLD.',
        ];
    }

    private function notifyAdmins(MagazinePurchase $purchase): void
    {
        try {
            $emails = User::where('role', 'admin')->pluck('email')
                ->push(WebsiteSetting::where('key', 'site_email')->value('value'))
                ->push(config('mail.admin_address'))
                ->filter()
                ->unique();

            foreach ($emails as $email) {
                Mail::to($email)->send(new NewMagazinePurchaseAdminMail($purchase));
            }
        } catch (\Throwable $exception) {
            Log::error('Admin magazine purchase email could not be sent.', ['purchase' => $purchase->id, 'error' => $exception->getMessage()]);
        }
    }
}
