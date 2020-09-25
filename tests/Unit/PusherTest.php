<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\SheetPusher;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Reader;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Transformer;

class PusherTest extends TestCase
{
    /** @test */
    public function it_pushes_translations()
    {
        $transformer = Mockery::mock(Transformer::class);
        $transformer->shouldReceive('setLocales')->once()->andReturn($transformer);
        $transformer->shouldReceive('transform')->once()->andReturn(new Collection);
        $transformer->shouldReceive('setTranslationsSheet')->once()->andReturn($transformer);

        $reader = Mockery::mock(Reader::class);
        $reader->shouldReceive('scan')->once()->andReturn(new Collection);
        $reader->shouldReceive('setTranslationsSheet')->once()->andReturn($reader);

        $translationSheet = Mockery::mock(TranslationsSheet::class);
        $translationSheet->shouldReceive('getSpreadsheet')->once()->andReturn($this->helper->spreadsheet());
        $translationSheet->shouldReceive('getTitle')->once();
        $translationSheet->shouldReceive('writeTranslations')->once();
        $translationSheet->shouldReceive('prepareForWrite')->once();
        $translationSheet->shouldReceive('updateHeaderRow')->once();
        $translationSheet->shouldReceive('styleDocument')->once();


        $pusher = (new SheetPusher($reader, $transformer))->setTranslationsSheet($translationSheet);
        $pusher->push();
    }
}
