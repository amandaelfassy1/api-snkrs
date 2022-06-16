<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function intent(Request $request)
    {
        $request->validate([
            'price' => 'required',
            'post_id' => 'required'
        ]);
        $payment = $request->user()->payWith(
            $request->price,
            ['card', 'bancontact'],
            [
                'payment_method_types' => ['card', 'bancontact'],
                'metadata' => [
                    'user_id' => $request->user()->id,
                    'post_id' => $request->post_id,
                    'price' => $request->price,
                ],
            ]
        );
        return $payment;
    }
}
