<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        // Define the roles to exclude from subscription checks
        $excludedRoles = ['super admin', 'administrator', 'manager'];

        // Check if the user is authenticated and their role is excluded
        if (auth()->check() && in_array(auth()->user()->role, $excludedRoles)) {
            return $next($request); // Allow access
        }

        $user = auth()->user();

        // Check if the user is subscribed and their subscription has not ended
        if ($user && $user->subscribed('default')) {
            // Ensure the subscription has not ended
            if (!$user->subscription('default')->ended() && !$user->subscription('default')->onGracePeriod()) {
                return $next($request); // Allow access
            }
        }

        // Check if the trial period has ended
        $trialEndsAt = optional($user->subscription('default'))->trial_ends_at;

        if ($trialEndsAt && Carbon::parse($trialEndsAt)->isFuture()) {
            return $next($request); // Allow access if trial is still active
        }

        // Redirect to subscription pricing page if not subscribed or trial ended
        return redirect()->route('frontend.subscription.pricing');
    }
}
