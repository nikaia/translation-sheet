<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use Nikaia\TranslationSheet\SheetPuller;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Writer;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use PHPUnit\Framework\Attributes\Test;

class PullerTest extends TestCase
{
    #[Test]
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
