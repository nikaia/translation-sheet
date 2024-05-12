<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Item;
use PHPUnit\Framework\Attributes\Test;

class ItemTest extends TestCase
{
    #[Test]
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

        $this->assertEquals('package', $item->namespace);
        $this->assertEquals('fr', $item->locale);
        $this->assertEquals('backend', $item->group);
        $this->assertEquals('version', $item->key);
        $this->assertEquals('package::backend.version', $item->full_key);
        $this->assertEquals('1.0', $item->value);
        $this->assertEquals('vendor/package/fr/backend.php', $item->source_file);
        $this->assertEquals('', $item->status);
    }
}
