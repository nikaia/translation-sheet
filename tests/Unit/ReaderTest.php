<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Item;
use Nikaia\TranslationSheet\Translation\Reader;

class ReaderTest extends TestCase
{
    /** @test */
    public function it_know_how_to_check_vendor_directory()
    {
        $reader = $this->app[Reader::class];

        $this->assertTrue($this->helper->invokeMethod($reader, 'isVendorDirectory', ['/path/to/vendor']));
        $this->assertFalse($this->helper->invokeMethod($reader, 'isVendorDirectory', ['/path/to/a/folder']));
    }

    /** @test */
    public function it_loads_languages_from_temp_folder()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);

        $this->assertEquals(trans('app.title'), 'Awesome');
    }

    /** @test */
    public function it_reads_translations_correctly()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);
        $this->helper->createLangFiles('fr', 'app', ['title' => 'Super']);
        $this->helper->createPackageLangFiles('fr', 'backend', ['version' => '1.0']);

        $translations = $this->app[Reader::class]->scan();

        $this->assertInstanceOf(Collection::class, $translations);
        $this->assertEquals($translations->count(), 3);

        $item = $translations->last();
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals($item->namespace, 'package');
        $this->assertEquals($item->locale, 'fr');
        $this->assertEquals($item->group, 'backend');
        $this->assertEquals($item->key, 'version');
        $this->assertEquals($item->value, '1.0');
    }

    /** @test : https://github.com/nikaia/translation-sheet/pull/31 */
    public function it_scans_all_directories_even_after_encountring_vendor()
    {
        $this->helper->createLangFiles('en', 'app', ['title' => 'Awesome']);
        $this->helper->createLangFiles('fr', 'app', ['title' => 'Super']);
        $this->helper->createPackageLangFiles('fr', 'backend', ['version' => '1.0']);
        $this->helper->createLangFiles('zh-CN', 'app', ['title' => 'Super zh-CN']);

        $translations = $this->app[Reader::class]->scan();

        $this->assertInstanceOf(Collection::class, $translations);
        $this->assertEquals($translations->count(), 4);
    }
}
