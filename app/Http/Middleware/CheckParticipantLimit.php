<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Participant;

class CheckParticipantLimit
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
            return redirect()->route('subscription.plans')->with('error', 'An active subscription is required to add participant profiles.');
        }

        // Check participant profile limit
        $currentCount = Participant::where('added_by_user_id', $user->id)->count();
        
        if (!$subscription->canAddParticipantProfiles($currentCount)) {
            return redirect()->route('subscription.manage')->with('error', 
                'You have reached your participant profile limit (' . $currentCount . '/' . $subscription->participant_profile_limit . '). ' .
                'Please upgrade your plan or wait for your monthly refresh to add more profiles.'
            );
        }

        return $next($request);
    }
}