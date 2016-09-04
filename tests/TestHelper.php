<?php

namespace Nikaia\TranslationSheet\Test;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Translation\Item;

class TestHelper
{
    /** @var \Illuminate\Foundation\Application */
    protected $app;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    public function __construct($app)
    {
        $this->app = $app;
        $this->files = new Filesystem();
    }

    public function langPath($path = '')
    {
        return $this->tempPath() . '/lang' . (empty($path) ? '' : '/' . $path);
    }

    public function fixturesPath($path)
    {
        return __DIR__ . '/fixtures' . (empty($path) ? '' : '/' . $path);
    }

    public function tempPath()
    {
        return __DIR__ . '/temp';
    }

    public function deleteLangFiles()
    {
        exec('rm -rf ' . $this->langPath('/*'));
    }

    public function createLangFiles($locale, $group, $translations)
    {
        $directory = $this->langPath($locale);
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }

        $content = "<?php \n return " . var_export($translations, true) . ';';

        $this->files->put($directory . '/' . $group . '.php', $content);
    }

    public function createPackageLangFiles($locale, $group, $translations)
    {
        $this->app['translator']->addNamespace('package', $this->langPath('/vendor/package'));

        $this->createLangFiles("vendor/package/$locale", $group, $translations);
    }

    public function pulledTranslations()
    {
        return new Collection($this->files->getRequire($this->fixturesPath('pulled-translations.php')));
    }

    public function readTranslations()
    {
        return new Collection(
            [
                Item::fromArray([
                    'namespace' => null,
                    'locale' => 'en',
                    'group' => 'app',
                    'key' => 'title',
                    'full_key' => 'app.title',
                    'value' => 'Awesome',
                    'source_file' => 'en/app.php',
                    'status' => null,
                ]),
                Item::fromArray([
                    'namespace' => null,
                    'locale' => 'fr',
                    'group' => 'app',
                    'key' => 'title',
                    'full_key' => 'app.title',
                    'value' => 'Super',
                    'source_file' => 'fr/app.php',
                    'status' => null,
                ]),
                Item::fromArray([
                    'namespace' => 'package',
                    'locale' => 'fr',
                    'group' => 'backend',
                    'key' => 'version',
                    'full_key' => 'package::backend.version',
                    'value' => '1.0',
                    'source_file' => 'vendor/package/fr/backend.php',
                    'status' => '',
                ]),
            ]
        );
    }

    /**
     * @return Spreadsheet
     */
    public function spreadsheet()
    {
        return (new Spreadsheet('ID', ['en', 'fr']))
            ->setTranslations($this->pulledTranslations()->toArray());
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}
