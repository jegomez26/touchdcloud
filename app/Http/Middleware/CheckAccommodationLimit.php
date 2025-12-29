<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Property;
use App\Models\Provider;

class CheckAccommodationLimit
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

        // Get provider profile
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return redirect()->route('provider.create')->with('error', 'Please complete your provider profile first.');
        }

        // Get current subscription
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
            return redirect()->route('subscription.plans')->with('error', 'An active subscription is required to add accommodation listings.');
        }

        // Check if subscription includes accommodation listings
        if ($subscription->accommodation_listing_limit <= 0) {
            return redirect()->route('subscription.manage')->with('error', 
                'Accommodation listings are not available with your current plan. Please upgrade to Growth or Premium plan.'
            );
        }

        // Check accommodation listing limit
        $currentCount = Property::where('provider_id', $provider->id)->count();
        
        if ($currentCount >= $subscription->accommodation_listing_limit) {
            return redirect()->route('subscription.manage')->with('error', 
                'You have reached your accommodation listing limit (' . $currentCount . '/' . $subscription->accommodation_listing_limit . '). ' .
                'Please upgrade your plan to add more listings.'
            );
        }

        return $next($request);
    }
}