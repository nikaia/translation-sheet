<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Item;

class ItemTest extends TestCase
{
    /** @test */
    public function it_create_items_from_array()
    {
        $item = Item::fromArray([
            'namespace' => 'package',
            'locale' => 'fr',
            'group' => 'backend',
            'key' => 'version',
            'full_key' => 'package::backend.version',
            'value' => '1.0',
            'source_file' => 'vendor/package/fr/backend.php',
            'status' => '',
        ]);

        $this->assertEquals($item->namespace, 'package');
        $this->assertEquals($item->locale, 'fr');
        $this->assertEquals($item->group, 'backend');
        $this->assertEquals($item->key, 'version');
        $this->assertEquals($item->full_key, 'package::backend.version');
        $this->assertEquals($item->value, '1.0');
        $this->assertEquals($item->source_file, 'vendor/package/fr/backend.php');
        $this->assertEquals($item->status, '');
    }
}
