<?php

namespace Nikaia\TranslationSheet\Test;

use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\FixtureClasses\FooServiceProvider;
use Nikaia\TranslationSheet\TranslationSheetServiceProvider;

abstract class FeatureTestCase extends \Orchestra\Testbench\TestCase
{
    /** @var TestHelper */
    protected $helper;


    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new TestHelper($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            TranslationSheetServiceProvider::class,

            // Foo package for testing package translations (aka files in resources/lang/vendor)
            FooServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set(
            'translation_sheet.serviceAccountCredentialsFile',
            realpath($this->basePath() . '/' . $app['config']->get('translation_sheet.serviceAccountCredentialsFile'))
        );
    }

    protected function basePath()
    {
        return realpath(__DIR__ . '/..');
    }

    protected function resetSpreadsheet()
    {
        /** @var Spreadsheet $spreadsheet */
        $spreadsheet = resolve(Spreadsheet::class);

        $spreadsheet->deleteAllSheets();
    }

    protected function setBasePathFromFixtureFolder($app, $folder)
    {
        $app->setBasePath($this->basePath() . '/tests/fixtures/basepaths/' . $folder);
    }
}
