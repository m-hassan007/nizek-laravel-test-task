<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Price;

/**
 *
 */
class PaymentController extends Controller
{
    /**
     * @throws ApiErrorException
     */
    public function index(Request $request): Factory|View|Application
    {
        // Set your secret API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Assume user is authenticated
        $user = Auth::user();

        // Define the recurring price based on the subscription type
        $unitAmount = $user->s_type === 'monthly' ? 1000 : 10000; // Amount in cents

        // Create a price object for the subscription
        $price = Price::create([
            'unit_amount' => $unitAmount,
            'currency' => 'usd',
            'recurring' => [
                'interval' => $user->s_type === 'monthly' ? 'month' : 'year', // Monthly or yearly subscription
            ],
            'product_data' => [
                'name' => 'Subscription (' . ucfirst($user->s_type) . ')',
            ],
        ]);

        // Create a checkout session
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $price->id,  // Use the price object created above
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('backend.checkout.success'),
            'cancel_url' => route('backend.checkout.cancel'),
        ]);

        // Return the view with the checkout session ID
        return view('backend.payment.stripecheckout', [
            'checkoutSessionId' => $checkoutSession->id,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function process(Request $request): RedirectResponse
    {
        $request->user()->createOrGetStripeCustomer();

        $paymentMethod = $request->input('payment_method');

        $request->user()->addPaymentMethod($paymentMethod);

        // Example: Charge a one-time payment
        $request->user()->charge(5000, $paymentMethod); // Amount in cents

        return redirect()->route('home')->with('success', 'Payment successful!');
    }
}
