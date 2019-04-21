<?php

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
            'validation*'
        ]);
        $this->assertCount(0, $pusher->getScannedAndTransformedTranslations());


        config()->set('translation_sheet.exclude', [
            'foo::*',
        ]);
        $this->assertCount(4, $pusher->getScannedAndTransformedTranslations());
    }
}