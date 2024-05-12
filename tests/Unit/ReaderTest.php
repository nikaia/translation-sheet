<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Item;
use Nikaia\TranslationSheet\Translation\Reader;
use PHPUnit\Framework\Attributes\Test;

class ReaderTest extends TestCase
{
    #[Test]
    public function it_know_how_to_check_vendor_directory()
    {
        $reader = $this->app[Reader::class];

        $this->assertTrue($this->helper->invokeMethod($reader, 'isVendorDirectory', ['/path/to/vendor']));
        $this->assertFalse($this->helper->invokeMethod($reader, 'isVendorDirectory', ['/path/to/a/folder']));
    }

    #[Test]
    public function it_loads_languages_from_temp_folder()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);

        $this->assertEquals('Awesome', trans('app.title'));
    }


    #[Test]
    public function it_loads_json_languages_from_temp_folder()
    {
        $this->helper->createJsonLangFiles('en', ['title' => 'Magnifique']);

        $this->assertEquals('Magnifique', trans('title'));
    }

    #[Test]
    public function it_reads_translations_correctly()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);
        $this->helper->createLangFiles('fr', 'app', ['title' => 'Super']);
        $this->helper->createPackageLangFiles('fr', 'backend', ['version' => '1.0']);

        $translations = $this->readerFor(Spreadsheet::primaryTranslationSheet())->scan();

        $this->assertInstanceOf(Collection::class, $translations);
        $this->assertEquals(3, $translations->count());

        $item = $translations->last();
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('package', $item->namespace);
        $this->assertEquals('fr', $item->locale);
        $this->assertEquals('backend', $item->group);
        $this->assertEquals('version', $item->key);
        $this->assertEquals('1.0', $item->value);
    }

    /**
     * @see https://github.com/nikaia/translation-sheet/pull/31
     */
    #[Test]
    public function it_scans_all_directories_even_after_encountring_vendor()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);
        $this->helper->createLangFiles('fr', 'app', ['title' => 'Super']);
        $this->helper->createPackageLangFiles('fr', 'backend', ['version' => '1.0']);
        $this->helper->createLangFiles('zh-CN', 'app', ['title' => 'Super zh-CN']);

        $translations = $this->readerFor(Spreadsheet::primaryTranslationSheet())->scan();

        $this->assertInstanceOf(Collection::class, $translations);
        $this->assertEquals(4, $translations->count());
    }

    #[Test]
    public function it_scans_json_files()
    {
        $this->helper->createJsonLangFiles('fr', ['Hello!' => 'Bonjour !']);
        $this->helper->createJsonLangFiles('es', ['Hello!' => '¡Hola!']);

        $translations = $this->readerFor(Spreadsheet::primaryTranslationSheet())->scan();

        $this->assertInstanceOf(Collection::class, $translations);
        $this->assertEquals(2, $translations->count());
    }

    #[Test]
    public function it_scans_both_json_and_php_files()
    {
        $this->helper->createJsonLangFiles('fr', ['Hello!' => 'Bonjour !']);
        $this->helper->createJsonLangFiles('es', ['Hello!' => '¡Hola!']);

        $this->helper->createLangFiles('fr', 'app', ['title' => 'Super']);
        $this->helper->createLangFiles('es', 'app', ['title' => 'Asombroso']);

        $translations = $this->readerFor(Spreadsheet::primaryTranslationSheet())->scan();

        $this->assertEquals(4, $translations->count());
    }

    #[Test]
    public function it_scans_specific_extra_sheet_lang_files()
    {
        $translations = $this->readerFor($this->helper->oneExtraTranslationSheet())->scan();
        $this->assertEquals(2, $translations->count());

        $this->assertEquals(
            'Ceci est un titre.',
            $translations->filter(function (Item $item) {
                return $item->locale === 'fr';
            })->first()->value
        );

        $this->assertEquals(
            'fr/messages.json',
            $translations->filter(function (Item $item) {
                return $item->locale === 'fr';
            })->first()->source_file
        );

        $this->assertEquals(
            'en/messages.json',
            $translations->filter(function (Item $item) {
                return $item->locale === 'en';
            })->first()->source_file
        );
    }

    /**
     * @param TranslationsSheet $translationsSheet
     * @return Reader
     */
    private function readerFor(TranslationsSheet $translationsSheet)
    {
        return $this
            ->app[Reader::class]
            ->setTranslationsSheet($translationsSheet);
    }
}
