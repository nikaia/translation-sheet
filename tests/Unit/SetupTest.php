<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use Nikaia\TranslationSheet\Setup;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

class SetupTest extends TestCase
{
    /** @test */
    public function it_setup_the_spreadsheet()
    {
        $translationSheet = Mockery::mock(TranslationsSheet::class);
        $translationSheet->shouldReceive('setup')->once();

        $setup = new Setup($translationSheet);
        $setup->run();
    }
}
