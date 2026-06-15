<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return view('customer.addresses', ['addresses' => $request->user()->addresses()->latest()->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($request, $data): void {
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->update(['is_default' => false]);
            }
            $request->user()->addresses()->create($data + ['is_default' => $request->boolean('is_default')]);
        });

        return back()->with('success', __('Address saved.'));
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $data = $this->validated($request);
        DB::transaction(function () use ($request, $address, $data): void {
            if ($request->boolean('is_default')) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }
            $address->update($data + ['is_default' => $request->boolean('is_default')]);
        });

        return back()->with('success', __('Address updated.'));
    }

    public function destroy(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $address->delete();

        return back()->with('success', __('Address removed.'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:60'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
