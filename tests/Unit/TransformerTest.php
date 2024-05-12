<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Transformer;
use PHPUnit\Framework\Attributes\Test;

class TransformerTest extends TestCase
{
    #[Test]
    public function it_transforms_read_translations_correctly()
    {
        $translations = $this->helper->readTranslations();

        $transformed = (new Transformer)->setLocales(['en', 'fr'])
            ->setTranslationsSheet(Spreadsheet::primaryTranslationSheet())
            ->transform($translations);

        $this->assertEquals([
            ['app.title', 'Awesome', 'Super', '', 'app', 'title', '{locale}/app.php'],
            ['package::backend.version', '', '1.0', 'package', 'backend', 'version', 'vendor/package/{locale}/backend.php'],
        ], $transformed->toArray());
    }
}
