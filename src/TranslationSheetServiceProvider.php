<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\ServiceProvider;
use Nikaia\TranslationSheet\Client\Client;
use Nikaia\TranslationSheet\Commands\Lock;
use Nikaia\TranslationSheet\Commands\Open;
use Nikaia\TranslationSheet\Commands\Prepare;
use Nikaia\TranslationSheet\Commands\Pull;
use Nikaia\TranslationSheet\Commands\Push;
use Nikaia\TranslationSheet\Commands\Setup;
use Nikaia\TranslationSheet\Commands\Status;
use Nikaia\TranslationSheet\Commands\Unlock;

class TranslationSheetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('translation_sheet.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'translation_sheet');

        $this->registerGoogleApiClient();

        $this->registerSpreadsheet();

        $this->registerCommands();
    }

    private function registerGoogleApiClient()
    {
        $this->app->singleton(Client::class, function () {
            $client = new Client;
            $client->setAuthConfigFile($this->app['config']['translation_sheet.serviceAccountCredentialsFile']);
            $client->setApplicationName($this->app['config']['translation_sheet.googleApplicationName']);
            $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);

            return $client;
        });
    }

    private function registerSpreadsheet()
    {
        $this->app->singleton(Spreadsheet::class, function () {
            return new Spreadsheet(
                $this->app['config']['translation_sheet.spreadsheetId'],
                $this->asArray($this->app['config']['translation_sheet.locales'])
            );
        });
    }

    private function registerCommands()
    {
        $this->commands([
            Setup::class,
            Push::class,
            Pull::class,
            Prepare::class,
            Lock::class,
            Unlock::class,
            Status::class,
            Open::class,
        ]);
    }

    private function asArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return array_filter(array_map(function ($item) {
            return trim($item);
        }, explode(',', $value)));
    }
}
