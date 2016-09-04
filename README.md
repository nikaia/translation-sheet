
# Laravel Translation Sheet  (wip)

Translation your Laravel languages files using a Google Spreadsheet.


[![Latest Version on Packagist](https://img.shields.io/packagist/v/nikaia/translation-sheet.svg?style=flat-square)](https://packagist.org/packages/nikaia/translation-sheet)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/nikaia/translation-sheet/master.svg?style=flat-square)](https://travis-ci.org/nikaia/translation-sheet)
[![StyleCI](https://styleci.io/repos/67361142/shield)](https://styleci.io/repos/67361142)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/62480992-18f7-4544-99b2-9c529e9feb23.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
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

> a vendor:publish --provider="Nikaia\TranslationSheet\ServiceProvider"


## Usage

### Configure Google Api
- head to https://console.developers.google.com/
- create a new project 
- Make sure to activte Sheet Api for the project
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
    
## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email nboourguig@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Nassif Bourguig](https://github.com/nbourguig)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



```
