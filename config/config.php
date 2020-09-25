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
    'serviceAccountCredentialsFile' => env('TS_SERVICE_ACCOUNT_CREDENTIALS_FILE', base_path('resources/google/service-account-crendentials.json')),

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

    /*
    |--------------------------------------------------------------------------
    | Exclude lang files or namespaces
    |--------------------------------------------------------------------------
    | List here files or namespaces that the package will exclude
    | from all the operations. (prepare, push & pull).
    |
    | You can use wild card pattern that can be used with the Str::is()
    | Laravel helper. (https://laravel.com/docs/5.8/helpers#method-str-is)
    |
    | Example:
    |   'validation*'
    |   'foo::*',
    |   'foo::bar.*',
    */
    'exclude' => [],

    /**
     * Primary sheet (tab) used for the translations.
     *
     */
    'primary_sheet' => [
        'name' => 'Translations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Extra Sheets
    |--------------------------------------------------------------------------
    | This config area give you the possibility to other sheets (tabs) to your spreadsheet.
    | they can be used to translate sperately other sections of your application.

    | ie. if you handle your web app or mobile app translation in laravel app. you can instruct
    | translations-sheet to add them as sheets.
    | Files for theses sheets must no live under resources/lang folder. But, resources/web-app-lang for instance.
    |
    */
    'extra_sheets' => [
        /*
        [
            // Sheet name
            'name' => 'Web App',

            // Relative path to the resources/lang folder, where the translations files are stored
            'path' => resource_path('web-app/lang'),

            // Tab color
            'tabColor' => '#0000FF',
        ]
        */
    ],
];
