<?php

return [
    'user'            => env('CROSSREF_USER'),
    'password'        => env('CROSSREF_PASSWORD'),
    'depositor_name'  => env('CROSSREF_DEPOSITOR_NAME', 'GJS - Go Journal System'),
    'depositor_email' => env('CROSSREF_DEPOSITOR_EMAIL'),
    'deposit_url'     => env('CROSSREF_DEPOSIT_URL', 'https://api.crossref.org/deposits'),
];
