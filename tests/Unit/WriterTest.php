<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Writer;

class WriterTest extends TestCase
{
    private $langPath;

    /** @test */
    public function it_writes_correctly_translations()
    {
        $writer = $this->app[Writer::class];
        $translations = $this->helper->pulledTranslations();

        $writer->setTranslations($translations)->write();

        $this->assertFileExistsAndEqual('en/app.php', ['submit' => 'Submit', 'back' => 'Back']);
        $this->assertFileExistsAndEqual('fr/app.php', ['submit' => 'Envoyer', 'back' => 'Retour']);
        $this->assertFileExistsAndEqual('/vendor/package/en/frontend.php', ['title' => 'Awesome package']);
        $this->assertFileExistsAndEqual('/vendor/package/fr/frontend.php', ['title' => 'Une extension magique']);
    }

    private function assertFileExistsAndEqual($file, $expected)
    {
        $filepath = $this->app['path.lang'].'/'.$file;

        $this->assertFileExists($this->langPath.'/'.$filepath);
        $this->assertEquals($this->fileTranslations($filepath), $expected);
    }

    private function fileTranslations($file)
    {
        return $this->app['files']->getRequire($file);
    }
}
