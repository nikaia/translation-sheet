<?php

namespace Nkreliefdev\TranslationSheet\Test\Feature;

use Nkreliefdev\TranslationSheet\Commands\Prepare;
use Nkreliefdev\TranslationSheet\Commands\Push;
use Nkreliefdev\TranslationSheet\Commands\Setup;
use Nkreliefdev\TranslationSheet\SheetPusher;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\Spreadsheet;
use Nkreliefdev\TranslationSheet\Test\FeatureTestCase;

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
