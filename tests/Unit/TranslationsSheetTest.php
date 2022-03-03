<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use Nikaia\TranslationSheet\Client\Api;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Sheet\Styles;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Sheet\TranslationsSheetCoordinates;

class TranslationsSheetTest extends TestCase
{
    /** @test */
    public function it_returns_title_and_id()
    {
        $sheet = new TranslationsSheet(Mockery::mock(Spreadsheet::class));
        $this->assertEquals($sheet->getId(), 0);
        $this->assertEquals($sheet->getTitle(), 'Translations');
    }

    /** @test */
    public function it_returns_coordinates()
    {
        $sheet = new TranslationsSheet($this->helper->spreadsheet());
        $this->assertInstanceOf(TranslationsSheetCoordinates::class, $sheet->emptyCoordinates());
        $this->assertInstanceOf(TranslationsSheetCoordinates::class, $sheet->coordinates());
    }

    /** @test */
    public function it_setup_sheet_correctly()
    {
        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('sheetStyles')->once()->andReturn(new Styles);
        $spreadsheet->shouldReceive('setSheetPropertiesRequest')->once();
        $spreadsheet->shouldReceive('api')->times(3)->andReturn($spreadsheet);
        $spreadsheet->shouldReceive('addBatchRequests')->once()->andReturn($spreadsheet);
        $spreadsheet->shouldReceive('sendBatchRequests')->once();

        (new TranslationsSheet($spreadsheet))->setup();
    }

    /** @test */
    public function it_write_translations_correctly()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('writeCells')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('setTranslations')->once();
        $spreadsheet->shouldReceive('translationsSheetCoordinates')->once()->andReturn(TranslationsSheetCoordinates::emptySheet(1, 1, 1));
        $spreadsheet->shouldReceive('api')->times(2)->andReturn($api);

        (new TranslationsSheet($spreadsheet))->writeTranslations([]);
    }

    /** @test */
    public function it_read_translations()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('readCells')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('translationsSheetCoordinates')->once()->andReturn(TranslationsSheetCoordinates::emptySheet(1, 1, 1));
        $spreadsheet->shouldReceive('api')->times(3)->andReturn($api);

        (new TranslationsSheet($spreadsheet))->readTranslations();
    }

    /** @test */
    public function it_style_document()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('frozenColumnRequest')->once();
        $api->shouldReceive('frozenRowRequest')->once();
        $api->shouldReceive('fixedColumnWidthRequest')->times(7);
        $api->shouldReceive('styleArea')->times(4);
        $api->shouldReceive('protectRangeRequest')->times(3);
        $api->shouldReceive('deleteRowsFrom')->once();
        $api->shouldReceive('deleteColumnsFrom')->once();
        $api->shouldReceive('addBatchRequests')->twice()->andReturn($api);
        $api->shouldReceive('sendBatchRequests')->twice();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('translationsSheetCoordinates')->andReturn(TranslationsSheetCoordinates::emptySheet(1, 1, 1));
        $spreadsheet->shouldReceive('sheetStyles')->andReturn(new Styles);
        $spreadsheet->shouldReceive('getLocales')->once()->andReturn(['fr', 'en']);
        $spreadsheet->shouldReceive('api')->times(46)->andReturn($api);

        (new TranslationsSheet($spreadsheet))->styleDocument();
    }

    /** @test */
    public function it_prepare_for_write()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('getSheetProtectedRanges')->once()->andReturn([(object) ['protectedRangeId' => 111], (object) ['protectedRangeId' => 222]]);
        $api->shouldReceive('deleteProtectedRange')->times(2);
        $api->shouldReceive('addBatchRequests')->once()->andReturn($api);
        $api->shouldReceive('sendBatchRequests')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('api')->andReturn($api);

        (new TranslationsSheet($spreadsheet))->prepareForWrite();
    }

    /** @test */
    public function it_lock_translations()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('getSheetRowCount')->once();
        $api->shouldReceive('protectRangeRequest')->once();
        $api->shouldReceive('styleArea')->once();
        $api->shouldReceive('addBatchRequests')->once()->andReturn($api);
        $api->shouldReceive('sendBatchRequests')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('translationsSheetCoordinates')->andReturn(TranslationsSheetCoordinates::emptySheet(1, 1, 1));
        $spreadsheet->shouldReceive('sheetStyles')->andReturn(new Styles);
        $spreadsheet->shouldReceive('api')->andReturn($api);

        (new TranslationsSheet($spreadsheet))->lockTranslations();
    }

    /** @test */
    public function it_unlock_translations()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('getSheetRowCount')->once();
        $api->shouldReceive('getSheetProtectedRanges')->once()->andReturn([(object) ['protectedRangeId' => 111], (object) ['protectedRangeId' => 222]]);
        $api->shouldReceive('deleteProtectedRange')->times(2);
        $api->shouldReceive('styleArea')->once();
        $api->shouldReceive('addBatchRequests')->once()->andReturn($api);
        $api->shouldReceive('sendBatchRequests')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('translationsSheetCoordinates')->andReturn(TranslationsSheetCoordinates::emptySheet(1, 1, 1));
        $spreadsheet->shouldReceive('sheetStyles')->andReturn(new Styles);
        $spreadsheet->shouldReceive('api')->andReturn($api);

        (new TranslationsSheet($spreadsheet))->unlockTranslations();
    }

    /** @test */
    public function it_return_lock_status()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('getSheetProtectedRanges')->once();

        $spreadsheet = Mockery::mock(Spreadsheet::class);
        $spreadsheet->shouldReceive('api')->andReturn($api);

        (new TranslationsSheet($spreadsheet))->isTranslationsLocked();
    }
}
