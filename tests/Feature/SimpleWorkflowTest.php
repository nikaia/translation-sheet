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
use PHPUnit\Framework\Attributes\Test;

class SimpleWorkflowTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->setBasePathFromFixtureFolder($app, '00-simple');
    }

    #[Test]
    public function it_executes_simple_workflow_correctly()
    {
        $this->helper->deleteAllLangFiles();
        $this->helper->noExtraTranslationSheet();
        $this->resetSpreadsheet();

        Artisan::call(Setup::class);
        Artisan::call(Prepare::class);
        Artisan::call(Push::class);

        // Simulate editing
        $this->simulateEditingNextOnTranslationsSheet();

        Artisan::call(Pull::class);

        $lang = require __DIR__ . '/../fixtures/basepaths/00-simple/resources/lang/vendor/foo/en/messages.php';
        $this->assertEquals('Next (edited)', $lang['next']);
    }

    #[Test]
    public function it_executes_simple_workflow_correctly_with_one_extra_sheet()
    {
        $this->helper->deleteAllLangFiles();
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

    #[Test]
    public function it_executes_simple_workflow_correctly_with_two_extra_sheets()
    {
        $this->helper->deleteAllLangFiles();
        $this->helper->twoExtraTranslationSheet();
        $this->resetSpreadsheet();

        Artisan::call(Setup::class);
        Artisan::call(Prepare::class);
        Artisan::call(Push::class);

        // Simulate editing
        $this->simulateEditingNextOnTranslationsSheet();
        $this->simulateEditingTitleOnWebAppSheet();
        $this->simulateEditingConfirmOnMobileAppSheet();

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
        $this->assertEquals(
            ['confirm' => 'Are you sure? (edited)'],
            json_decode(file_get_contents($this->helper->customPath('mobile-app/lang/en/ui.json')), true)
        );
        $this->assertEquals(
            ['confirm' => 'Êtes-vous sûrs ? (edited)'],
            json_decode(file_get_contents($this->helper->customPath('mobile-app/lang/fr/ui.json')), true)
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
        resolve(Spreadsheet::class)->api()->writeCells('Web App!B2', [['This is a title. (edited)']]);
        resolve(Spreadsheet::class)->api()->writeCells('Web App!C2', [['Ceci est un titre. (edited)']]);
    }

    private function simulateEditingConfirmOnMobileAppSheet()
    {
        /* @var Spreadsheet $spreadsheet */
        resolve(Spreadsheet::class)->api()->writeCells('Mobile App!B2', [['Are you sure? (edited)']]);
        resolve(Spreadsheet::class)->api()->writeCells('Mobile App!C2', [['Êtes-vous sûrs ? (edited)']]);
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
