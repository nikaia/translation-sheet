<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use GuzzleHttp\Subscriber\Mock;
use Nikaia\TranslationSheet\Puller;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Test\TestCase;
use Mockery;
use Nikaia\TranslationSheet\Translation\Writer;

class PullerTest extends TestCase
{

    /** @test */
    function it_pulls_the_translations()
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
