<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'onesignal' => [
        'vn.greenapp.thekings' =>['appId' => 'c00b3c5c-63f9-4eb1-8b77-685cde33bf78',
                                'apiKey' => 'MmNmODBkYzctY2NhMC00OWQ5LWFkMTQtOWQ2ZDljNTdjMzBl'],
        'vn.greenapp.agriapp' => ['appId' => '521943dd-1034-4953-beda-82d46c797d0f',
                                'apiKey' => 'ZDAzMDJjNWYtM2I1Ni00Mjg1LThhODQtODNkNzQ2MDM0OTIw'],
        'vn.greenapp.AgriApp' => ['appId' => '521943dd-1034-4953-beda-82d46c797d0f',
                                'apiKey' => 'ZDAzMDJjNWYtM2I1Ni00Mjg1LThhODQtODNkNzQ2MDM0OTIw'],
        'vn.greenapp.worktrack' => ['appId' => 'af08312a-9e84-47eb-b0da-184e94000f37',
                                  'apiKey' => 'NjQ1YjYzYjgtN2NjMi00NzVjLTgzZWYtNjM5Y2VmYzU2Zjlh']
    ],
];
