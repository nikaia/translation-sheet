<?php

namespace Nkreliefdev\TranslationSheet\Test;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Nkreliefdev\TranslationSheet\Sheet\TranslationsSheet;
use Nkreliefdev\TranslationSheet\Spreadsheet;
use Nkreliefdev\TranslationSheet\Translation\Item;

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

    public function customPath($path)
    {
        return $this->tempPath() . '/' . $path;
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

    public function deleteCustomPath($path)
    {
        exec('rm -rf ' . $this->customPath($path));
    }

    public function deleteWebAppLangFiles()
    {
        $this->deleteCustomPath('web-app/lang/');
    }

    public function deleteAllLangFiles()
    {
        $this->deleteLangFiles();
        $this->deleteWebAppLangFiles();
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

    public function createJsonLangFiles($locale, $translation)
    {
        $json = $this->langPath($locale) . '.json';

        $this->files->replace($json, json_encode($translation));
    }

    public function createCustomJsonLangFile($translation, $customPath, $file)
    {
        $directory = $this->customPath($customPath);
        $this->files->makeDirectory($directory, 0755, true, true);

        $json = "{$directory}/{$file}";

        $this->files->replace($json, json_encode($translation));
    }

    public function pulledTranslations()
    {
        return new Collection($this->files->getRequire($this->fixturesPath('pulled-translations.php')));
    }

    public function oneExtraSheetPulledTranslations()
    {
        return collect($this->files->getRequire($this->fixturesPath('pulled-webapp-extra-sheet-translations.php')));
    }

    public function twoExtraSheetPulledTranslations()
    {
        return collect($this->files->getRequire($this->fixturesPath('pulled-mobileapp-extra-sheet-translations.php')));
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
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function primaryTranslationSheet()
    {
        /** @var TranslationsSheet $instance */
        $instance = resolve(TranslationsSheet::class);

        return $instance
            ->setTitle(config('translation_sheet.primary_sheet.name', 'Translations'));
    }

    public function noExtraTranslationSheet()
    {
        config()->set('translation_sheet.extra_sheets', []);
    }

    public function oneExtraTranslationSheet()
    {
        $this->createCustomJsonLangFile(
            ['title' => 'This is a title.'],
            'web-app/lang/en/',
            'messages.json'
        );
        $this->createCustomJsonLangFile(
            ['title' => 'Ceci est un titre.'],
            'web-app/lang/fr/',
            'messages.json'
        );

        config()->set('translation_sheet.extra_sheets', [
            [
                'name' => 'Web App',
                'path' => $this->customPath('web-app/lang'),
                'tabColor' => '#0000FF'
            ]
        ]);

        return resolve(Spreadsheet::class)->configuredExtraSheets()->first();
    }

    public function twoExtraTranslationSheet()
    {
        $this->createCustomJsonLangFile(
            ['title' => 'This is a title.'],
            'web-app/lang/en/',
            'messages.json'
        );
        $this->createCustomJsonLangFile(
            ['title' => 'Ceci est un titre.'],
            'web-app/lang/fr/',
            'messages.json'
        );
        $this->createCustomJsonLangFile(
            ['confirm' => 'Are you sure?'],
            'mobile-app/lang/en/',
            'ui.json'
        );
        $this->createCustomJsonLangFile(
            ['confirm' => 'Êtes-vous sûrs ?'],
            'mobile-app/lang/fr/',
            'ui.json'
        );

        config()->set('translation_sheet.extra_sheets', [
            [
                'name' => 'Web App',
                'path' => $this->customPath('web-app/lang'),
                'tabColor' => '#0000FF'
            ],
            [
                'name' => 'Mobile App',
                'path' => $this->customPath('mobile-app/lang'),
                'tabColor' => '#00FF00'
            ]
        ]);

        return resolve(Spreadsheet::class)->configuredExtraSheets()->first();
    }
}
