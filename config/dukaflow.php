<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Credit Engine Feature Flag
    |--------------------------------------------------------------------------
    |
    | The credit-scoring and working-capital lending module is built in v1
    | but stays gated until a partner bank/MFI agreement and regulatory
    | sign-off are in place. Flip this on to reveal it in the admin and
    | merchant portal navigation once the business is ready to go live.
    |
    */
    'credit_engine_enabled' => env('DUKAFLOW_CREDIT_ENGINE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Platform contact details
    |--------------------------------------------------------------------------
    |
    | Shown on the "Powered by DukaFlow" footer of generated documents
    | (invoices, proformas). Phone and address are optional — the footer
    | only prints the ones that are actually set.
    |
    */
    'support_email' => env('DUKAFLOW_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS')),
    'support_phone' => env('DUKAFLOW_SUPPORT_PHONE'),
    'support_address' => env('DUKAFLOW_SUPPORT_ADDRESS'),

];
