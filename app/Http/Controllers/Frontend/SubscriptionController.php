<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    /**
     * @throws ApiErrorException
     */
    public function pricing(): Factory|View|Application
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Fetch all plans from Stripe (you can also specify a product ID if needed)
        $plans = Plan::all();

        // Filter out only active plans
        $activePlans = array_filter($plans->data, function ($plan) {
            return isset($plan->active) && $plan->active; // Check if the plan is active
        });

        return view('subscription.price')->with(compact( 'activePlans'));
    }

    public function submit(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve the subscription plan from the request
        $priceId = $request->input('subscription_plan');

        try {
            // Create a new Stripe checkout session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'success_url' => route('frontend.subscription.success'),
                'cancel_url' => route('frontend.subscription.cancel'),
            ]);

            // Save the session ID in the session for later retrieval
            session()->put('stripe_session_id', $session->id);

            // Redirect to the checkout page
            return redirect($session->url);
        } catch (\Exception $e) {
            // Handle errors
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success()
    {
        // Retrieve the Stripe session ID from the session
        $sessionId = session()->get('stripe_session_id');

        if (!$sessionId) {
            return back()->withErrors(['error' => 'No session ID found']);
        }

        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the Stripe checkout session details using the session ID
            $session = Session::retrieve($sessionId);

            // Get the user from the logged-in user
            $user = auth()->user();

            // Retrieve the subscription ID from the session
            $subscriptionId = $session->subscription;

            // Retrieve the subscription details using the subscription ID
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            // Get the payment method details from the subscription
            $paymentMethodId = $subscription->default_payment_method;
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

            // Check if the payment method is a card and retrieve the last 4 digits
            $pm_last = null;
            if ($paymentMethod && isset($paymentMethod->card)) {
                $pm_last = $paymentMethod->card->last4;
            } else {
                throw new \Exception('No card details available for this payment method.');
            }

            // Update the user with Stripe details
            $user->update([
                'stripe_id' => $session->customer,
                'pm_type' => $session->payment_method_types[0], // This is "card" in your case
                'pm_last' => $pm_last,
                'trial_ends_at' => now()->addDays(30), // Customize trial period
            ]);

            // Create a new subscription record
            DB::table('subscriptions')->insert([
                'stripe_id' => $subscription->id, // Subscription ID from Stripe
                'stripe_status' => $subscription->status, // Subscription status (active, canceled, etc.)
                'stripe_price' => $subscription->items->data[0]->price->id, // Stripe plan ID
                'quantity' => 1, // Set this as per your requirements
                'name' => $subscription->status, // Ensure that 'name' is correctly set
                'trial_ends_at' => now()->addDays(30), // You can customize the trial period as needed
                'user_id' => auth()->id(), // Assuming you want to associate this with the currently logged-in user
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Redirect to home
            return redirect(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel()
    {
        // Log the user out
        Auth::logout();

        // Optionally, invalidate the session
        session()->invalidate();
        session()->regenerateToken();

        // Redirect to the home page or login page
        return redirect()->route('frontend.home')->with('status', 'You have been logged out due to subscription cancellation.');
    }

}
