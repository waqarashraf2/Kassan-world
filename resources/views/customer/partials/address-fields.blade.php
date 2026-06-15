<div class="form-grid"><label>Label<input name="label" value="{{ old('label', $address?->label ?? 'Home') }}" required></label><label>Recipient<input name="recipient_name" value="{{ old('recipient_name', $address?->recipient_name ?? auth()->user()->name) }}" required></label></div>
<div class="form-grid"><label>Phone<input name="phone" value="{{ old('phone', $address?->phone ?? auth()->user()->phone) }}" required></label><label>City<input name="city" value="{{ old('city', $address?->city) }}"></label></div>
<label>Full address<textarea name="address" rows="3" required>{{ old('address', $address?->address) }}</textarea></label>
<label>Postal code<input name="postal_code" value="{{ old('postal_code', $address?->postal_code) }}"></label>
<label class="check-label"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default))> Use as default address</label>
