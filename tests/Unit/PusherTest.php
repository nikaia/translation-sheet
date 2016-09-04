<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Pusher;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Test\TestCase;
use Mockery;
use Nikaia\TranslationSheet\Translation\Reader;
use Nikaia\TranslationSheet\Translation\Transformer;
use Illuminate\Support\Collection;

class PusherTest extends TestCase
{
    /** @test */
    function it_pushes_translations()
    {
        $transformer = Mockery::mock(Transformer::class);
        $transformer->shouldReceive('setLocales')->once()->andReturn($transformer);
        $transformer->shouldReceive('transform')->once()->andReturn(new Collection);

        $reader = Mockery::mock(Reader::class);
        $reader->shouldReceive('scan')->once()->andReturn(new Collection);

        $translationSheet = Mockery::mock(TranslationsSheet::class);
        $translationSheet->shouldReceive('getSpreadsheet')->once()->andReturn($this->helper->spreadsheet());
        $translationSheet->shouldReceive('writeTranslations')->once();
        $translationSheet->shouldReceive('prepareForWrite')->once();
        $translationSheet->shouldReceive('styleDocument')->once();

        $pusher = new Pusher($reader, $translationSheet, $transformer);
        $pusher->push();
    }

}
