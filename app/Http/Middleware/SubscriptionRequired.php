<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Provider;

class SubscriptionRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has a provider profile
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        // Check for active subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('paypal_status', 'active');
            })
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            })
            ->first();

        if (!$subscription) {
            return redirect()->route('subscription.plans')->with('error', 'An active subscription is required to access this feature.');
        }

        // Add subscription to request for use in controllers
        $request->merge(['current_subscription' => $subscription]);

        return $next($request);
    }
}