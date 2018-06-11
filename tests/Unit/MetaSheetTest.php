<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use GuzzleHttp\Subscriber\Mock;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Sheet\Styles;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Sheet\MetaSheet;

class MetaSheetTest extends TestCase
{
    /** @test */
    public function it_returns_title_and_id()
    {
        $metaSheet = new MetaSheet(Mockery::mock(Spreadsheet::class));
        $this->assertEquals($metaSheet->getId(), 1);
        $this->assertEquals($metaSheet->getTitle(), 'Meta');
    }

    /** @test */
    public function it_setup_sheet_correctly()
    {
        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('sheetStyles')->once()->andReturn(new Styles);
        $spreadsheet->shouldReceive('addSheetRequest')->once();
        $spreadsheet->shouldReceive('api')->times(2)->andReturn($spreadsheet);
        $spreadsheet->shouldReceive('addBatchRequests')->once()->andReturn($spreadsheet);
        $spreadsheet->shouldReceive('sendBatchRequests')->once();

        (new MetaSheet($spreadsheet))->setup();
    }
}
