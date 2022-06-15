<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // public function checkout(Request $request, Response $response)
    // {
    //     Stripe::setApiKey('sk_test_VePHdqKTYQjKNInc7u56JBrQ');
    //     $customer = Customer::create();
    //     $ephemeralKey = EphemeralKey::create(
    //         [
    //             'customer' => $customer->id,
    //         ],
    //         [
    //             'stripe_version' => '2020-08-27',
    //         ]
    //     );
    //     $paymentIntent = PaymentIntent::create([
    //         'amount' => 1099,
    //         'currency' => 'eur',
    //         'customer' => $customer->id,
    //         'automatic_payment_methods' => [
    //             'enabled' => 'true',
    //         ],
    //     ]);
    //     return $response->withJson([
    //         'paymentIntent' => $paymentIntent->client_secret,
    //         'ephemeralKey' => $ephemeralKey->secret,
    //         'customer' => $customer->id,
    //         'publishableKey' => 'pk_test_oKhSR5nslBRnBZpjO6KuzZeX'
    //     ])->withStatus(200);
    // }
}
