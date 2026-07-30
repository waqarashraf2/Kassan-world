<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GuestOrderClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GoogleAuthController extends Controller
{
    public function __construct(private readonly GuestOrderClaimService $guestOrders)
    {
    }

    public function redirect(Request $request)
    {
        $this->ensureConfigured();

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function callback(Request $request)
    {
        $this->ensureConfigured();

        $expectedState = (string) $request->session()->pull('google_oauth_state');
        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            throw ValidationException::withMessages(['email' => __('Google sign-in session expired. Please try again.')]);
        }

        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()->route('login')->withErrors(['email' => __('Google sign-in was cancelled.')]);
        }

        try {
            $token = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => $this->redirectUri(),
                'grant_type' => 'authorization_code',
                'code' => $request->string('code'),
            ])->throw()->json();

            $profile = Http::withToken($token['access_token'])->timeout(15)
                ->get('https://openidconnect.googleapis.com/v1/userinfo')
                ->throw()
                ->json();
        } catch (RequestException) {
            return redirect()->route('login')->withErrors([
                'email' => __('Google sign-in could not be completed. Please try again.'),
            ]);
        }

        if (empty($profile['email']) || empty($profile['sub']) || empty($profile['email_verified'])) {
            return redirect()->route('login')->withErrors(['email' => __('Google did not return a verified email address.')]);
        }

        $user = User::where('google_id', $profile['sub'])->orWhere('email', $profile['email'])->first();
        if ($user) {
            $user->update([
                'google_id' => $profile['sub'],
                'avatar_path' => $profile['picture'] ?? $user->avatar_path,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $profile['name'] ?? Str::before($profile['email'], '@'),
                'email' => $profile['email'],
                'google_id' => $profile['sub'],
                'avatar_path' => $profile['picture'] ?? null,
                'email_verified_at' => now(),
                'password' => Str::random(48),
                'role' => 'customer',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->guestOrders->claimFor($user);

        return redirect()->intended(route('customer.dashboard'));
    }

    private function ensureConfigured(): void
    {
        abort_unless(
            config('services.google.client_id') && config('services.google.client_secret'),
            503,
            'Google sign-in has not been configured yet.'
        );
    }

    private function redirectUri(): string
    {
        return config('services.google.redirect') ?: route('google.callback');
    }
}
