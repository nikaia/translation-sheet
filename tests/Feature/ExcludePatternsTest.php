<?php

use Nikaia\TranslationSheet\Commands\Prepare;
use Nikaia\TranslationSheet\Commands\Push;
use Nikaia\TranslationSheet\Commands\Setup;
use Nikaia\TranslationSheet\Pusher;
use Nikaia\TranslationSheet\Test\FeatureTestCase;

class ExcludeFilterTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setBasePathFromFixtureFolder($app, '00-exclude');
    }

    /** @test */
    public function it_exludes_correctly_the_specified_patterns()
    {
        /** @var Pusher $pusher */
        $pusher = resolve(Pusher::class);

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
    public function it_exludes_correctly_the_specified_patterns_for_push()
    {
        /** @var Pusher $pusher */
        $pusher = resolve(Pusher::class);
        config()->set('translation_sheet.exclude', [
            'foo::*',
        ]);
        $this->assertIsArray(json_decode($pusher->getScannedAndTransformedTranslations()->toJson()));

        $this->resetSpreadsheet();

        foreach ([new Setup(), new Prepare(), new Push()] as $command) {
            $this->artisan($command->getName())->assertExitCode(0);
        }
    }
}
