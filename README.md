# Laravel Translation Sheet

Translating Laravel languages files using a Google Spreadsheet.


[![Latest Version on Packagist](https://img.shields.io/packagist/v/nikaia/translation-sheet.svg?style=flat-square)](https://packagist.org/packages/nikaia/translation-sheet)
[![Build Status](https://github.com/nikaia/translation-sheet/workflows/run-tests/badge.svg)](https://github.com/nikaia/translation-sheet/actions?query=workflow%3Arun-tests)


![](./docs/banner.jpg)

## Contents

- [Installation](#installation)
- [Usage](#usage)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

- Install package

    ```bash
    $ composer require nikaia/translation-sheet --dev
    ```
    
- If Laravel version <= 5.4,  Add service provider to your 'config/app.php'. For version >= 5.5, package will be auto-discoverd by Laravel.

    ```php
    Nikaia\TranslationSheet\TranslationSheetServiceProvider::class,
    ```



- Configuration can be done via environments variables, but if you prefer you can override the configuration by publishing the package config file using :
    
    ```bash
    $ php artisan translation_sheet:publish
        or
    $ php artisan vendor:publish --provider="Nikaia\TranslationSheet\TranslationSheetServiceProvider"
    ```

### Requirements
Laravel >= 5.1
    
## Configuration

### Google Api credentials

- Create a new project in https://console.developers.google.com/ 
- Make sure to activate `Sheet Api` for the created project
    - Navigate to "Library"                                      
    - Search "Google Sheets API" > Click on "Google Sheets API" and click enable                           
- Create credentials
    - Navigate to "Credentials"
    - Click "Create credentials" 
    - Choose "Service Account key"
    - Choose A "New Service Account" in the "Service account" select
        - Choose a name. (ie. This is the name that will show up in the Spreadsheet history operations), 
        - Choose "Editor" as the role 
        - Choose "JSON" for the key type.
    - Save the credentials to 'resources/google/service-account.json' folder. (You can choose another name/folder and update the package configuration)
    - Make sure to write down the service account email, you will need to share the spreadsheet with this email (see below).

### Spreadsheet
 - Create a blank/new spreadsheet here [https://docs.google.com/spreadsheets/](https://docs.google.com/spreadsheets/) .
 - Share it with the service account email with `Can edit` permission.
 

### Package configuration

In your .env file or in your published config file (`config/translation_sheet.php`), you need to add the following
       
    # The service account email   
    TS_SERVICE_ACCOUNT_EMAIL=***@***.iam.gserviceaccount.com
    
    # The path to the downloaded service account credentials file
    TS_SERVICE_ACCOUNT_CREDENTIALS_FILE=resources/google/service-account.json
    
    # The ID of the spreadsheet that we will be using for translation (the last portion of the spreadsheet url)
    TS_SPREADSHEET_ID=xxxx
    
    # The locales of the application (separated by comma) 
    TS_LOCALES=fr,en,es
  
  
## Usage
  
1/ Setup the spreadsheet 
  
This need to be done only once.
  
```bash
$ php artisan translation_sheet:setup
```  
  
2/ Prepare the sheet
 
To avoid some conflicts, we will first run this command to rewrite the locale languages files.

```bash
$ php artisan translation_sheet:prepare
```  
  
3/ Publish translation to sheet

```bash
$ php artisan translation_sheet:push
```  
  
4/ Share the spreadsheet with clients or project managers for translations.
  
5/ Once done, You can lock the translations on the spreadsheet (to avoid conflicts)  
```bash
$ php artisan translation_sheet:lock
```  

6/ Pull the translations

This will pull the translations from the spreadsheet, and write it the language files in your applications.
You can use git diff here to make sure eveything is ok (Conflicts, errors etc ...)
```bash
$ php artisan translation_sheet:pull
```  

6/ Unlock the translations on the spreadsheet
```bash
$ php artisan translation_sheet:unlock
```  
    
Open the spreadsheet in the browser
```bash
$ php artisan translation_sheet:open
```  

## Excluding translations 

Sometimes you might need to instruct the package to exclude some translations. 
You can do so by specifying patterns in the `exclude` config option.
It accepts multiple patterns that target the full translation keys and that the [Str::is](https://laravel.com/docs/5.8/helpers#method-str-is) can understand. 

```php
[
    // ...
    
    'exclude' => [
        'validation*',  // This will exclude all the `validation.php` translations.
        'foo::*',       // This will exclude all the `foo` namespace translations.
        'foo::bar.*',   // this will exclude the `bar` translations from the `foo` namespace.
    ],
    
    // ...
]
```  


## Extra sheets

![Extra sheets](./docs/extra-sheets.png)

Sometimes you may have other files that need translations. 
They are not related to the laravel application per se and are not stored in the `resources\lang` folder. 
Maybe you are building a web app (spa), or even a mobile app alongside
the laravel app, and you need to handle their translations. 

In this specific case, you can configure extra sheets to handle those translations files stored inside a specific path.

- This feature handles only json files.
- The files must live outside the `resources\lang` directory. For instance `resources\web-app\lang`.

```php
[
    // ...

    'extra_sheets' => [
        [
            'name' => 'Web App',                        // Spreadsheet sheet (tab) name.
            'path' => resource_path('web-app/lang'),    // Path where json files are stored. Files can be organized inside sub folders.        
            'tabColor' => '#0000FF',                    // Color of the spreadsheet tab
        ],
        [
            'name' => 'Mobile App',
            'path' => resource_path('mobile-app/lang'),        
            'tabColor' => '#0000FF',
        ],    
    ],
]
```

> You need to run `translation_sheet:setup` command, if you add this config later on. 










## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

_N.B : You need a valid configuration service-account.json file to run tests._

### Github action

To test your fork using github action, you need a valid `service-account.json`. The file is ignored in the repository to avoid exposing credentials.
You need to encode your credentials file `tests/fixtures/service-account.json using `gpg`

```bash
# Save credential file to tests/fixtures/service-account.json
$ gpg -c tests/fixtures/service-account.json  tests/fixtures/service-account.json.gpg
```
 
Commit the `.gpg` encoded file. 
 
PS. Github action will decrypt the file just before running the tests. See the `run-tests.yml` file.


## Security

If you discover any security related issues, please email nbourguig@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Nassif Bourguig](https://github.com/nbourguig)
- [All Contributors](../../contributors)

