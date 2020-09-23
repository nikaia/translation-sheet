<?php

namespace Nikaia\TranslationSheet\Test\Feature;

use Illuminate\Support\Facades\Artisan;
use Nikaia\TranslationSheet\Commands\Prepare;
use Nikaia\TranslationSheet\Commands\Pull;
use Nikaia\TranslationSheet\Commands\Push;
use Nikaia\TranslationSheet\Commands\Setup;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Test\FeatureTestCase;
use Nikaia\TranslationSheet\Util;

class SimpleWorkflowTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setBasePathFromFixtureFolder($app, '00-simple');
    }

    /** @test */
    public function it_executes_simple_workflow_correctly()
    {
        // Reset translation file
        $this->writeFooMessagesFile([
            'next' => 'Next',
            'previous' => 'Previous',
        ]);
        $this->assertEquals('Next', $this->readFooMessagesFile()['next']);

        $this->helper->oneExtraTranslationSheet();
        $this->resetSpreadsheet();


        Artisan::call(Setup::class);
        Artisan::call(Prepare::class);
        Artisan::call(Push::class);

        // Simulate editing
        $this->simulateEditingNextOnTranslationsSheet();
        $this->simulateEditingTitleOnWebAppSheet();

        Artisan::call(Pull::class);

        $lang = require __DIR__ . '/../fixtures/basepaths/00-simple/resources/lang/vendor/foo/en/messages.php';
        $this->assertEquals('Next (edited)', $lang['next']);

        $this->assertEquals(
            ['title' => 'This is a title. (edited)'],
            json_decode(file_get_contents($this->helper->customPath('web-app/lang/en/messages.json')), true)
        );
        $this->assertEquals(
            ['title' => 'Ceci est un titre. (edited)'],
            json_decode(file_get_contents($this->helper->customPath('web-app/lang/fr/messages.json')), true)
        );
    }

    private function simulateEditingNextOnTranslationsSheet()
    {
        /* @var Spreadsheet $spreadsheet */
        resolve(Spreadsheet::class)->api()->writeCells('Translations!B3', [['Next (edited)']]);
    }

    private function simulateEditingTitleOnWebAppSheet()
    {
        /* @var Spreadsheet $spreadsheet */
        resolve(Spreadsheet::class)->api()->writeCells('web-app!B2', [['This is a title. (edited)']]);
        resolve(Spreadsheet::class)->api()->writeCells('web-app!C2', [['Ceci est un titre. (edited)']]);
    }

    private function fooMessagesFile()
    {
        return __DIR__ . '/../fixtures/basepaths/00-simple/resources/lang/vendor/foo/en/messages.php';
    }

    private function readFooMessagesFile()
    {
        return require $this->fooMessagesFile();
    }

    private function writeFooMessagesFile($translations)
    {
        file_put_contents(
            $this->fooMessagesFile(),
            "<?php\n\nreturn " . Util::varExport($translations) . ";\n"
        );
    }
}
