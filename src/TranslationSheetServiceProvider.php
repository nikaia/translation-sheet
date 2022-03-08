<?php

namespace Nkreliefdev\TranslationSheet;

use Illuminate\Support\ServiceProvider;
use Nkreliefdev\TranslationSheet\Client\Client;
use Nkreliefdev\TranslationSheet\Commands\Lock;
use Nkreliefdev\TranslationSheet\Commands\Open;
use Nkreliefdev\TranslationSheet\Commands\Prepare;
use Nkreliefdev\TranslationSheet\Commands\Publish;
use Nkreliefdev\TranslationSheet\Commands\Pull;
use Nkreliefdev\TranslationSheet\Commands\Push;
use Nkreliefdev\TranslationSheet\Commands\Setup;
use Nkreliefdev\TranslationSheet\Commands\Status;
use Nkreliefdev\TranslationSheet\Commands\Unlock;

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
            return Client::create(
                $this->app['config']['translation_sheet.serviceAccountCredentialsFile'],
                $this->app['config']['translation_sheet.googleApplicationName']
            );
        });
    }

    private function registerSpreadsheet()
    {
        $this->app->singleton(Spreadsheet::class, function () {
            return new Spreadsheet(
                $this->app['config']['translation_sheet.spreadsheetId'],
                Util::asArray($this->app['config']['translation_sheet.locales'])
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
            Publish::class,
        ]);
    }
}
