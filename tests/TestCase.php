<?php

namespace Nikaia\TranslationSheet\Test;

use Illuminate\Console\Application;
use Nikaia\TranslationSheet\TranslationSheetServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /** @var TestHelper */
    protected $helper;

    protected $consoleOutput;

    protected function getPackageProviders($app)
    {
        return [TranslationSheetServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Make laravel application load language files from our test folder
        $app->instance('path.lang', (new TestHelper($app))->langPath());
    }

    public function setUp()
    {
        parent::setUp();

        $this->helper = new TestHelper($this->app);

        $this->helper->deleteLangFiles();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->helper->deleteLangFiles();
        $this->helper = null;
        $this->consoleOutput = '';
    }

    public function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('artisan', function ($app) {
            return new Application($app, $app['events'], $app->version());
        });

        $app->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);
    }

    public function artisan($command, $parameters = [])
    {
        parent::artisan($command, array_merge($parameters, ['--no-interaction' => true]));
    }

    public function consoleOutput()
    {
        return $this->consoleOutput ?: $this->consoleOutput = $this->app[Kernel::class]->output();
    }
}
