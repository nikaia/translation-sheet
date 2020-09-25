<?php

namespace Nikaia\TranslationSheet\Test\Unit;

use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\TestCase;
use Nikaia\TranslationSheet\Translation\Writer;

class WriterTest extends TestCase
{
    /** @test */
    public function it_writes_correctly_translations()
    {
        $translations = $this->helper->pulledTranslations();

        $writer = $this->writerFor(Spreadsheet::primaryTranslationSheet());
        $writer->setTranslations($translations)->write();

        $this->assertFileExistsAndEqual('en/app.php', ['submit' => 'Submit', 'back' => 'Back']);
        $this->assertFileExistsAndEqual('fr/app.php', ['submit' => 'Envoyer', 'back' => 'Retour']);
        $this->assertFileExistsAndEqual('vendor/package/en/frontend.php', ['title' => 'Awesome package']);
        $this->assertFileExistsAndEqual('vendor/package/fr/frontend.php', ['title' => 'Une extension magique']);
        $this->assertJsonFileExistsAndEqual('fr.json', ['Whoops!' => 'Oups !']);
    }

    /** @test */
    public function it_writes_correctly_extra_sheet_translations()
    {
        $translations = $this->helper->oneExtraSheetPulledTranslations();
        $writer = $this->writerFor($this->helper->oneExtraTranslationSheet());
        $this->helper->deleteCustomPath('web-app/lang/');

        $writer->setTranslations($translations)->write();

        $this->assertFileExists($this->helper->customPath('web-app/lang/fr/messages.json'));
        $this->assertEquals(
            ['title' => 'Ceci est un titre mis à jour.'],
            json_decode(file_get_contents($this->helper->customPath('web-app/lang/fr/messages.json')), true)
        );

        $this->assertFileExists($this->helper->customPath('web-app/lang/en/messages.json'));
        $this->assertEquals(
            ['title' => 'This is an updated title.'],
            json_decode(file_get_contents($this->helper->customPath('web-app/lang/en/messages.json')), true)
        );

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

    /**
     * @param TranslationsSheet $translationsSheet
     * @return Writer
     */
    private function writerFor(TranslationsSheet $translationsSheet)
    {
        return $this
            ->app[Writer::class]
            ->setTranslationsSheet($translationsSheet);
    }
}
