<?php

return [
    'api_keys' => [
        'secret_key' => env('STRIPE_SECRET_KEY', null)
    ]
];


$stripe = new \Stripe\StripeClient(
    'sk_test_51Mt2sYBMW2pHNbgO03YUbRusadpTK3kaTvlD8HH0yG1SCc6w8UHnZXSdH5tiiPEfKDPDP7OVg8r0izz2KwLUQ84I00IkCJVzWB'
);
$stripe->tokens->create([
    'card' => [
        'number' => '4242424242424242',
        'exp_month' => 2,
        'exp_year' => 2023,
        'cvc' => '314',
    ],
]);

$stripe->charges->create([
    'amount' => 2,
    'currency' => 'usd',
    'source' => 'tok_mastercard',
    'description' => 'My First Test Charge',
]);