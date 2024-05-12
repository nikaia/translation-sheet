<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Mockery;
use Nikaia\TranslationSheet\Client\Api;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Sheet\Styles;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Sheet\TranslationsSheetCoordinates;
use PHPUnit\Framework\Attributes\Test;

class SpreadsheetTest extends TestCase
{
    /** @var Spreadsheet */
    private $s;

    public function setUp(): void
    {
        parent::setUp();

        $this->s = $this->helper->spreadsheet();
    }

    #[Test]
    public function it_returns_correct_id()
    {
        $this->assertEquals('ID', $this->s->getId());
    }

    #[Test]
    public function it_return_correct_locales_count()
    {
        $this->assertEquals(2, $this->s->getLocalesCount());
    }

    #[Test]
    public function it_sets_locales()
    {
        $locales = ['en', 'fr', 'ar'];
        $this->s->setLocales($locales);
        $this->assertEquals($this->s->getLocales(), $locales);
    }

    #[Test]
    public function is_sets_translations()
    {
        $translations = ['t1', 't2'];
        $this->s->setTranslations($translations);
        $this->assertEquals($this->s->getTranslations(), $translations);
    }

    #[Test]
    public function is_returns_translations_count()
    {
        $translations = ['t1', 't2', 't3', 't4'];
        $this->s->setTranslations($translations);
        $this->assertEquals(4, $this->s->getTranslationsCount());
    }

    #[Test]
    public function it_return_corrected_header()
    {
        $this->assertEquals(
            ['Full key', 'en', 'fr', 'Namespace', 'Group', 'Key', 'Source file'],
            $this->s->getHeader()
        );

        $this->assertEquals(
            ['Full key', 'en', 'fr', 'ar', 'Namespace', 'Group', 'Key', 'Source file'],
            $this->s->setLocales(['en', 'fr', 'ar'])->getHeader()
        );
    }

    #[Test]
    public function it_return_corrected_header_columns_count()
    {
        $this->assertEquals(7, $this->s->getHeaderColumnsCount());
        $this->assertEquals(8, $this->s->setLocales(['en', 'fr', 'ar'])->getHeaderColumnsCount());
    }

    #[Test]
    public function it_return_corrected_camelized_header()
    {
        $this->assertEquals(
            ['fullKey', 'en', 'fr', 'namespace', 'group', 'key', 'sourceFile'],
            $this->s->getCamelizedHeader()
        );
    }

    #[Test]
    public function it_returns_correct_header_count()
    {
        $this->assertEquals(1, $this->s->getHeaderRowsCount());
    }

    #[Test]
    public function it_return_styles()
    {
        $this->assertInstanceOf(Styles::class, $this->s->sheetStyles());
    }

    #[Test]
    public function it_returns_empty_sheet_coordinates()
    {
        $coordinates = $this->s->translationsEmptySheetCoordinates(0, 'SHEET_TITLE');
        $this->assertInstanceOf(TranslationsSheetCoordinates::class, $coordinates);
    }

    #[Test]
    public function it_returns_sheet_coordinates()
    {
        $coordinates = $this->s->translationsSheetCoordinates(0, 'SHEET_TITLE');
        $this->assertInstanceOf(TranslationsSheetCoordinates::class, $coordinates);
    }

    #[Test]
    public function it_returns_api_set_with_spreadsheet_id()
    {
        $api = Mockery::mock(Api::class);
        $api->shouldReceive('setSpreadsheetId')->with($this->s->getId())->once();

        $s = new Spreadsheet('ID', ['en', 'fr'], $api);
        $s->api();
    }

    #[Test]
    public function it_returns_spreadsheet_url()
    {
        $this->assertEquals($this->s->getUrl(), 'https://docs.google.com/spreadsheets/d/' . $this->s->getId());
    }
}
