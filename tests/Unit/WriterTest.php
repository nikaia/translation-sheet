<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Writer;

class WriterTest extends TestCase
{
    /** @test */
    public function it_writes_correctly_translations()
    {
        $writer = $this->app[Writer::class];
        $translations = $this->helper->pulledTranslations();

        $writer->setTranslations($translations)->write();

        $this->assertFileExistsAndEqual('en/app.php', ['submit' => 'Submit', 'back' => 'Back']);
        $this->assertFileExistsAndEqual('fr/app.php', ['submit' => 'Envoyer', 'back' => 'Retour']);
        $this->assertFileExistsAndEqual('vendor/package/en/frontend.php', ['title' => 'Awesome package']);
        $this->assertFileExistsAndEqual('vendor/package/fr/frontend.php', ['title' => 'Une extension magique']);
        $this->assertJsonFileExistsAndEqual('fr.json', ['Whoops!' => 'Oups !']);
    }

    private function assertFileExistsAndEqual($file, $expected)
    {
        $filepath = $this->helper->langPath($file);

        $this->assertFileExists($filepath);
        $this->assertEquals($this->fileTranslations($filepath), $expected);
    }

    private function assertJsonFileExistsAndEqual($file, $expected)
    {
        $filepath = $this->helper->langPath($file);

        $this->assertFileExists($filepath);
        $this->assertEquals(json_decode($this->app['files']->get($filepath), true), $expected);
    }

    private function fileTranslations($file)
    {
        return $this->app['files']->getRequire($file);
    }
}
