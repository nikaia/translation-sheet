<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use GuzzleHttp\Subscriber\Mock;
use Nikaia\TranslationSheet\Puller;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Writer;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;

class PullerTest extends TestCase
{
    /** @test */
    public function it_pulls_the_translations()
    {
        $translationSheet = Mockery::mock(TranslationsSheet::class);
        $translationSheet->shouldReceive('getSpreadsheet')->once()->andReturn($this->helper->spreadsheet());
        $translationSheet->shouldReceive('readTranslations')->once();

        $writer = Mockery::mock(Writer::class);
        $writer->shouldReceive('withOutput')->once()->andReturn($writer);
        $writer->shouldReceive('setTranslations')->once()->andReturn($writer);
        $writer->shouldReceive('write');

        $puller = new Puller($translationSheet, $writer);
        $puller->pull();
    }
}
