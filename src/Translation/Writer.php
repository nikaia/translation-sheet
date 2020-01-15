<?php

namespace Nikaia\TranslationSheet\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Spreadsheet;
use Nikaia\TranslationSheet\Util;

class Writer
{
    use Output;

    /** @var Collection */
    protected $translations;

    /** @var Spreadsheet */
    protected $spreadsheet;

    /** @var Filesystem */
    protected $files;

    /** @var Application */
    protected $app;

    public function __construct(Spreadsheet $spreadsheet, Filesystem $files, Application $app)
    {
        $this->spreadsheet = $spreadsheet;
        $this->files = $files;
        $this->app = $app;

        $this->nullOutput();
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    public function write()
    {
        $this
            ->groupTranslationsByFile()
            ->map(function ($items, $sourceFile) {
                if (Str::endsWith($sourceFile, ['.json'])) {
                    $this->writeJsonFile($this->app->make('path.lang').'/'.$sourceFile, $items);
                    return;
                }
                $this->writeFile(
                    $this->app->make('path.lang').'/'.$sourceFile,
                    $items
                );
            });
    }

    protected function writeJsonFile($file, $items)
    {
        $this->output->writeln('  JSON: '.$file);

        if (!$this->files->isDirectory($dir = dirname($file))) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($file, json_encode($items, JSON_PRETTY_PRINT));
    }


    protected function writeFile($file, $items)
    {
        $this->output->writeln('  '.$file);

        $content = "<?php\n\nreturn ".Util::varExport($items).";\n";

        if (!$this->files->isDirectory($dir = dirname($file))) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($file, $content);
    }

    protected function groupTranslationsByFile()
    {
        $items = $this
            ->translations
            ->groupBy('sourceFile')
            ->map(function ($fileTranslations, $source) {
                if(Str::endsWith($source, ['.json'])) {
                    return $this->buildTranslationsForJsonFile($fileTranslations);
                }
                return $this->buildTranslationsForFile($fileTranslations);
            });

        // flatten does not seem to work for every case. !!! refactor !!!
        $result = [];
        foreach ($items as $subitems) {
            $result = array_merge($result, $subitems);
        }

        return new Collection($result);
    }

    protected function buildTranslationsForFile($fileTranslations)
    {
        $files = [];
        $locales = $this->spreadsheet->getLocales();

        $locales = array_filter($locales, static function($locale) {
            return ! in_array($locale, config('translation_sheet.exclude_from_pull'));
        });

        foreach ($locales as $locale) {
            foreach ($fileTranslations as $translation) {

                // We will only write non empty translations
                // For instance, we have `app.title` that is the same for each locale,
                // We dont want to translate it to every locale, and prefer letting
                // Laravel default back to the default locale.
                if (!isset($translation[$locale])) {
                    continue;
                }

                $localeFile = str_replace('{locale}/', $locale.'/', $translation['sourceFile']);
                if (empty($files[$localeFile])) {
                    $files[$localeFile] = [];
                }

                Arr::set($files[$localeFile], $translation['key'], $translation[$locale]);
            }
        }

        return $files;
    }

    protected function buildTranslationsForJsonFile($fileTranslations)
    {
        $files = [];
        $locales = $this->spreadsheet->getLocales();

        foreach ($locales as $locale) {
            foreach ($fileTranslations as $translation) {

                // We will only write non empty translations
                // For instance, we have `app.title` that is the same for each locale,
                // We dont want to translate it to every locale, and prefer letting
                // Laravel default back to the default locale.
                if (!isset($translation[$locale])) {
                    continue;
                }

                $localeFile = str_replace('{locale}.json', $locale.'.json', $translation['sourceFile']);
                if (!isset($files[$localeFile])) {
                    $files[$localeFile] = [];
                }


                $files[$localeFile][$translation['key']] = $translation[$locale];
            }
        }

        return $files;
    }
}
