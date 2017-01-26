
# Laravel Translation Sheet

Translating Laravel languages files using a Google Spreadsheet.


[![Latest Version on Packagist](https://img.shields.io/packagist/v/nikaia/translation-sheet.svg?style=flat-square)](https://packagist.org/packages/nikaia/translation-sheet)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/nikaia/translation-sheet/master.svg?style=flat-square)](https://travis-ci.org/nikaia/translation-sheet)
[![StyleCI](https://styleci.io/repos/67361142/shield)](https://styleci.io/repos/67361142)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/62480992-18f7-4544-99b2-9c529e9feb23.svg?style=flat-square)](https://insight.sensiolabs.com/projects/62480992-18f7-4544-99b2-9c529e9feb23)
[![Quality Score](https://img.shields.io/scrutinizer/g/nikaia/translation-sheet.svg?style=flat-square)](https://scrutinizer-ci.com/g/nikaia/translation-sheet)


<p align="center">
    <img src="https://s18.postimg.org/9q7czq50p/translation_sheet.jpg" alt="Laravel Translation Sheet">
</p>

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
    $ composer require nikaia/translation-sheet
    ```
    
- Add service provider to your 'config/app.php'    

    ```php
    Nikaia\TranslationSheet\TranslationSheetServiceProvider::class,
    ```

- Configuration can be done via environments variables, but if you prefer you can override the configuration by publishing the package config file using :
    
    ```bash
    $ php artisan vendor:publish --provider="Nikaia\TranslationSheet\ServiceProvider"
    ```

### Requirements
Laravel 5.1 | 5.2 | 5.3 | 5.4    
    
## Configuration

### Google Api

- head to https://console.developers.google.com/
- create a new project 
- Make sure to activate Sheet Api for the project
    - Navigate to "Library"
    - Search "Google Sheets API" > Click on "Google Sheets API"
    - Click "Enable"
- Create a Service Account and credentials
    - Navigate to "Credentials"
    - Click "Create credentials" 
    - choose "Service Account key"
    - Choose A "New Service Account" in the "Service account" select
    - Choose a name. (ie. This is the name that will show up in the Spreadsheet history operations), "Editor" as role and "JSON" for the key type.
    - Save the credentials to 'resources/google/service-account.json' folder. (You can choose another name/folder if you want in your application folder)
    - Make sure to write down the service account email, you will need it later for the package configuration.               

### Spreadsheet
 - Create a blank/new spreadsheet here [https://docs.google.com/spreadsheets/](https://docs.google.com/spreadsheets/) .
 - Share it with the service account email with `Can edit` permission.
 
 
 
### Required configuration

In your .env file or in your published config file (`config/translation_sheet.php`)
       
    # The service account email   
    TS_SERVICE_ACCOUNT_EMAIL=***@***.iam.gserviceaccount.com
    
    # The path to the downloaded service account credentials file
    TS_SERVICE_ACCOUNT_CREDENTIALS_FILE=resources/google/service-account.json
    
    # The ID of the spreadsheet that we will be using for translation
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


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

_N.B : You need a valid service-account.json file to run tests._

## Security

If you discover any security related issues, please email nbourguig@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Nassif Bourguig](https://github.com/nbourguig)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
