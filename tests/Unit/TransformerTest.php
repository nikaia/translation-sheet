<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Transformer;

class TransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_read_translations_correctly()
    {
        $translations = $this->helper->readTranslations();

        $transformed = (new Transformer)->setLocales(['en', 'fr'])->transform($translations);

        $this->assertEquals($transformed->toArray(), [
            ['app.title', 'Awesome', 'Super', '', 'app', 'title', '{locale}/app.php'],
            ['package::backend.version', '', '1.0', 'package', 'backend', 'version', 'vendor/package/{locale}/backend.php'],
        ]);
    }
}
