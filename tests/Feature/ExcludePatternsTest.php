<?php

namespace Nikaia\TranslationSheet\Test\Feature;

use Nikaia\TranslationSheet\Commands\Prepare;
use Nikaia\TranslationSheet\Commands\Push;
use Nikaia\TranslationSheet\Commands\Setup;
use Nikaia\TranslationSheet\SheetPusher;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\FeatureTestCase;

class ExcludePatternsTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setBasePathFromFixtureFolder($app, '00-exclude');
    }

    /** @test */
    public function it_excludes_correctly_the_specified_patterns()
    {
        $this->helper->noExtraTranslationSheet();

        /** @var SheetPusher $pusher */
        $pusher = resolve(SheetPusher::class)->setTranslationsSheet(
            Spreadsheet::primaryTranslationSheet()
        );

        config()->set('translation_sheet.exclude', [
            'foo::*',
            'validation*',
        ]);
        $this->assertCount(0, $pusher->getScannedAndTransformedTranslations());

        config()->set('translation_sheet.exclude', [
            'foo::*',
        ]);
        $this->assertCount(4, $pusher->getScannedAndTransformedTranslations());
    }

    /** @test */
    public function it_excludes_correctly_the_specified_patterns_for_push()
    {
        $this->helper->noExtraTranslationSheet();

        /** @var SheetPusher $pusher */
        $pusher = resolve(SheetPusher::class)->setTranslationsSheet(
            Spreadsheet::primaryTranslationSheet()
        );
        config()->set('translation_sheet.exclude', [
            'foo::*',
        ]);
        $this->assertIsArray(json_decode($pusher->getScannedAndTransformedTranslations()->toJson()));

        $this->resetSpreadsheet();

        foreach ([
                     (new Setup),
                     (new Prepare),
                     (new Push)
                 ] as $command) {
            $this->artisan($command->getName())->assertExitCode(0);
        }
    }
}
