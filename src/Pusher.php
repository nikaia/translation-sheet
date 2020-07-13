<?php

namespace Nikaia\TranslationSheet;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Nikaia\TranslationSheet\Commands\Output;
use Nikaia\TranslationSheet\Sheet\TranslationsSheet;
use Nikaia\TranslationSheet\Translation\Reader;
use Nikaia\TranslationSheet\Translation\Transformer;

class Pusher
{
    use Output;

    /** @var Reader */
    protected $reader;

    /** @var TranslationsSheet */
    protected $translationsSheet;

    /** @var Transformer */
    protected $transformer;

    public function __construct(Reader $reader, TranslationsSheet $translationsSheet, Transformer $transformer)
    {
        $this->reader = $reader;
        $this->translationsSheet = $translationsSheet;
        $this->transformer = $transformer;

        $this->nullOutput();
    }

    public function push()
    {
        $this->pushTab(config('translation_sheet.default_tab_name', 'Translations'));

        foreach (config('translation_sheet.additional_tabs') as $tab) {
            $this->pushTab($tab['name'], $tab['path']);
        }
    }

    public function pushTab($title, $path = null)
    {
        $this->output->writeln('<comment>Scanning '. $title .' languages files</comment>');
        $translations = $this->getScannedAndTransformedTranslations($path);

        $this->output->writeln('<comment>Preparing spreadsheet for new write operation</comment>');
        $this->translationsSheet
            ->setTitle($title)
            ->prepareForWrite();

        $this->output->writeln('<comment>Updating header</comment>');
        $this->translationsSheet->updateHeaderRow();

        $this->output->writeln('<comment>Writing translations in the spreadsheet</comment>');
        $this->translationsSheet->writeTranslations($translations->toArray());

        $this->output->writeln('<comment>Styling document</comment>');
        $this->translationsSheet->styleDocument();

        $this->output->writeln('<info>Done '. $title .'</info>.');
    }

    public function getScannedAndTransformedTranslations($path = null)
    {
        $excludePatterns = config('translation_sheet.exclude');

        return $this->transformer
            ->setLocales($this->translationsSheet->getSpreadsheet()->getLocales())
            ->transform($this->reader->scan($path))
            ->when(is_array($excludePatterns) && !empty($excludePatterns), static function (Collection $collection) use ($excludePatterns) {
                return $collection->reject(function ($item) use ($excludePatterns) {
                    return Str::is($excludePatterns, $item[0] /* full key */);
                });
            });
    }
}
