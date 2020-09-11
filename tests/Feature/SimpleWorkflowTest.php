<?php

namespace Nikaia\TranslationSheet\Test\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Translation\Translator;
use Nikaia\TranslationSheet\Commands\Setup;
use Nikaia\TranslationSheet\Commands\Prepare;
use Nikaia\TranslationSheet\Commands\Push;
use Nikaia\TranslationSheet\Commands\Pull;
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
            "next" => "Next",
            "previous" => "Previous"
        ]);
        $this->assertEquals('Next', $this->readFooMessagesFile()['next']);

        $this->resetSpreadsheet();

        Artisan::call(Setup::class);
        Artisan::call(Prepare::class);
        Artisan::call(Push::class);

        // Simulate editing
        $this->simulateEditingFooMessagesNext();

        Artisan::call(Pull::class);

        $lang = require __DIR__ . '/../fixtures/basepaths/00-simple/resources/lang/vendor/foo/en/messages.php';
        $this->assertEquals('Next (edited)', $lang['next']);
    }

    private function simulateEditingFooMessagesNext()
    {
        /** @var Spreadsheet $spreadsheet */
        resolve(Spreadsheet::class)->api()->writeCells('Translations!B2', [['Next (edited)']]);
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
