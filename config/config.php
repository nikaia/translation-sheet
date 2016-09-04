<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google application name
    |--------------------------------------------------------------------------
    */
    'googleApplicationName' => env('TS_GOOGLE_APP_NAME', 'Laravel Translation Sheet'),

    /*
    |--------------------------------------------------------------------------
    | Google service account email
    |--------------------------------------------------------------------------
    | This is the service account email that you need to create.
    | https://console.developers.google.com/apis/credentials
    */
    'serviceAccountEmail' => env('TS_SERVICE_ACCOUNT_EMAIL', '***@***.iam.gserviceaccount.com'),

    /*
    |--------------------------------------------------------------------------
    | Google service account credentials file path
    |--------------------------------------------------------------------------
    */
    'serviceAccountCredentialsFile' => env('TS_SERVICE_ACCOUNT_CREDENTIALS_FILE', resource_path('google/service-account-crendentials.json')),

    /*
    |--------------------------------------------------------------------------
    | Spreadsheet
    |--------------------------------------------------------------------------
    | The spreadsheet that will be used for translations.
    | You need to create a new empty spreadsheet manually and fill its ID here.
    | You can find the ID in the spreadsheet URL.
    | (https://docs.google.com/spreadsheets/d/{spreadsheetId}/edit#gid=0)
    */
    'spreadsheetId' => env('TS_SPREADSHEET_ID', '***'),

    /*
    |--------------------------------------------------------------------------
    | Available locales
    |--------------------------------------------------------------------------
    | List here the app locales
    */
    'locales' => env('TS_LOCALES', ['en']),

];

