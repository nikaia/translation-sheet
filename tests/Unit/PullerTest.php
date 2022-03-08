<?php

namespace Nkreliefdev\TranslationSheet\Test\Unit;

use Mockery;
use GuzzleHttp\Subscriber\Mock;
use Nkreliefdev\TranslationSheet\SheetPuller;
use Nkreliefdev\TranslationSheet\Test\TestCase;
use Nkreliefdev\TranslationSheet\Translation\Writer;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;

class PullerTest extends TestCase
{
    /** @test */
    public function it_pulls_the_translations()
    {
        $translationSheet = Mockery::mock(TranslationsSheet::class);
        $translationSheet->shouldReceive('getSpreadsheet')->once()->andReturn($this->helper->spreadsheet());
        $translationSheet->shouldReceive('getTitle')->once();
        $translationSheet->shouldReceive('readTranslations')->once();

        $writer = Mockery::mock(Writer::class);
        $writer->shouldReceive('withOutput')->once()->andReturn($writer);
        $writer->shouldReceive('setTranslations')->once()->andReturn($writer);
        $writer->shouldReceive('setTranslationsSheet')->once()->andReturn($writer);
        $writer->shouldReceive('write');

        $puller = (new SheetPuller($writer))->setTranslationsSheet($translationSheet);
        $puller->pull();
    }
}
